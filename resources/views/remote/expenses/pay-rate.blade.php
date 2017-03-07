@extends('layouts.app')

@section('content-top')

	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-12">
			<h2>{{ $menu->getActiveName() }}</h2>
		</div>
	</div>

	<div class="wrapper wrapper-content">
		<div class="row">
			<div class="col-lg-12">
				<div class="row">
					<div class="col-sm-4">
						<form action="" id="month-form">
							<select class="form-control m-b" name="month" id="month-select">
								@foreach(array_reverse(iterator_to_array($monthRange)) as $month)
									<?php
									$selected = false;
									if (\Request::get('month') == $month->format('Y年m月')) {
										$selected = true;
									}
									?>
									<option	value="{{ $month->format('Y年m月') }}" @if($selected) selected="true"@endif >{{ $month->format('Y年m月') }}</option>
								@endforeach
							</select>
						</form>
					</div>
					<div class="col-sm-8"></div>
				</div>

			</div>
		</div>
		<div class="row">

			<div class="col-lg-12 animated fadeInRight">
				<div class="ibox float-e-margins">
					<div class="row">
						<div class="ibox-content">
							<div>
								<canvas id="line-chart" height="60"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection

@section('style')

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
			var lineData = {
				labels: [
						<?php
					$count = count($result); $i = 0;
					?>
						@foreach($result as $r)
						"{{ $r['day'] }}" @if(++$i != $count), @endif
						@endforeach
				],
				datasets: [
					{
						label: "Example dataset",
						fillColor: "rgba(220,220,220,0.5)",
						strokeColor: "rgba(220,220,220,1)",
						pointColor: "rgba(220,220,220,1)",
						pointStrokeColor: "#fff",
						pointHighlightFill: "#fff",
						pointHighlightStroke: "rgba(220,220,220,1)",
						data: [
							<?php
									$count = count($result); $i = 0;
									?>
									@foreach($result as $r)
									"{{ $r['new'] }}" @if(++$i != $count), @endif
							@endforeach
						]
					},
					{
						label: "Example dataset",
						fillColor: "rgba(26,179,148,0.5)",
						strokeColor: "rgba(26,179,148,0.7)",
						pointColor: "rgba(26,179,148,1)",
						pointStrokeColor: "#fff",
						pointHighlightFill: "#fff",
						pointHighlightStroke: "rgba(26,179,148,1)",
						data: [
							<?php
									$count = count($result); $i = 0;
									?>
									@foreach($result as $r)
									"{{ $r['old'] }}" @if(++$i != $count), @endif
							@endforeach
						]
					}
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
			};


			var ctx = document.getElementById("line-chart").getContext("2d");
			var myNewChart = new Chart(ctx).Line(lineData, lineOptions);


		});
	</script>
@endsection
