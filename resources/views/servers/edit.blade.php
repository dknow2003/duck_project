@extends('layouts.app')

@section('content-top')

  <div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
      <h2>修改服务器</h2>
    </div>
    <div class="col-lg-2">

    </div>
  </div>
  <div class="row wrapper wrapper-content animated fadeInRight">
    <div class="row">
      <div class="col-lg-12">

        {!! Form::model($server, ['route' => ['admin.servers.update', $server->id], 'method' => 'PUT', 'class' => 'form-horizontal']) !!}

        @include('servers.form', ['submitName' => '修改服务器'])

        {!! Form::close() !!}

        <div class="clearfix"></div>
      </div>
    </div>
  </div>
@endsection

@section('style')
	<link href="{{ asset('css/plugins/datapicker/datepicker3.css') }}"
	      rel="stylesheet">
@endsection

@section('script')
	<!-- Data picker -->
	<script
			src="{{ asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
	<script
			src="{{ asset('js/plugins/datapicker/bootstrap-datepicker.zh-CN.min.js') }}"
			charset="UTF-8"></script>
	<script>
		$(document).ready(function () {
			// datepicker
			$('.input-date').datepicker({
				keyboardNavigation: false,
				forceParse: false,
				autoclose: true,
				language: 'zh-CN',
				format: 'yyyy-mm-dd'
			});
		});
	</script>
@endsection
