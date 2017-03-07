<!DOCTYPE html>
<html>

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title>{{ $menu->getActiveName() }} - {{ app()['config']->get('app.site_name') }}</title>

  <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('font-awesome/css/font-awesome.css') }}"
        rel="stylesheet">
	<link href="{{ asset('css/plugins/toastr/toastr.min.css') }}" rel="stylesheet">
	<link href="{{ asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
  @yield('style')

  <link href="{{ asset('css/animate.css') }}" rel="stylesheet">
  <link href="{{ asset('css/style.css') }}" rel="stylesheet">

</head>

<body>
<div id="wrapper">

  @include('layouts.sidebar-left')

  <div id="page-wrapper" class="gray-bg dashbard-1">

    <div class="row border-bottom">
      @include('layouts.nav-top')
    </div>

    <div class="row  border-bottom white-bg dashboard-header">
      @yield('content-top')
    </div>

    <div class="row">
      <div class="col-lg-12">

        @section('content')
          @include('layouts.footer')
        @show

      </div>
    </div>

  </div>

  {{--@include('layouts.small-chat-window')--}}
  {{--@include('layouts.small-chat-button')--}}

{{--  @include('layouts.sidebar-right')--}}

</div>

<!-- Mainly scripts -->
<script src="{{ asset('js/jquery-2.1.1.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
<script
    src="{{ asset('js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
<!-- Custom and plugin javascript -->
<script src="{{ asset('js/inspinia.js') }}"></script>
<script src="{{ asset('js/plugins/pace/pace.min.js') }}"></script>
<!-- iCheck -->
<script src="{{ asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('js/plugins/toastr/toastr.min.js') }}"></script>

@include('layouts.notification')

<script>
	$(document).ready(function () {
		$('a').tooltip();
		// iCheck
		$('.i-checks').iCheck({
			checkboxClass: 'icheckbox_square-green',
			radioClass: 'iradio_square-green',
		});

		// Change users status form
		$('.switch-server').on('ifChecked', function () {
			$('.switch-server').iCheck('uncheck');
			$('#server-id').val($(this).val());
			$('#switch-server').submit();
		});


	});
</script>

@yield('script')

</body>
</html>
