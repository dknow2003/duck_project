@extends('layouts.app')

@section('content-top')


	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-12">
			<h2>{{ $menu->getActiveName() }} ({{ $orders->total() ?: 0 }})</h2>
		</div>
	</div>

	<div class="wrapper wrapper-content">
		<div class="row">

			<div class="col-lg-12 animated fadeInRight">
				<div class="ibox float-e-margins">

					{{-- search --}}
					<div class="row" style="padding-right: 15px;">
						<?php
						$appends = [];
						if (\Request::has('channel_id')) {
							$appends = array_merge($appends, ['channel_id' => \Request::get('channel_id')]);
						}

						$inputValue = '';
						if ($roleName = \Request::get('channel_id')) {
							$inputValue = $roleName;
						} elseif ($roleId = \Request::get('channel_name'))
						{
							$inputValue = $roleId;
						}
						?>
						<div class="col-sm-4" style="padding: 20px 0;">
							<form action="{{ url('channel-comparison/pay-rank') }}" method="GET"
							      id="search-form">
								<input type="hidden" id="search-hidden" name="order"
								       value="{{ $inputValue }}">
								<div class="input-group">
									<input type="text" class="input-sm form-control"
									       value="{{ $inputValue }}"
									       id="search-input" placeholder="渠道名称或 ID">
									<div class="input-group-btn">
										<button data-toggle="dropdown"
										        class="btn btn-primary btn-sm dropdown-toggle"
										        type="submit">搜索 <span class="caret"></span>
										</button>
										<ul class="dropdown-menu pull-right" id="search-button">
											<li><a href="#" data-search-type="channel_id">渠道 ID</a></li>
											<li><a href="#" data-search-type="channel_name">渠道名</a></li>
										</ul>
									</div>
								</div>
							</form>
						</div>
						<div class="col-sm-8 text-right">{{ $orders->appends($appends)->links() }}</div>
					</div>
					{{-- // search --}}

					<div class="row">
						<style>
							.table-striped a {
								color: #333;
							}
						</style>
						<div class="table-responsive">
							<table class="table table-striped">
								<thead>
								<tr>
									<th>排名</th>
									<th>渠道 ID</th>
									<th>渠道名称</th>
									<th>充值总额</th>
									<th>订单笔数</th>
								</tr>
								</thead>
								<tbody>

								<?php $i = 0; ?>
								@foreach($orders as $order)
									<tr>
										<td>{{ ++$i }}</td>
										<td><a data-toggle="tooltip" data-placement="top" title="查看该渠道所有充值" href="{{ url('channel-comparison/pay-rank?channel_id=' . $order->channelId) }}">{{ $order->channelId}}</a></td>
										<td><a data-toggle="tooltip" data-placement="top" title="查看该渠道所有充值" href="{{ url('channel-comparison/pay-rank?channel_id=' . $order->channelId) }}">{{ isset($order->channel) && isset($order->channel->name) && !empty($order->channel) ? $order->channel->name : '未定义' }}</a></td>
										<td>{{ $order->money }}</td>
										<td>{{ $order->count}}</td>
									</tr>
								@endforeach

								</tbody>
							</table>
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

	<script>
		$(document).ready(function () {
			$('a').tooltip();

			// search
			$('#search-button a').click(function (e) {
				var type = $(this).data('search-type');
				if ($('#search-input').val()) {
					$('#search-hidden').attr('name', type).val($('#search-input').val());
					$('#search-form').submit();
				}
				e.preventDefault();
			});
		});
	</script>
@endsection
