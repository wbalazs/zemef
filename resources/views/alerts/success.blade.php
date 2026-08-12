@if (session($key ?? 'status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session($key ?? 'status') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('message'))
    @foreach(session('message') as $m)
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $m }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endforeach
@endif