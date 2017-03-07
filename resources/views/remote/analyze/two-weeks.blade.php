@extends('layouts.app')

@section('content-top')
	<div class="wrapper wrapper-content">

		{{-- 纵览面板 --}}
		<div class="row">

			<div class="col-lg-9">
				<div class="widget style1 navy-bg">
					<div class="row">

						<div class="col-xs-4">
							<div class="col-xs-4 ">
								<i class="fa fa-users fa-4x middle"></i>
							</div>
							<div class="col-xs-8 text-left">
								<span>注册 / 活跃 / 充值人数</span>
								<h2 class="font-bold">{{ $players['registers'] }} / {{ $players['actives'] }} / {{ $players['payed'] }}</h2>
							</div>
						</div>

						<div class="col-xs-4">
							<div class="col-xs-4">
								<i class="fa fa-credit-card fa-4x"></i>
							</div>
							<div class="col-xs-8 text-left">
								<span>收入</span>
								<h2 class="font-bold">{{ $players['amount'] }}</h2>
							</div>
						</div>

						<div class="col-xs-4">
							<div class="col-xs-4 ">
								<i class="fa fa-clock-o fa-4x middle"></i>
							</div>
							<div class="col-xs-8 text-left">
								<span>开服日期</span>
								<h2 class="font-bold">{{ $players['start_time'] }}</h2>
							</div>
						</div>
					</div>
				</div>
			</div>
			{{-- 日期范围输入 --}}
			<div class="col-lg-3">
				<form method="GET" id="filter-form">
					<div class="widget style2" style="border:1px solid #1ab394;">
						<div class="row" style="padding: 15px 0;">
							<div class="col-xs-12">

								<div class="input-daterange input-group">
									<input id="from-input" type="text" class="input-sm form-control active"
									       name="from" value="{{ $players['display_from'] }}">
									<span class="input-group-addon"> 到 </span>
									<input id="to-input" type="text" class="input-sm form-control active"
									       name="to" value="{{ $players['display_to'] }}">
								</div>
							</div>
							<div class="row">

							</div>
						</div>
					</div>
				</form>
			</div>
			{{--//纵览--}}

			{{-- 在线趋势 --}}
			<div class="col-lg-12">
				<div class="ibox float-e-margins">

					<div class="ibox-title">
						<h5>在线趋势</h5>
						<div class="ibox-tools">
							<a class="btn btn-primary btn-circle" href="?export=true&fetch=acu-pcu">
								<i class="fa fa-download"></i>
							</a>
					</div>

					<div class="ibox-content">
						<div class="col-xs-4">
							<div>
								<canvas id="acu-pcu" height="120"></canvas>
							</div>
							{{--<span>平均在线 ACU  {{ $players['acu'] }}比 最高在线 PCU {{ $players['pcu'] }} </span>--}}
						</div>
						<div class="col-xs-8 text-left">
							<div>
								<canvas id="acu-pcu-line" height="60"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>
			{{--//在线趋势--}}

			{{-- 关键指标--}}
			<div class="col-lg-12" id="download-key">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5>关键指标</h5>
						<div class="ibox-tools">
							<a class="btn btn-primary btn-circle export-button" href="?export=true&fetch=login-register">
								<i class="fa fa-download"></i>
							</a>
						</div>
					</div>
					<div class="ibox-content">
						<div class="tabs-container">
							<ul class="nav nav-tabs">
								<li class="active"><a data-toggle="tab" data-method="new"
								                      data-filled="true" href="#tab-1" data-download-area="#download-key" data-download-link="?export=true&fetch=login-register&from={{ $players['display_from'] }}&to={{ $players['display_to'] }}">新增玩家</a>
								</li>
								<li class=""><a data-toggle="tab" data-method="active"
								                href="#tab-2" data-download-area="#download-key" data-download-link="/analyze/summarize/json?method=active&export=true&fetch=active&from={{ $players['display_from'] }}&to={{ $players['display_to'] }}&by=two_weeks">活跃玩家</a>
								</li>
								<li class=""><a data-toggle="tab" data-method="payed"
								                href="#tab-3" data-download-area="#download-key" data-download-link="/analyze/summarize/json?method=payed&export=true&fetch=payed&from={{ $players['display_from'] }}&to={{ $players['display_to'] }}&by=two_weeks">付费玩家</a>
								</li>
								<li class=""><a data-toggle="tab" data-method="incoming"
								                href="#tab-4" data-download-area="#download-key" data-download-link="/analyze/summarize/json?method=incoming&export=true&fetch=incoming&from={{ $players['display_from'] }}&to={{ $players['display_to'] }}&by=two_weeks">收入</a>
								</li>
							</ul>
							<div class="tab-content">
								<div id="tab-1" class="tab-pane active fade in">
									<div class="panel-body">

										<div class="spiner-example" style="display: none;">
											<div class="sk-spinner sk-spinner-wave">
												<div class="sk-rect1"></div>
												<div class="sk-rect2"></div>
												<div class="sk-rect3"></div>
												<div class="sk-rect4"></div>
												<div class="sk-rect5"></div>
											</div>
										</div>
										{{-- 新增玩家 --}}
										<canvas id="registers-logins" height="60"
										        class="chart"></canvas>

									</div>
								</div>
								<div id="tab-2" class="tab-pane fade in">
									<div class="panel-body">

										<div class="spiner-example">
											<div class="sk-spinner sk-spinner-wave">
												<div class="sk-rect1"></div>
												<div class="sk-rect2"></div>
												<div class="sk-rect3"></div>
												<div class="sk-rect4"></div>
												<div class="sk-rect5"></div>
											</div>
										</div>
										{{--  活跃玩家--}}
										<canvas id="active-player" height="60"
										        class="chart"></canvas>
									</div>
								</div>
								<div id="tab-3" class="tab-pane fade in">
									<div class="panel-body">

										<div class="spiner-example">
											<div class="sk-spinner sk-spinner-wave">
												<div class="sk-rect1"></div>
												<div class="sk-rect2"></div>
												<div class="sk-rect3"></div>
												<div class="sk-rect4"></div>
												<div class="sk-rect5"></div>
											</div>
										</div>

										{{--  付费玩家 --}}
										<canvas id="payed-player" class="chart" height="60"></canvas>
									</div>
								</div>
								<div id="tab-4" class="tab-pane fade in">
									<div class="panel-body">

										<div class="spiner-example">
											<div class="sk-spinner sk-spinner-wave">
												<div class="sk-rect1"></div>
												<div class="sk-rect2"></div>
												<div class="sk-rect3"></div>
												<div class="sk-rect4"></div>
												<div class="sk-rect5"></div>
											</div>
										</div>

										{{-- 收入 --}}
										<canvas id="incoming" class="chart" height="60"></canvas>

									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			{{--//关键指标--}}

			{{-- 付费渗透 --}}
			<div class="col-lg-12" id="download-pay">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5>付费渗透</h5>
						<div class="ibox-tools">
							<a class="btn btn-primary btn-circle export-button" href="?export=true&fetch=pay-percent">
								<i class="fa fa-download"></i>
							</a>
						</div>
					</div>
					<div class="ibox-content">
						<div class="tabs-container">
							<ul class="nav nav-tabs">
								<li class="active">
									<a data-toggle="tab" href="#tab-pay-1" data-filled="true" data-method="pur"  data-download-area="#download-pay" data-download-link="?export=true&fetch=pay-percent&from={{ $players['display_from'] }}&to={{ $players['display_to'] }}&by=day">付费率</a>
								</li>
								<li >
									<a data-toggle="tab" href="#tab-pay-2" data-method="arpu"  data-download-area="#download-pay" data-download-link="/analyze/summarize/json?method=arpu&export=true&fetch=arpu&from={{ $players['display_from'] }}&to={{ $players['display_to'] }}&by=two_weeks">ARPU</a>
								</li>
								<li class="">
									<a data-toggle="tab" href="#tab-pay-3" data-method="arppu"  data-download-area="#download-pay" data-download-link="/analyze/summarize/json?method=arppu&export=true&fetch=arppu&from={{ $players['display_from'] }}&to={{ $players['display_to'] }}&by=two_weeks">ARPPU</a>
								</li>
								<li class="">
									<a data-toggle="tab" href="#tab-pay-4" data-method="au-avg"  data-download-area="#download-pay" data-download-link="/analyze/summarize/json?method=au-avg&export=true&fetch=au-avg&from={{ $players['display_from'] }}&to={{ $players['display_to'] }}&by=two_weeks">活跃用户平均付费</a>
								</li>
							</ul>
							<div class="tab-content">
								<div id="tab-pay-1" class="tab-pane active">
									<div class="panel-body">
										<div class="spiner-example"  style="display: none;">
											<div class="sk-spinner sk-spinner-wave">
												<div class="sk-rect1"></div>
												<div class="sk-rect2"></div>
												<div class="sk-rect3"></div>
												<div class="sk-rect4"></div>
												<div class="sk-rect5"></div>
											</div>
										</div>
										<canvas id="pur" height="60"></canvas>
									</div>
								</div>
								<div id="tab-pay-2" class="tab-pane">
									<div class="panel-body">
										<div class="spiner-example" >
											<div class="sk-spinner sk-spinner-wave">
												<div class="sk-rect1"></div>
												<div class="sk-rect2"></div>
												<div class="sk-rect3"></div>
												<div class="sk-rect4"></div>
												<div class="sk-rect5"></div>
											</div>
										</div>
										<canvas id="arpu" height="60"
										        class="chart"></canvas>
									</div>
								</div>
								<div id="tab-pay-3" class="tab-pane">
									<div class="panel-body">
										<div class="spiner-example">
											<div class="sk-spinner sk-spinner-wave">
												<div class="sk-rect1"></div>
												<div class="sk-rect2"></div>
												<div class="sk-rect3"></div>
												<div class="sk-rect4"></div>
												<div class="sk-rect5"></div>
											</div>
										</div>
										<canvas id="arppu" height="60"
										        class="chart"></canvas>
									</div>
								</div>
								<div id="tab-pay-4" class="tab-pane">
									<div class="panel-body">
										<div class="spiner-example">
											<div class="sk-spinner sk-spinner-wave">
												<div class="sk-rect1"></div>
												<div class="sk-rect2"></div>
												<div class="sk-rect3"></div>
												<div class="sk-rect4"></div>
												<div class="sk-rect5"></div>
											</div>
										</div>
										<canvas id="au-avg" height="60"
										        class="chart"></canvas>
									</div>
								</div>

							</div>
						</div>

					</div>
				</div>
			</div>
			{{--// 付费渗透--}}

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
	<!-- ChartJS-->
	<script src="{{ asset('js/plugins/chartJs/Chart.min.js') }}"></script>

	<script>
		$(document).ready(function () {
			// 异步图表基础配置
			var defaultChart = {
				labels: [],
				datasets: [
					{
						label: "Example dataset",
						fillColor: "rgba(220,220,220,0.5)",
						strokeColor: "rgba(220,220,220,1)",
						pointColor: "rgba(220,220,220,1)",
						pointStrokeColor: "#fff",
						pointHighlightFill: "#fff",
						pointHighlightStroke: "rgba(220,220,220,1)",
						data: [],
					},
					{
						label: "Example dataset",
						fillColor: "rgba(26,179,148,0.5)",
						strokeColor: "rgba(26,179,148,0.7)",
						pointColor: "rgba(26,179,148,1)",
						pointStrokeColor: "#fff",
						pointHighlightFill: "#fff",
						pointHighlightStroke: "rgba(26,179,148,1)",
						data: []
					},
					{
						label: "Example dataset",
						fillColor: "rgba(26,179,148,0.5)",
						strokeColor: "rgba(26,179,148,0.7)",
						pointColor: "rgba(26,179,148,1)",
						pointStrokeColor: "#fff",
						pointHighlightFill: "#fff",
						pointHighlightStroke: "rgba(26,179,148,1)",
						data: []
					}
				]
			};

			var defaultOptions = {
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
				responsive: true
			};

			// tab show
			$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
				var chart = $($(this).attr('href')).find('.chart');
				var chartId = chart.attr('id');
				var loading = $($(this).attr('href')).find('.spiner-example');
				var downloadArea = $(this).data('download-area');
				var downloadLink = $(this).data('download-link');
				$(downloadArea).find('.export-button').attr('href', downloadLink);
				if ($(this).data('filled')) {
					loading.fadeOut();
					chart.fadeIn();
				} else {
					var method = $(this).data('method');
					// need ajax
					$.getJSON('/analyze/summarize/json', {
						method: method,
						from: $('#from-input').val(),
						to: $('#to-input').val(),
						by: 'two_weeks'
					}, function (data) {
						var results;
						defaultChart.labels = [];
						defaultChart.datasets[0].data = [];
						defaultChart.datasets[1].data = [];

						for (results in data.result) {
							defaultChart.labels.push(data.result[results].day);
							if ('one' in data.result[results]) {
								defaultChart.datasets[0].data.push(data.result[results].one.toString());
							}
							if ('two' in data.result[results]) {
								defaultChart.datasets[1].data.push(data.result[results].two.toString());
							}
							if ('three' in data.result[results]) {
								defaultChart.datasets[2].data.push(data.result[results].three.toString());
							}
						}
						switch (method) {
							case 'active':
								defaultChart.datasets[0].label = '全部活跃: ';
								defaultChart.datasets[1].label = '新增活跃: ';
								defaultOptions.multiTooltipTemplate =  "<%= datasetLabel %> <%= value %>";
							break;

							case 'payed':
								defaultChart.datasets[1].label = '付费用户数: ';
							  defaultOptions.multiTooltipTemplate =  "<%= datasetLabel %> <%= value %>";
								defaultOptions.tooltipTemplate =  "<%= datasetLabel %> <%= value %>";
								break;

							case 'incoming':
								defaultChart.datasets[1].label = '收入: ';
							  defaultOptions.multiTooltipTemplate =  "<%= datasetLabel %> <%= value %>";
								defaultOptions.tooltipTemplate =  "<%= datasetLabel %> <%= value %>";
								break;

							case 'arpu':
								defaultChart.datasets[1].label = '活跃用户平均付费: ';
							  defaultOptions.multiTooltipTemplate =  "<%= datasetLabel %> <%= value %>";
								defaultOptions.tooltipTemplate =  "<%= datasetLabel %> <%= value %>";
								break;

							case 'arppu':
								defaultChart.datasets[1].label = '每付费用户平均收入: ';
							  defaultOptions.multiTooltipTemplate =  "<%= datasetLabel %> <%= value %>";
								defaultOptions.tooltipTemplate =  "<%= datasetLabel %> <%= value %>";
								break;
						}
//						console.log(defaultChart);
						loading.hide();
						var ctx = document.getElementById(chartId).getContext("2d");
						var myNewChart = new Chart(ctx).Line(defaultChart, defaultOptions);
						$(this).data('filled', true);
					});
				}
			});

			// datepicker
			$('.input-daterange').datepicker({
				keyboardNavigation: false,
				forceParse: false,
				autoclose: true,
				language: 'zh-CN'
			});

			// filter form
			$('.input-daterange').on('changeDate', function () {
				$('#filter-form').submit();
			});

			// bootstrap tooltip
			$('a').tooltip();

			// 在线趋势
			var lineData = {
				labels: [
					<?php $acuCount = count($players['acu_by_day']); $i = 0 ?>
							@foreach($players['acu_by_day'] as $key => $acu)
							"{{ $acu['day'] }}" @if(++$i != $acuCount), @endif
					@endforeach
				],
				datasets: [
					{
						label: "PCU: ",
						fillColor: "rgba(220,220,220,0.5)",
						strokeColor: "rgba(220,220,220,1)",
						pointColor: "rgba(220,220,220,1)",
						pointStrokeColor: "#fff",
						pointHighlightFill: "#fff",
						pointHighlightStroke: "rgba(220,220,220,1)",
						data: [
							<?php $acuCount = count($players['pcu_by_day']); $i = 0 ?>
									@foreach($players['pcu_by_day'] as $key => $acu)
									"{{ round($acu['pcu'], 0) }}" @if(++$i != $acuCount), @endif
							@endforeach
						],
					},
					{
						label: "ACU: ",
						fillColor: "rgba(26,179,148,0.5)",
						strokeColor: "rgba(26,179,148,0.7)",
						pointColor: "rgba(26,179,148,1)",
						pointStrokeColor: "#fff",
						pointHighlightFill: "#fff",
						pointHighlightStroke: "rgba(26,179,148,1)",
						data: [
							<?php $acuCount = count($players['acu_by_day']); $i = 0 ?>
									@foreach($players['acu_by_day'] as $key => $acu)
									"{{ round($acu['acu'], 0) }}" @if(++$i != $acuCount), @endif
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
				multiTooltipTemplate: "<%= datasetLabel %> <%= value %>",
			};


			var ctx = document.getElementById("acu-pcu-line").getContext("2d");
			var myNewChart = new Chart(ctx).Line(lineData, lineOptions);

			// 在线趋势横向 bar
			var barData = {
				labels: ["PCU & ACU"],
				datasets: [
					{
						label: "PCU: ",
						fillColor: "rgba(220,220,220,0.5)",
						strokeColor: "rgba(220,220,220,0.8)",
						highlightFill: "rgba(220,220,220,0.75)",
						highlightStroke: "rgba(220,220,220,1)",
						data: [{{ round($players['pcu'], 0) }}]
					},
					{
						label: "ACU: ",
						fillColor: "rgba(26,179,148,0.5)",
						strokeColor: "rgba(26,179,148,0.8)",
						highlightFill: "rgba(26,179,148,0.75)",
						highlightStroke: "rgba(26,179,148,1)",
						data: [{{ round($players['acu'], 0) }}]
					}
				]
			};

			var barOptions = {
				type: "horizontalBar",
				scaleBeginAtZero: true,
				scaleShowGridLines: true,
				scaleGridLineColor: "rgba(0,0,0,.05)",
				scaleGridLineWidth: 1,
				barShowStroke: true,
				barStrokeWidth: 1,
				barValueSpacing: 5,
				barDatasetSpacing: 1,
				responsive: true,
				multiTooltipTemplate: "<%= datasetLabel %> <%= value %>",
			}
			var ctx = document.getElementById("acu-pcu").getContext("2d");
			var myNewChart = new Chart(ctx).Bar(barData, barOptions);


			// 新增注册 新增登录
			var lineData = {
				labels: [
					<?php $regCount = count($players['login_by_day']); $i = 0 ?>
							@foreach($players['login_by_day'] as $key => $acu)
							"{{ $acu['day'] }}" @if(++$i != $regCount), @endif
					@endforeach
				],
				datasets: [
					{
						label: "注册人数: ",
						fillColor: "rgba(220,220,220,0.5)",
						strokeColor: "rgba(220,220,220,1)",
						pointColor: "rgba(220,220,220,1)",
						pointStrokeColor: "#fff",
						pointHighlightFill: "#fff",
						pointHighlightStroke: "rgba(220,220,220,1)",
						data: [
							<?php $acuCount = count($players['login_by_day']); $i = 0 ?>
									@foreach($players['login_by_day'] as $key => $acu)
									"{{ round($acu['registers'], 0) }}" @if(++$i != $acuCount), @endif
							@endforeach
						],
					},
					{
						label: "首次登录人数: ",
						fillColor: "rgba(26,179,148,0.5)",
						strokeColor: "rgba(26,179,148,0.7)",
						pointColor: "rgba(26,179,148,1)",
						pointStrokeColor: "#fff",
						pointHighlightFill: "#fff",
						pointHighlightStroke: "rgba(26,179,148,1)",
						data: [
							<?php $acuCount = count($players['login_by_day']); $i = 0 ?>
									@foreach($players['login_by_day'] as $key => $acu)
									"{{ round($acu['logins'], 0) }}" @if(++$i != $acuCount), @endif
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
				multiTooltipTemplate: "<%= datasetLabel %> <%= value %>",
			};


			var ctx = document.getElementById("registers-logins").getContext("2d");
			var myNewChart = new Chart(ctx).Line(lineData, lineOptions);

			// 付费率
			var lineData = {
				labels: [
					<?php $regCount = count($players['pur']); $i = 0 ?>
							@foreach($players['pur'] as $key => $acu)
							"{{ $acu['day'] }}" @if(++$i != $acuCount), @endif
					@endforeach
				],
				datasets: [
					{
						label: "付费率: ",
						fillColor: "rgba(220,220,220,0.5)",
						strokeColor: "rgba(220,220,220,1)",
						pointColor: "rgba(220,220,220,1)",
						pointStrokeColor: "#fff",
						pointHighlightFill: "#fff",
						pointHighlightStroke: "rgba(220,220,220,1)",
						data: [
							<?php $acuCount = count($players['pur']); $i = 0 ?>
									@foreach($players['pur'] as $key => $acu)
									"{{ round($acu['payed'], 0) }}" @if(++$i != $acuCount), @endif
							@endforeach
						],
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
				tooltipTemplate: "<%= datasetLabel %> <%= value %> %",
								scaleLabel: "<%= value + ' %' %>"
			};


			var ctx = document.getElementById("pur").getContext("2d");
			var myNewChart = new Chart(ctx).Line(lineData, lineOptions);
		});
	</script>
@endsection
