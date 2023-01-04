<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;


class BrowserController extends Controller    
{
    //
    public function index(){
        // get folders and files
        $directories = Storage::directories();
        $files = [];

        foreach(Storage::files() as $file){
            array_push($files, [
                'file' => $file,
                'url' => Storage::url($file),
                'size' => Storage::size($file),
                'lastModified' => Carbon::createFromTimestamp(Storage::lastModified($file))->toFormattedDateString()
            ]);
        }
  
        return view('browser')->with([
            'directories' => $directories,
            'files' => $files,
            'breadcrumbs' => [
                'home'
            ]
        ]);
    }

    public function openFolder($name, Request $request){
        $breadcrumbs = json_decode($request->breadcrumbs);
        $directory = '';
        foreach($breadcrumbs as $i => $breadcrumb){
            if($i == 0){
                continue;
            }
            $directory = $directory.'/'.$breadcrumb;
        }
        $directory = $directory.'/'.$name;
        $directories = Storage::directories($directory);
        $files = [];
        foreach(Storage::files($directory) as $file){
            // return str_replace($directory, "", $file);
            array_push($files, [
                'file' => str_replace($directory, " ", $file),
                'url' => $file,
                'size' => Storage::size($file),
                'lastModified' => Carbon::createFromTimestamp(Storage::lastModified($file))->toFormattedDateString()
            ]);
        }
        array_push($breadcrumbs, $name);
        return view('browser')->with([
            'directories' => $directories,
            'files' => $files,
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    public function download($file){
        // Storage::download('D://Projects/file-manager/storage/app/Bellie Joe jandusay Sept 16 - 30.xlsx', 'ok');
        return Storage::download($file);
        // Storage::download('public/.gitignore', 'ok');
        return $file;
    }
}