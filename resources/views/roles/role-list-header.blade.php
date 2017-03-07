<div class="mail-box-header">



  <h2>
    {{ trans('角色管理') }} ({{ $roles->total() }})
  </h2>

  <div class="mail-tools tooltip-demo m-t-md">

    <a href="{{ url('admin/roles/create') }}" class="btn btn-md btn-primary" style="margin-right: 20px;">新建角色</a>
  {{--<div class="btn-group pull-left">--}}
      <!-- Page button -->
      @if($previousUrl = $roles->previousPageUrl())
        <a class="btn btn-white btn-sm" href="{{ $previousUrl }}"><i class="fa fa-arrow-left"></i></a>
      @else
        <button class="btn btn-white btn-sm" disabled><i class="fa fa-arrow-left"></i></button>
      @endif

      @if($nextUrl = $roles->nextPageUrl())
        <a class="btn btn-white btn-sm" href="{{ $nextUrl }}"><i class="fa fa-arrow-right"></i></a>
      @else
        <button class="btn btn-white btn-sm" disabled><i class="fa fa-arrow-right"></i></button>
      @endif

    {{--</div>--}}
    {{--<div class="btn-group pull-right">--}}
      {{--<!-- Page button -->--}}
      {{--@if($previousUrl = $roles->previousPageUrl())--}}
        {{--<a class="btn btn-white btn-sm" href="{{ $previousUrl }}"><i class="fa fa-arrow-left"></i></a>--}}
      {{--@else--}}
        {{--<button class="btn btn-white btn-sm" disabled><i class="fa fa-arrow-left"></i></button>--}}
      {{--@endif--}}

      {{--@if($nextUrl = $roles->nextPageUrl())--}}
        {{--<a class="btn btn-white btn-sm" href="{{ $nextUrl }}"><i class="fa fa-arrow-right"></i></a>--}}
      {{--@else--}}
        {{--<button class="btn btn-white btn-sm" disabled><i class="fa fa-arrow-right"></i></button>--}}
      {{--@endif--}}

    </div>
  {{----}}
    {{--<form action="{{ url('users/change-state') }}">--}}
      {{--{{ csrf_field() }}--}}
    {{-- Change the state --}}
    {{--<button class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="left" title="Refresh inbox"><i class="fa fa-refresh"></i> Refresh</button>--}}
    {{--<button class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="top" title="Mark as read"><i class="fa fa-eye"></i> </button>--}}
    {{--<button class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="top" title="Move to trash"><i class="fa fa-trash-o"></i> </button>--}}
    {{--</form>--}}
  {{--</div>--}}
</div>
