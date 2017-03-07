<div class="mail-box">

  <table class="table table-hover table-mail">
    <tbody>

    @if(count($channels))
    @foreach($channels as $channel)
      <tr class="read">
        <td style="padding-left: 20px;">
          <a href="{{ url('admin/channels/' . $channel->id . '/edit') }}">
            {{ $channel->channel_id}}
          </a>
        </td>
        <td class="mail-ontact" style="padding-left: 20px;">
          <a href="{{ url('admin/channels/' . $channel->id . '/edit') }}">
            {{ $channel->name}}
          </a>
        </td>

        <td class="text-right mail-date">{{ $channel->updated_at }}</td>
      </tr>
    @endforeach
      @else
      <tr class="read">
        <td class="check-mail" style="background: #fff;">
          <div class="alert alert-warning" style="margin: 20px 0;">
            渠道列表为空， <a class="alert-link"
                       href="{{ url('admin/channels/create') }}">新建一个？</a>。
          </div>
        </td>
      </tr>
    @endif
    </tbody>
  </table>
</div>
