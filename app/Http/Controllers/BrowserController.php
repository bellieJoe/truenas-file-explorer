<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;


class BrowserController extends Controller    
{
    // routes
    public function index(){
        // get folders and files
        $directories = [];
        $files = [];

        foreach(Storage::directories() as $dir){
            array_push($directories, [
                'name' => $this->simplifyName($dir),
                'dir' => $dir
            ]);
        }
        foreach(Storage::files() as $file){
            array_push($files, [
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
            'breadcrumbs' => []
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
                'name' => $this->simplifyName($dir),
                'dir' => $dir
            ]);
        }
        $files = [];
        foreach(Storage::files($request->path) as $file){
            array_push($files, [
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
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    public function download($file){
        return Storage::download($file);
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