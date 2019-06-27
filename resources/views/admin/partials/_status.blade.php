@if($status == 0 && $is_admin == 0)
<a href="/admin/users/ban/{{ $user_id }}" data-toggle="tooltip" data-placement="bottom" title="Забанить" class="btn btn-success">Активный</a>
@elseif($status == 1)
<a href="/admin/users/ban/{{ $user_id }}" data-toggle="tooltip" data-placement="bottom" title="Разбанить" class="btn btn-danger">Забанен</a>
@endif