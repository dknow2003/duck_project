@extends('layouts.app')


@section('content-top')
  <div class="wrapper wrapper-content">
    <div class="row">
      <div class="col-lg-12" style="height: auto;">
        <iframe id="phpinfo" src="{{ url('admin/phpinfo') }}" width="100%"
                height="100%"
                style="width: 100%; height:3000px; min-width: 935px; display:block;"
                scrolling="no"
                frameborder="0"></iframe>
      </div>
    </div>
  </div>
@endsection

@section('style')
@endsection
@section('script')
  <script src="{{ asset('js/jquery.browser.js') }}"></script>
  <script src="{{ asset('js/jquery-iframe-auto-height.min.js') }}"></script>

  <script>
    $('#phpinfo').iframeAutoHeight();
  </script>
@endsection
