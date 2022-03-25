@if (session('status_failed'))
    <div class="alert alert-danger" role="alert">
        {{ __(session('status_failed')) }}
    </div>
@endif

@if (session('status_warning'))
    <div class="alert alert-warning" role="alert">
        {{ __(session('status_warning')) }}
    </div>
@endif

@if (session('status_success'))
    <div class="alert alert-success" role="alert">
        {{ __(session('status_success')) }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
