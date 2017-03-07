@extends('layouts.app')

@section('content-top')

	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-12">
			<h2>{{ $menu->getActiveName() }}</h2>
		</div>
	</div>

	<div class="wrapper wrapper-content">
		<div class="row">

			<div class="col-lg-12 animated fadeInRight">
				<div class="ibox float-e-margins">


					{{-- search --}}
					<form method="GET" action="{{ url('online/trending') }}"
					      id="fetch-form">
						<input type="hidden" name="by"
						       value="{{ $by = \Request::get('by') }}" id="fetch-input">
						<div class="row" style="">
							<div class="col-sm-12">
								<div data-toggle="buttons" class="btn-group"
								     style="">

									<label
											class="btn btn-sm btn-white fetch-button {{ !$by || $by == "month" ? ' active' : ''}}">
										<input type="radio" id="option1" name="" value="month">月
									</label>

									<label
											class="btn btn-sm btn-white fetch-button {{ $by == "week" ? ' active' : ''}}">
										<input type="radio" id="option1" name="" value="week">周
									</label>

									<label
											class="btn btn-sm btn-white fetch-button {{ $by == "day" ? ' active' : ''}}">
										<input type="radio" id="option1" name="" value="day">天
									</label>

									<label
											class="btn btn-sm btn-white fetch-button {{ $by == "hour" ? ' active' : ''}}">
										<input type="radio" id="option1" name="" value="hour">小时
									</label>

									{{-- date --}}
									<div class="input-group" style="margin-left: 20px;">
										<input id="form-input" type="text"
										       class="input-sm form-control active" name="from"
										       value="{{ \Request::get('from') ?: ''}}" style="margin-left: 20px; border-radius: 4px;">
									</div>
									{{-- // end date --}}
								</div>

							</div>
						</div>
					</form>


					<div class="row">
						<div class="ibox-content">
							<div id="morris-line-chart"></div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

@endsection

@section('style')
	<link href="{{ asset('css/plugins/datapicker/datepicker3.css') }}"
	      rel="stylesheet">
	<!-- morris -->
	<link href="{{ asset('css/plugins/morris/morris-0.4.3.min.css') }}"
	      rel="stylesheet">
@endsection

@section('script')
	<!-- Data picker -->
	<script
			src="{{ asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
	<script
			src="{{ asset('js/plugins/datapicker/bootstrap-datepicker.zh-CN.min.js') }}"
			charset="UTF-8"></script>
	<!-- Morris -->
	<script src="{{ asset('js/plugins/morris/raphael-2.1.0.min.js') }}"></script>
	<script src="{{ asset('js/plugins/morris/morris.js') }}"></script>

	<script>
		$(document).ready(function () {

			// datepicker
			$('#form-input').datepicker({
				keyboardNavigation: false,
				forceParse: false,
				autoclose: true,
				language: 'zh-CN',
				format: 'yyyy-mm-dd',
			});

			// filter form
			$('#form-input').on('changeDate', function () {
				$('#fetch-form').submit();
			});

			// 点击选择按什么排序
			$('.fetch-button').click(function (e) {
				var type = $(this).find('input').val();
				$('#fetch-input').val(type);
				$('#fetch-form').submit();
			});

			<!-- Morris -->
			Morris.Area({
				element: 'morris-line-chart',
				data: [
						@foreach($stats as $stat)
					{
						x: '{{ $stat['x'] }}', y: parseInt({{ $stat['avg'] }})
					},
					@endforeach
				],
				xkey: 'x',
				ykeys: 'y',
				postUnits: ' 人',
				xLabelFormat: function (date) {
					return date.label + '{{ $stats->x_unit or '' }}';
				},
				parseTime: false,
				resize: true,
				lineWidth: 4,
				labels: ['在线'],
				lineColors: ['#1ab394'],
				pointSize: 5,
				gridTextSize: 13,
				continuousLine: false
			});

		});
	</script>
@endsection
