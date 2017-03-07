<div class="mail-box-header">


  <h2>
    {{ trans('渠道管理') }} ({{ $advertisers->total() }})
  </h2>
  <div class="mail-tools tooltip-demo m-t-md">

    <a href="{{ url('admin/advertisers/create') }}" class="btn btn-md btn-primary" style="margin-right: 20px;">新建渠道</a>
  {{--<div class="btn-group pull-left">--}}
      <!-- Page button -->
      @if($previousUrl = $advertisers->previousPageUrl())
        <a class="btn btn-white btn-sm" href="{{ $previousUrl }}"><i class="fa fa-arrow-left"></i></a>
      @else
        <button class="btn btn-white btn-sm" disabled><i class="fa fa-arrow-left"></i></button>
      @endif

      @if($nextUrl = $advertisers->nextPageUrl())
        <a class="btn btn-white btn-sm" href="{{ $nextUrl }}"><i class="fa fa-arrow-right"></i></a>
      @else
        <button class="btn btn-white btn-sm" disabled><i class="fa fa-arrow-right"></i></button>
      @endif


    </div>
</div>
