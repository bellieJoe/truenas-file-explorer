<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Pion\Laravel\ChunkUpload\Exceptions\UploadFailedException;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;


class BrowserController extends Controller    
{
    public $uploadable = 'pdf,doc,docx,xlsx,jpg,jpeg,png,gif';
    public $max_upload_size = 20 * 1024 * 1024; //20GB

    // routes
    public function index(){
        // get folders and files
        config([
            'filesystems.disks.ftp.username' => session()->get('username'),
            'filesystems.disks.ftp.password' => session()->get('password')
        ]);
        $directories = [];
        $files = [];

        foreach(Storage::directories() as $dir){
            array_push($directories, [
                'name' => $this->simplifyName($dir),
                'dir' => $dir,
                'type' => 'directory'
            ]);
        }
        // return 'oke';
        foreach(Storage::files() as $file){
            array_push($files, [
                'type' => 'file',
                'name' => $this->simplifyName($file),
                'file' => $file,
                'url' => Storage::url($file),
                // 'size' => 200,
                'size' => Storage::size($file),
                'lastModified' => Carbon::createFromTimestamp(Storage::lastModified($file))->toFormattedDateString()
            ]);
        }
        // return $files;
        return view('browser')->with([
            'directories' => $directories,
            'files' => $files,
            'breadcrumbs' => [],
            'path' => ''
        ]);
    }

    /* 
        Request params
        path string, breadcrumbs json
    */
    public function openFolder(Request $request){
        config([
            'filesystems.disks.ftp.username' => session()->get('username'),
            'filesystems.disks.ftp.password' => session()->get('password')
        ]);
        $breadcrumbs = explode('/', $request->path);
        $directories = [];
        foreach(Storage::directories($request->path) as $dir){
            array_push($directories, [
                'type' => 'directory',
                'name' => $this->simplifyName($dir),
                'dir' => $dir
            ]);
        }
        $files = [];
        // return $directories;
        foreach(Storage::files($request->path) as $file){
            array_push($files, [
                'type' => 'file',
                'name' => $this->simplifyName($file),
                'file' => $file,
                'url' => $file,
                // 'size' => 200,
                'size' => Storage::size($file),
                'lastModified' => Carbon::createFromTimestamp(Storage::lastModified($file))->toFormattedDateString()
            ]);
        }
        return view('browser')->with([
            'directories' => $directories,
            'files' => $files,
            'breadcrumbs' => $breadcrumbs,
            'path' => $request->path,
        ]);
    
    }

    /* 
        Reqeuest params
        fileDir
    */
    public function download(Request $request){
        config([
            'filesystems.disks.ftp.username' => session()->get('username'),
            'filesystems.disks.ftp.password' => session()->get('password')
        ]);
        if (! Storage::exists($request->fileDir)) {
            abort(404);
        }

        $filename = $request->fileDir;
        return redirect('http://'. env('PC_HOST').':3000/download?file=' 
        . $request->fileDir . '&mimetype=' 
        . Storage::mimeType($request->fileDir) 
        . '&host=' . env('FTP_HOST')
        . '&username=' . session()->get('username')
        . '&password=' . session()->get('password'));
        // return StreamedResponse::create(function () use ($filename) {
        //     $stream = Storage::readStream($filename);
    
        //     fpassthru($stream);
        // }, 200, [
        //     'Content-Type' => Storage::mimeType($filename),
        //     'Content-Disposition' => 'attachment; filename="' . basename($filename) . '"',
        // ]);

    
        // $filePath = Storage::path($request->fileDir);
        // return response()->streamDownload(function () use ($filePath) {
        //     readfile($filePath);
        // }, $this->simplifyName($request->fileDir));
        
        /*     
            // Get the file path
            $filePath = Storage::path($request->fileDir);

            // Create a new response object
            $response = new Response();

            // Set the headers for the response
            $response->headers->set("Content-Type", "application/octet-stream");
            $response->headers->set("Content-Disposition", "attachment; filename=" . $request->fileDir);

            // Open the file in read-only mode
            $stream = fopen($filePath, "r");

            // Set the chunk size to 2 MB
            $chunkSize = 2 * 1024 * 1024;

            // Loop through the chunks
            while (! feof($stream)) {
                // Read the chunk from the stream
                $chunk = fread($stream, $chunkSize);

                echo $chunk;
                // Send the chunk to the browser
                // $response->sendContent($chunk);
            }

            // Close the stream
            fclose($stream);

            // Return the response 
        */
        // return $response;
    }

    /* 
        Request params
        breadcrumbs json
        index int
    */
    public function navigateTo(Request $request){
        config([
            'filesystems.disks.ftp.username' => session()->get('username'),
            'filesystems.disks.ftp.password' => session()->get('password')
        ]);
        $path = '';
        foreach(json_decode($request->breadcrumbs) as $index => $breadcrumb){
            if($index <= $request->index){
                $path = $index == 0 ? $breadcrumb : $path.'/'.$breadcrumb;
            }
        }
        return redirect()->route('browse', ['path' => $path]);
    }

    /* 
        Request params
        $path string
        $name string
    */
    public function makeDirectory(Request $request){
        config([
            'filesystems.disks.ftp.username' => session()->get('username'),
            'filesystems.disks.ftp.password' => session()->get('password')
        ]);
        $request->validate([
            'name' => 'required'
        ]);
        // saka na to
        // if(in_array($request->name, Storage::directories($request->path))){
        //     return response([
        //         'status' => 'failed',
        //         'message' => 'Folder '.$request->name.' already exist.'
        //     ], 419);
        // }
        Storage::makeDirectory($request->path == '' ? $request->name : $request->path.'/'.$request->name);
        return \back();
    }

    /* 
        Request params
        $path string
        $name string
    */
    public function upload(Request $request)
    {
        config([
            'filesystems.disks.ftp.username' => session()->get('username'),
            'filesystems.disks.ftp.password' => session()->get('password')
        ]);
        $validation = Validator::make(
            $request->all(),
            [
                'file' => 'required|file|mimes:'.$this->uploadable.'|max:'.$this->max_upload_size,
                'name' => 'required'
            ],
            []
        );

        if($validation->fails()){
            return redirect()->back()->withErrors( $validation->errors(), 'upload_form');
        }

        $request->file->storeAs($request->path, $request->name);

        return back();
    }

    /* 
    Req params
    dirs array
    */
    public function deleteMany(Request $request){
        config([
            'filesystems.disks.ftp.username' => session()->get('username'),
            'filesystems.disks.ftp.password' => session()->get('password')
        ]);
        $files = [];
        // $directories = [];
        foreach($request->dirs as $dir){
            if($dir['type'] == 'directory'){
                Storage::deleteDirectory($dir['dir']);
            }
            else {
                array_push($files, $dir['file']);
            }
            Storage::delete($files);
        }
    }

    public function login(){
        return view('login');
    }

    public function logout(){
        session()->flush();
        return redirect()->route('login');
    }

    public function signin(Request $request){
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);
        session([
            'username' => $request->username,
            'password' => $request->password
        ]);
        return \redirect('/');
    }
    
    /* 
        Request params
        path string
        breadcrumds json
    */
    public function chunkUpload(Request $request)
    {
        return view('chunk-upload')->with([
            'path' => $request->path,
            'breadcrumbs' => json_decode($request->breadcrumbs)
        ]);
    }

    public function uploadLargeFiles(Request $request) {
        // return $request;
       $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));
    
        if (!$receiver->isUploaded()) {
            // file not uploaded
        }
    
        $fileReceived = $receiver->receive(); // receive file
        if ($fileReceived->isFinished()) { // file uploading is complete / all chunks are uploaded
            $file = $fileReceived->getFile(); // get file
            $extension = $file->getClientOriginalExtension();
            $fileName = str_replace('.'.$extension, '', $file->getClientOriginalName()); //file name without extenstion

            $fileName .= '_' . time() . '.' . $extension; // a unique file name
    
            $disk = Storage::disk(config('filesystems.default'));
            $path = $disk->putFileAs($request->path, $file, $fileName);
    
            // delete chunked file
            unlink($file->getPathname());
            return [
                'path' => asset('storage/' . $path),
                'filename' => $fileName
            ];
        }
    
        // otherwise return percentage information
        $handler = $fileReceived->handler();
        return [
            'done' => $handler->getPercentageDone(),
            'status' => true
        ]; 
    }
    



    // helpers
    public static function simplifyName($name){
        $slashIndex = 0;
        for ($i = 0; $i < strlen($name); $i++) {
            if($name[$i] == '/'){
                $slashIndex = $i;
            }
        }
        if($slashIndex > 0){
            return substr($name, $slashIndex + 1);
        }
        return $name;
    }
}