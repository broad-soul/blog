@if(session($status))
    <br>
    <br>
    <div class="alert alert-{{ $class }}">{{ session($status) }}</div>
@endif