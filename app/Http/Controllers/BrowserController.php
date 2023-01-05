<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;


class BrowserController extends Controller    
{
    public $uploadable = 'pdf,doc,docx,xlsx,jpg,jpeg,png,gif';
    public $max_upload_size = 20 * 1024 * 1024; //20GB

    // routes
    public function index(){
        // get folders and files
        $directories = [];
        $files = [];

        foreach(Storage::directories() as $dir){
            array_push($directories, [
                'name' => $this->simplifyName($dir),
                'dir' => $dir,
                'type' => 'directory'
            ]);
        }
        foreach(Storage::files() as $file){
            array_push($files, [
                'type' => 'file',
                'name' => $this->simplifyName($file),
                'file' => $file,
                'url' => Storage::url($file),
                'size' => Storage::size($file),
                'lastModified' => Carbon::createFromTimestamp(Storage::lastModified($file))->toFormattedDateString()
            ]);
        }
  
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
        foreach(Storage::files($request->path) as $file){
            array_push($files, [
                'type' => 'file',
                'name' => $this->simplifyName($file),
                'file' => $file,
                'url' => $file,
                'size' => Storage::size($file),
                'lastModified' => Carbon::createFromTimestamp(Storage::lastModified($file))->toFormattedDateString()
            ]);
        }
        return view('browser')->with([
            'directories' => $directories,
            'files' => $files,
            'breadcrumbs' => $breadcrumbs,
            'path' => $request->path
        ]);
    }

    /* 
        Reqeuest params
        fileDir
    */
    public function download(Request $request){
        return Storage::download($request->fileDir);
    }

    /* 
        Request params
        breadcrumbs json
        index int
    */
    public function navigateTo(Request $request){
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