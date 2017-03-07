@extends('layouts.app')

@section('content-top')

	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-12">
			<h2>{{ $menu->getActiveName() }}</h2>
		</div>
	</div>
	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="col-lg-12">
			<div class="row">
				<div class="col-lg-6">
					<div class="widget navy-bg no-padding">
						<div class="p-m">
							<h1 class="m-xs"><i class="fa fa-gamepad"></i>
								{{ $currentOnline }}
							</h1>

							<h3 class="font-bold no-margins">
								实时在线 </h3>
							<small> &nbsp; </small>
						</div>
						<div class="flot-chart">
							<div class="flot-chart-content" id="flot-chart1"></div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="widget lazur-bg no-padding">
						<div class="p-m">
							<h1 class="m-xs"><i class="fa fa-child"></i> {{ $currentLogin }}
							</h1>

							<h3 class="font-bold no-margins">
								实时登录 </h3>
							<small> &nbsp;</small>
						</div>
						<div class="flot-chart">
							<div class="flot-chart-content" id="flot-chart2"></div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="widget yellw-bg no-padding">
						<div class="p-m">
							<h1 class="m-xs">{{ 100 * round($roleCount / $userCount, 3) }}
								%</h1>

							<h3 class="font-bold no-margins">
								角色占帐号比例 </h3>
							<small>帐号 {{ $userCount }} 个，角色 {{ $roleCount }} 个。</small>
						</div>
						<div class="flot-chart">
							<div class="flot-chart-content" id="flot-chart3"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="row" style="height: 45px;"></div>
		</div>
	</div>
@endsection

@section('style')

	<!-- morris -->
	<link href="{{ asset('css/plugins/morris/morris-0.4.3.min.css') }}"
	      rel="stylesheet">
@endsection

@section('script')

	<!-- Morris -->
	<script src="{{ asset('js/plugins/morris/raphael-2.1.0.min.js') }}"></script>
	<script src="{{ asset('js/plugins/morris/morris.js') }}"></script>

	<!-- Flot -->
	<script src="{{ asset('js/plugins/flot/jquery.flot.js') }}"></script>
	<script
			src="{{ asset('js/plugins/flot/jquery.flot.tooltip.min.js') }}"></script>
	<script src="{{ asset('js/plugins/flot/jquery.flot.resize.js') }}"></script>
	<script>
		$(document).ready(function () {
			var d1 = [[1262304000000, 6], [1264982400000, 3057], [1267401600000, 20434], [1270080000000, 31982], [1272672000000, 26602], [1275350400000, 27826], [1277942400000, 24302], [1280620800000, 24237], [1283299200000, 21004], [1285891200000, 12144], [1288569600000, 10577], [1291161600000, 10295]];
			var d2 = [[1262304000000, 5], [1264982400000, 200], [1267401600000, 1605], [1270080000000, 6129], [1272672000000, 11643], [1275350400000, 19055], [1277942400000, 30062], [1280620800000, 39197], [1283299200000, 37000], [1285891200000, 27000], [1288569600000, 21000], [1291161600000, 17000]];

			var data1 = [
				{label: "Data 1", data: d1, color: '#17a084'},
				{label: "Data 2", data: d2, color: '#127e68'}
			];
			$.plot($("#flot-chart1"), data1, {
				xaxis: {
					tickDecimals: 0
				},
				series: {
					lines: {
						show: true,
						fill: true,
						fillColor: {
							colors: [{
								opacity: 1
							}, {
								opacity: 1
							}]
						}
					},
					points: {
						width: 0.1,
						show: false
					}
				},
				grid: {
					show: false,
					borderWidth: 0
				},
				legend: {
					show: false
				}
			});

			var data2 = [
				{label: "Data 1", data: d1, color: '#19a0a1'}
			];
			$.plot($("#flot-chart2"), data2, {
				xaxis: {
					tickDecimals: 0
				},
				series: {
					lines: {
						show: true,
						fill: true,
						fillColor: {
							colors: [{
								opacity: 1
							}, {
								opacity: 1
							}]
						}
					},
					points: {
						width: 0.1,
						show: false
					}
				},
				grid: {
					show: false,
					borderWidth: 0
				},
				legend: {
					show: false
				}
			});

			var data3 = [
				{label: "Data 1", data: d1, color: '#fbbe7b'},
				{label: "Data 2", data: d2, color: '#f8ac59'}
			];
			$.plot($("#flot-chart3"), data3, {
				xaxis: {
					tickDecimals: 0
				},
				series: {
					lines: {
						show: true,
						fill: true,
						fillColor: {
							colors: [{
								opacity: 1
							}, {
								opacity: 1
							}]
						}
					},
					points: {
						width: 0.1,
						show: false
					}
				},
				grid: {
					show: false,
					borderWidth: 0
				},
				legend: {
					show: false
				}
			});
		});
	</script>
@endsection
