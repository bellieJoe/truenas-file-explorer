@extends('index')
@section('content')
<nav class="navbar">
    <div class="container-lg">
        <a href="/" class="navbar-brand fw-bold text-primary">File Explorer</a>
    </div>
</nav>
<div class="container-lg py-4">
    <div class="mb-3">
        <button class="btn btn-primary btn-sm"><i class="fa-solid fa-folder-plus me-2"></i>Create Folder</button>
        <button class="btn btn-primary btn-sm"><i class="fa-solid fa-upload me-2"></i>Upload</button>
    </div>
    <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);">
        <ol class="breadcrumb">
            @foreach ($breadcrumbs as $breadcrumb)
                @if ($breadcrumb == 'home')
                    <li class="breadcrumb-item active" aria-current="page"><a href="/" class="text-decoration-none">{{ $breadcrumb }}</a></li>
                @else
                    <li class="breadcrumb-item active" aria-current="page"><a href="/{{ $breadcrumb }}" class="text-decoration-none">{{ $breadcrumb }}</a></li>
                @endif
            @endforeach
        </ol>
    </nav>
      
    <table class="table">
        <tbody>
            @foreach ($directories as $directory)
            <tr style="cursor: pointer;">
                <td><i class="fa-solid fa-folder text-secondary"></i></td>
                <td colspan="3"><a href="/{{ $directory }}?breadcrumbs={{ json_encode($breadcrumbs) }}">{{ $directory }}</a></td>
            </tr>
            @endforeach
            @foreach ($files as $file)
            <tr style="">
                <td><i class="fa-solid fa-file text-secondary"></i></td>
                <td><a href="/download/{{ $file['file'] }}" class="text-decoration-none">{{ $file['file'] }}</a></td>
                <td>{{ ($file['lastModified']) }}</td>
                <td>{{ number_format($file['size'] / 1024 , 0) }} KB</td>
            </tr>
            @endforeach            
        </tbody>
    </table>
</div>
<script>

</script>
@endsection