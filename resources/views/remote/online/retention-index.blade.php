@extends('layouts.app')

@section('content-top')

	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-12">
			<h2>{{ $menu->getActiveName() }} {{ $date->toDateString() }}</h2>
		</div>
	</div>

	<div class="wrapper wrapper-content">

		<div class="row">

			<div class="col-lg-12 animated fadeInRight">
				<div class="ibox float-e-margins">
					{{-- search --}}

					<form method="GET" id="day-form">
						<div class="row">
							<div class="col-sm-12" style="margin: 20px 0;">
								<div class="input-group pull-left">
									<input id="form-input" type="text"
									       class="input-sm form-control active" name="day"
									       value="{{ $date->toDateString() }}" style="border-radius: 4px;">
								</div>
								<?php
									// 如果用户之前设有邮箱，就用之前的，如果没有，则用用户资料里的。
								$emailAddress = '';
									?>
								<span id="export-button" class="btn btn-primary btn-circle pull-right"
								   style="display:inline-block" data-container="body" data-placement="left" data-content='<input id="email-input" autocomplete="off" class="form-control" name="email" value="{{ $emailAddress }}"><a class="btn btn-primary btn-xs pull-right" style="margin:10px 5px;" id="export-email-button">导出到邮箱</a><a class="btn btn-primary btn-xs pull-right" style="margin:10px 5px;" id="export-directly-button">直接下载</a>'><i
											class="fa fa-download"></i></span>
							</div>
						</div>
					</form>
					<div class="row">
						<div class="ibox-content">
							<canvas id="au-ip" height="60" class="chart"></canvas>
							<div id="legend"></div>
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

	<!-- ChartJS-->
	<script src="{{ asset('js/plugins/chartJs/Chart.min.js') }}"></script>
	<!-- Data picker -->
	<script
			src="{{ asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
	<script
			src="{{ asset('js/plugins/datapicker/bootstrap-datepicker.zh-CN.min.js') }}"
			charset="UTF-8"></script>

	<script>
		$(document).ready(function () {
			<?php
			$query = http_build_query([
					'day'    => \Request::get('day'),
					'export' => true
			]);
			?>
			// enable html and make url
			$('#export-button').popover({ html : true }).on('shown.bs.popover', function () {
				// export directly
				$('#export-directly-button').attr('href', '?{!! $query !!}');
				// export to email
				var emailAddress = $('#email-input').val();
				$('#export-email-button').attr('href', '?{!! $query !!}&to_email=1&email=' + emailAddress);
				$('#email-input').on('input', function () {
					$('#export-email-button').attr('href', '?{!! $query !!}&to_email=1&email=' + $(this).val());
				});
			});

			// datepicker
			$('#form-input').datepicker({
				keyboardNavigation: false,
				forceParse: false,
				autoclose: true,
				language: 'zh-CN',
				format: 'yyyy-mm-dd'
			});

			// filter form
			$('#form-input').on('changeDate', function () {
				$('#day-form').submit();
			});


			// 在线趋势
			var lineData = {
				labels: [
					<?php $count = count(end($result)); $i = 0; ?>
							@foreach(end($result) as $key => $au)
							"{{ $au['day'] }}" @if(++$i != $count), @endif
					@endforeach
				],
				datasets: [
						<?php $c = count($result); $d = 0;?>
						@foreach($result as $rk => $r)
					{
						label: "{{ $rk }}: ",
						fillColor: "rgba(220,220,220,0)",
						strokeColor: "{{ $color = random_color() }}",
						pointColor: "{{ $color }}",
						pointStrokeColor: "#fff",
						pointHighlightFill: "#fff",
						pointHighlightStroke: "rgba(220,220,220,1)",
						data: [
							<?php $count = count($r); $i = 0 ?>
									@foreach($r as $key => $au)
									"{{ round($au['au'], 2) }}" @if(++$i != $count), @endif
							@endforeach
						]
					} @if(++$d != $c), @endif
					@endforeach
				]
			};

			var lineOptions = {
				scaleShowGridLines: true,
				scaleGridLineColor: "rgba(0,0,0,.05)",
				scaleGridLineWidth: 1,
				bezierCurve: true,
				bezierCurveTension: 0.4,
				pointDot: true,
				pointDotRadius: 4,
				pointDotStrokeWidth: 1,
				pointHitDetectionRadius: 20,
				datasetStroke: true,
				datasetStrokeWidth: 2,
				datasetFill: true,
				responsive: true,
				animateScale: true,
				multiTooltipTemplate: "<%= datasetLabel %> <%= value %> %",
			scales: {
				xAxes: [{
					gridLines: {
						drawBorder: false,
						display: false
					}
				}],
				yAxes: [{
					gridLines: {
						drawBorder: false,
						display: false
					},
					ticks: {
						beginAtZero: true
					}
				}]
			}
	  };

	  var ctx = document.getElementById("au-ip").getContext("2d");
	  var myNewChart = new Chart(ctx).Line(lineData, lineOptions);
	  });
  </script>
@endsection
