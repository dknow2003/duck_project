<div class="mail-box">

  <table class="table table-hover table-mail">
    <tbody>

    @if(count($roles))
    @foreach($roles as $role)
      <tr class="read">
        <td class="mail-ontact" style="padding-left: 20px;">
          <a href="{{ url('admin/roles/' . $role->id . '/edit') }}">
            {{ $role->display_name }}
          </a>
        </td>
{{--        <td class="mail-subject"><a href="{{ url('users/' . $role->id . '/edit') }}">{{ $role->email }}</a></td>--}}
        <td>
          <i class="fa fa-users"></i>
          @if($count = $role->usersCount)
            <a href="{{ url('admin/users?role=' . $role->id) }}" data-placement="top" data-original-title="帐号数量" rel="tooltip" class="badge badge-sm badge-primary " style="color: #fff;">
              {{ $count }}
            </a>
          @else
            <span href="{{ url('admin/users?role=' . $role->id) }}" data-placement="top" data-original-title="帐号数量" rel="tooltip" class="badge badge-sm badge-default" style="color: #fff;">
              {{ $count }}
            </span>
          @endif
        </td>
        <td>
          <i class="fa fa-gears"></i>
          <a href="{{ url('admin/roles/' . $role->id . '/edit') }}" data-placement="top" data-original-title="拥有权限" rel="tooltip" class="badge badge-sm badge-primary " style="color: #fff;">
            {{ $role->perms()->count() }}
          </a>
        </td>

        <td class="text-right mail-date">{{ $role->updated_at }}</td>
      </tr>
    @endforeach
      @else
      <tr class="read">
        <td class="check-mail" style="background: #fff;">
          <div class="alert alert-warning" style="margin: 20px 0;">
            角色列表为空， <a class="alert-link"
                       href="{{ url('admin/roles/create') }}">新建一个？</a>。
          </div>
        </td>
      </tr>
    @endif

    </tbody>
  </table>
</div>
