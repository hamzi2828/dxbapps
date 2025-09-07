@if(session('error'))
    <div class="alert alert-error">
        <strong>Error:</strong> {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">
        <strong>Success:</strong> {{ session('success') }}
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info">
        <strong>Info:</strong> {{ session('info') }}
    </div>
@endif