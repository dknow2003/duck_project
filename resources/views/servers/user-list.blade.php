{!! Form::open(['url' => 'admin/servers/change-default', 'method' => 'POST', 'id' => 'change-default-form']) !!}
<div class="mail-box">

  <table class="table table-hover table-mail">
    <tbody>

    @if(count($servers))
      @foreach($servers as $server)
        <tr class="read">

          <td class="mail-ontact"><a
                href="{{ url('admin/servers/' . $server->id . '/edit') }}">ID {{ $server->id }}</a>
          </td>

          <td class="mail-ontact"><a
                href="{{ url('admin/servers/' . $server->id . '/edit') }}">{{ $server->name }}</a>
          </td>
          <td class="">
            @if(!$server->status)
              <span class="label label-danger">已禁用</span>
            @endif
          </td>
          <?php
          ?>
          <td class="mail-subject"><a
                href="{{ url('admin/servers/' . $server->id . '/edit') }}">{{
              $server->connections[1]['host'] == $server->connections[2]['host']
              ? $server->connections[1]['host']
              : $server->connections[1]['host'] . ' -- ' . $server->connections[2]['host']
              }}</a>
          </td>
          {{--<td class="">--}}
          {{--@foreach($server->roles as $role)--}}
          {{--<span class="label label-warning">Warning</span>--}}
          {{--<span class="badge badge-primary">{{ $role->display_name }}</span>--}}
          {{--@endforeach--}}
          {{--</td>--}}

          <td class="text-right mail-date">{{ $server->updated_at }}</td>
        </tr>
      @endforeach
    @else
      <tr class="read">
        <td class="check-mail" style="background: #fff;">
          <div class="alert alert-warning" style="margin: 20px 0;">
            服务器列表为空， <a class="alert-link"
                        href="{{ url('admin/servers/create') }}">新建一个？</a>。
          </div>
        </td>
      </tr>
    @endif


    {{--<tr class="read">--}}
    {{--<td class="check-mail">--}}
    {{--<input type="checkbox" class="i-checks">--}}
    {{--</td>--}}
    {{--<td class="mail-ontact"><a href="mail_detail.html">Facebook</a> <span--}}
    {{--class="label label-warning pull-right">Clients</span></td>--}}
    {{--<td class="mail-subject"><a href="mail_detail.html">Many desktop--}}
    {{--publishing packages and web page editors.</a></td>--}}
    {{--<td class=""></td>--}}
    {{--<td class="text-right mail-date">Jan 16</td>--}}
    {{--</tr>--}}
    {{--<tr class="read">--}}
    {{--<td class="check-mail">--}}
    {{--<input type="checkbox" class="i-checks">--}}
    {{--</td>--}}
    {{--<td class="mail-ontact"><a href="mail_detail.html">Alex T.</a> <span--}}
    {{--class="label label-danger pull-right">Documents</span></td>--}}
    {{--<td class="mail-subject"><a href="mail_detail.html">Lorem ipsum dolor--}}
    {{--noretek imit set.</a></td>--}}
    {{--<td class=""><i class="fa fa-paperclip"></i></td>--}}
    {{--<td class="text-right mail-date">December 22</td>--}}
    {{--</tr>--}}

    {{--<tr class="read">--}}
    {{--<td class="check-mail">--}}
    {{--<input type="checkbox" class="i-checks">--}}
    {{--</td>--}}
    {{--<td class="mail-ontact"><a href="mail_detail.html">Patrick Pertners</a>--}}
    {{--<span class="label label-info pull-right">Adv</span></td>--}}
    {{--<td class="mail-subject"><a href="mail_detail.html">If you are going to--}}
    {{--use a passage of Lorem </a></td>--}}
    {{--<td class=""></td>--}}
    {{--<td class="text-right mail-date">May 28</td>--}}
    {{--</tr>--}}

    </tbody>
  </table>
</div>
{!! Form::close() !!}
