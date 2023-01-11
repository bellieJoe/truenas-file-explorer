@extends('index')
@section('content')

{{-- browser --}}
<div class="container-lg py-4">
    <div class="btn-group btn-group-sm mb-3" style="width:fit-content">
        <button class="btn btn-outline-dark " data-bs-toggle="modal" data-bs-target="#make-dir-modal">
            {{-- <i class="fa-solid fa-folder-plus me-2"></i> --}}
            Create Folder
        </button>
        <a href='/chunk-upload?path={{ $path }}&breadcrumbs={{ json_encode($breadcrumbs) }}' class="btn btn-outline-dark " ><i class="fa-solid fa-upload me-2"></i>Upload</a>
        <button type="button" class="btn btn-outline-danger" id="delete-button" disabled><i class="fa-solid fa-trash me-2"></i>Delete</button>
    </div>

    <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page"><a href="/" class="text-decoration-none">Home</a></li>
            @foreach ($breadcrumbs as $index => $breadcrumb)
                <li class="breadcrumb-item active" aria-current="page"><a href="/navigate?index={{ $index }}&breadcrumbs={{ json_encode($breadcrumbs) }}" class="text-decoration-none">{{ $breadcrumb }}</a></li>
            @endforeach
        </ol>
    </nav>
      
    <table class="table">
        <tbody>
            @foreach ($directories as $directory)
            <tr style="cursor: pointer;">
                <td colspan="3">
                    <input type="checkbox" onchange="checkDir(event)" value='{{ json_encode($directory) }}' name="" id="" class="form-check-input me-4">
                    <a class="text-decoration-none" href="/browse?path={{ $directory['dir'] }}">
                        {{-- <i class="fa-solid fa-folder me-2" style="color: #ebb331"></i> --}}
                        <img width="20" height="20" src="{{ asset('icons/folder.svg') }}" alt="Folder">
                        <i class="fa-solid fa-folder me-2" style="color: #ebb331"></i>
                        {{ $directory['name'] }}
                    </a>
                </td>
            </tr>
            @endforeach
            @foreach ($files as $file)
            <tr style="">
                <td>
                    <input type="checkbox" onchange="checkDir(event)" value='{{ json_encode($file) }}' name="" id="" class="form-check-input me-4">
                    {{-- <a href="http://localhost:3000/download?file={{ $file['file'] }}" class="text-decoration-none"> --}}
                    <a href="/download?fileDir={{ $file['file'] }}" class="text-decoration-none">
                        {{-- <i class="fa-solid fa-file  me-2" style="color: #88d4f7"></i> --}}
                        <img width="20" height="20" src="{{ asset('icons/document.svg') }}" alt="Folder">
                        {{ $file['name'] }}
                    </a>
                </td>
                <td>{{ ($file['lastModified']) }}</td>
                <td>{{ number_format($file['size'] / 1024 , 0) }} KB</td>
            </tr>
            @endforeach           
            @if (empty($directories) && empty($files))
                <tr>
                    <td colspan="3" class="text-center">No Files and Folders</td>
                </tr>
            @endif 
        </tbody>
    </table>
</div>

{{-- make directory modal --}}
<div class="modal fade" id="make-dir-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <h5>Create Folder</h5>
                <form action="/make-directory" method="GET">
                    <input type="hidden" name="path" value="{{ $path }}">
                    <div class="">
                        <label for="">Folder Name <span class="text-danger">*</span></label>
                        <input name="name" type="text" class="form-control" required>
                    </div>
                    <button class=" btn btn-primary d-block mt-3 ms-auto me-0" type="submit" >Create</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- upload file --}}
<div class="modal fade" id="upload-file-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <h5>Create Folder</h5>
                <form method="POST" action="/upload" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="path" value="{{ $path }}">
                    <div class="mb-3">
                        <label for="">Name</label>
                        <input type="text" name="name" class="form-control"  id="upload-file-name" value='{{ old('name') }}'>
                        @error('name', 'upload_form')
                            <label for="" class="text-danger">{{ $message }}</label>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <input type="file" name="file" class="form-control" id="upload-file-file" value='{{ old('file') }}' required>
                        @error('file', 'upload_form')
                            <label for="" class="text-danger">{{ $message }}</label>
                        @enderror
                    </div>
                    <button  class=" btn btn-primary d-block mt-3 ms-auto me-0" type="submit">Upload</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>

    let toDelete = [];
    $(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const uploadFileModal = new bootstrap.Modal(document.getElementById('upload-file-modal'));
        const $upload_file_file = $('#upload-file-file');
        const $upload_file_name = $('#upload-file-name');
        $delete_button = $('#delete-button');
        
        $delete_button.click(async function(){
            console.log("clicked")
            try{
                const res = await $.ajax({
                    type: 'POST',
                    url: '/delete-many', 
                    data: { dirs: toDelete },
                    async: true
                })
                location.reload();
            }
            catch(error){
                console.log(error)
                alert("An error accured while deleting");
            }
            
        })
        $upload_file_file.change(function(){
            console.log(simplifyFileName($(this).val()))
            $upload_file_name.val(simplifyFileName($(this).val()));
        })
        @if ($errors->upload_form->any())
            uploadFileModal.show();
        @endif
    })

    function checkDir(ev){
        $checkInput = $(ev.target); 
        let value = JSON.parse($checkInput.val());
        // console.log(value);
        if($checkInput.is(':checked')){
            toDelete.push(value);
        }
        else{
            toDelete = toDelete.filter((val) => {
                if(val.type == 'file'){
                    return val.file != value.file;
                }
                else{
                    return val.dir != value.dir;
                }
            })
        }
        if(toDelete.length > 0){
            $('#delete-button').attr('disabled', false)
        }
        else{
            $('#delete-button').attr('disabled', 'disabled')
        }
    }

    function simplifyFileName(name){
        let slashIndex
        for (var i = name.length - 1; i >= 0; i--) {
            if(name[i] == '\\'){
                slashIndex = i;
                break;
            }
        }            
        
        return name.substring(slashIndex+1, name.length);
    }
</script>
@endsection