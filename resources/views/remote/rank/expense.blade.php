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
					<?php
					$appends = [];
					if (\Request::has('channel_id')) {
						$appends = array_merge($appends, ['channel_id' => \Request::get('channel_id')]);
					}
					?>
					<div class="col-sm-4">
						<form action="" id="select-form">
							<select class="form-control m-b" name="channel_id" id="select-list">
								<option value="">选择渠道</option>
								@foreach($channelList as $channel)
									<?php
									$selected = false;
									if (\Request::get('channel_id') == $channel->channel_id) {
										$selected = true;
									}
									?>
									<option	value="{{ $channel->channel_id }}" @if($selected) selected="true"@endif >{{ $channel->name }}</option>
								@endforeach
							</select>
						</form>
					</div>
					<div class="col-sm-8 text-right">{{ $orders->appends($appends)->links() }}</div>
				</div>

			</div>
		</div>
		<div class="row">

			<div class="col-lg-12 animated fadeInRight">
				<div class="ibox float-e-margins">
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
									<th>帐号 ID</th>
									<th>角色</th>
									<th>消费钻石</th>
									<th>消费次数</th>
								</tr>
								</thead>
								<tbody>

								<?php $i = 0; ?>
								@foreach($orders as $order)
									<tr>
										<td>{{ ($orders->currentPage() - 1) * 10 + (++$i)}}</td>
										<td>{{ $order->UserID }}</td>
										<td>{{ $order->role->RoleName or '' }}</td>
										<td>{{ $order->money }}</td>
										<td>{{ $order->count }}</td>
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
			// select
			$('#select-list').on('change', function (e) {
				if($(this).val()){
					$('#select-form').submit();
				}
			});
		});
	</script>
@endsection
