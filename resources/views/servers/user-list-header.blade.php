<div class="mail-box-header">

  {{--<form method="get" action="index.html" class="pull-right mail-search">--}}
    {{--<div class="input-group">--}}
      {{--<input type="text" class="form-control input-sm" name="search" placeholder="{{ trans('user.search_by_email_or_username') }}">--}}
      {{--<div class="input-group-btn">--}}
        {{--<button type="submit" class="btn btn-sm btn-primary">--}}
          {{--{{ trans('utilities.search') }}--}}
        {{--</button>--}}
      {{--</div>--}}
    {{--</div>--}}
  {{--</form>--}}
  <h2>
    {{ trans('服务器管理') }} ({{ $servers->total() }})
  </h2>

  <div class="mail-tools tooltip-demo m-t-md">

    <a href="{{ url('admin/servers/create') }}" class="btn btn-md btn-primary" style="margin-right: 20px;">新建服务器</a>

      <!-- Page button -->
    @if($previousUrl = $servers->previousPageUrl())
        <a class="btn btn-white btn-sm" href="{{ $previousUrl }}"><i class="fa fa-arrow-left"></i></a>
      @else
        <button class="btn btn-white btn-sm" disabled><i class="fa fa-arrow-left"></i></button>
      @endif

    @if($nextUrl = $servers->nextPageUrl())
        <a class="btn btn-white btn-sm" href="{{ $nextUrl }}"><i class="fa fa-arrow-right"></i></a>
      @else
        <button class="btn btn-white btn-sm" disabled><i class="fa fa-arrow-right"></i></button>
      @endif

    {{-- Change the status --}}
    {{--<button class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="left" title="Refresh inbox"><i class="fa fa-refresh"></i> Refresh</button>--}}
    {{--<button type="submit" class="btn btn-white btn-sm change-default-btn" data-toggle="tooltip" data-placement="top" title="设为默认" value="1"><i class="fa fa-angellist"></i> </button>--}}
    {{--<button  type="submit" class="btn btn-white btn-sm change-status-btn" data-toggle="tooltip" data-placement="top" title="禁用选中帐号" value="0"><i class="fa fa-trash-o"></i> </button>--}}

  </div>
</div>
