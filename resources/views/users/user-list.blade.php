{!! Form::open(['url' => 'admin/users/change-status', 'method' => 'POST', 'id' => 'change-status-form']) !!}
<input type="hidden" value="0" name="status" id="change-status-type">
<input type="hidden" value="" name="user_id" id="change-status-value">
<div class="mail-box">

  <table class="table table-hover table-mail">
    <tbody>
    @if(count($users))
      @foreach($users as $user)
        <tr class="read">
          <td class="check-mail">
            <input type="checkbox" class="i-checks check-users" name="users[]"
                   value="{{ $user->id }}" {{ $user->status ? " checked" : '' }}>
          </td>
          <td class="mail-ontact"><a
                href="{{ url('admin/users/' . $user->id . '/edit') }}">
              {{ $user->username }} {{ $user->full_name ? '(' . $user->full_name . ')' : ''}}</a>
          </td>

          <td class="">
            @foreach($user->roles as $role)
              <a href="{{ url('admin/roles/' . $role->id . '/edit')  }}"
                 data-placement="top"
                 data-original-title="查看{{ $role->display_name }}权限"
                 rel="tooltip"><span
                    class="badge badge-primary">{{ $role->display_name }}</span></a>
            @endforeach
          </td>

          <td class="mail-subject"><a
                href="{{ url('admin/users/' . $user->id . '/edit') }}">{{ $user->email }}</a>
          </td>
          <td class="">
            @if(!$user->status)
              <span class="label label-danger">已禁用</span>
            @endif
          </td>

          <td class="text-right mail-date">{{ $user->updated_at }}</td>
        </tr>
      @endforeach
    @else
      <tr class="read">
        <td class="check-mail" style="background: #fff;">
          <div class="alert alert-warning" style="margin: 20px 0;">
            账号列表为空， <a class="alert-link"
                       href="{{ url('admin/users/create') }}">新建一个？</a>。
          </div>
        </td>
      </tr>
    @endif

    </tbody>
  </table>
</div>
{!! Form::close() !!}
