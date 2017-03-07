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
					<div class="row">
						<div class="ibox-content">
							<div>
								<canvas id="bar-chart" height="60"></canvas>
							</div>
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
@endsection

@section('script')

	<!-- ChartJS-->
	<script src="{{ asset('js/plugins/chartJs/Chart.min.js') }}"></script>
	<script>
		$(document).ready(function () {
			// date
			$('#month-select').on('change', function (e) {
				$('#month-form').submit();
			});
			// chart
			var barData = {
				labels: [
					<?php $count = count(range(0, 23));$i = 0; ?>
							@foreach(range(0, 23) as $hour)
							"{{ $hour }}" @if(++$i != $count), @endif
							@endforeach
				],
				datasets: [
					{
						label: "My Second dataset",
						fillColor: "rgba(26,179,148,0.5)",
						strokeColor: "rgba(26,179,148,0.8)",
						highlightFill: "rgba(26,179,148,0.75)",
						highlightStroke: "rgba(26,179,148,1)",
						data: [
							<?php $i = 0; ?>
									@foreach($result as $r)
									"{{ $r['money'] }}" @if($count != ++$i), @endif
							@endforeach
						]
					}
				]
			};

			var barOptions = {
				scaleBeginAtZero: true,
				scaleShowGridLines: true,
				scaleGridLineColor: "rgba(0,0,0,.05)",
				scaleGridLineWidth: 1,
				barShowStroke: true,
				barStrokeWidth: 2,
				barValueSpacing: 5,
				barDatasetSpacing: 1,
				responsive: true
			};

			var ctx = document.getElementById("bar-chart").getContext("2d");
			var myNewChart = new Chart(ctx).Bar(barData, barOptions);

		});
	</script>
@endsection
