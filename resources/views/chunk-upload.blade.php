@extends('index')
@section('content')
<div class="container pt-4">
    <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page"><a href="/" class="text-decoration-none">Home</a></li>
            @foreach ($breadcrumbs as $index => $breadcrumb)
                <li class="breadcrumb-item active" aria-current="page"><a href="/navigate?index={{ $index }}&breadcrumbs={{ json_encode($breadcrumbs) }}" class="text-decoration-none">{{ $breadcrumb }}</a></li>
            @endforeach
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">
                    <h5>Upload File</h5>
                </div>

                <div class="card-body">
                    <div id="upload-container" class="text-center">
                        <button id="browseFile" class="btn btn-primary">Browse Files</button>
                    </div>
                    <div  style="display: none" class="progress mt-3" style="height: 25px">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%; height: 100%">75%</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade">
        {{-- .modal --}}
    </div>
</div>


{{-- resumable js --}}
{{-- <script src="{{ asset('js/chunk-upload.js') }}"></script> --}}
<script src="{{ asset('resumablejs/resumable.js') }}"></script>
<script type="text/javascript">
    let browseFile = $('#browseFile');
    let resumable = new Resumable({
        target: '/upload-large',
        query:{
            _token: '{{ csrf_token() }}',
            path: '{{ $path }}'
        } ,// CSRF token
        fileType: [],
        chunkSize: 1*1024*1024, // default is 1*1024*1024, this should be less than your maximum limit in php.ini
        headers: {
            'Accept' : 'application/json'
        },
        testChunks: false,
        throttleProgressCallbacks: 1,
    });

    resumable.assignBrowse(browseFile[0]);

    resumable.on('fileAdded', async function (file) { // trigger when file picked
        if(await isFileTooLarge(file)){
            if(!confirm("File is too large. We recommend keeping file sizes under 500mb for optimal performance. \n\nDo you want to continue?")){
                return;
            }
        }
        showProgress();
        resumable.upload() // to actually start uploading.
    });

    resumable.on('fileProgress', function (file) { // trigger when file progress update
        updateProgress(Math.floor(file.progress() * 100));
    });

    resumable.on('fileSuccess', function (file, response) { // trigger when file upload complete
        response = JSON.parse(response)
        // alert("File upload finished")
        // location.href = '/browse?path=' + '{{ $path }}'
    });

    resumable.on('complete', function(){
        location.href = '/browse?path=' + '{{ $path }}'
    })

    resumable.on('fileError', function (file, response) { // trigger when there is any error
        console.log(response)
        alert('file uploading error.')
        location.reload()
    });


    let progress = $('.progress');
    function showProgress() {
        progress.find('.progress-bar').css('width', '0%');
        progress.find('.progress-bar').html('0%');
        progress.find('.progress-bar').removeClass('bg-success');
        progress.show();
    }

    function updateProgress(value) {
        progress.find('.progress-bar').css('width', `${value}%`)
        progress.find('.progress-bar').html(`${value}%`)
    }

    function hideProgress() {
        progress.hide();
    }

    async function isFileTooLarge(file){ // return boolean
        const maxFileSize = 0.5 * 1024 * 1024 * 1024 // 1GB
        if(file.file.size > maxFileSize){
            return true;
        }
        return false;
    }

</script>

@endsection