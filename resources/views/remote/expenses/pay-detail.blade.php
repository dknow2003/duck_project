@extends('layouts.app')

@section('content-top')


	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-12">
			<h2>订单 ({{ $orders->total() ?: 0 }})</h2>
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
						if (\Request::has('aid')) {
							$appends = array_merge($appends, ['aid' => \Request::get('aid')]);
						}
						if (\Request::has('serial')) {
							$appends = array_merge($appends, ['serial' => \Request::get('serial')]);
						}

						$inputValue = '';
						if ($roleName = \Request::get('aid')) {
							$inputValue = $roleName;
						} elseif ($roleId = \Request::get('serial'))
						{
							$inputValue = $roleId;
						}
						?>
						<div class="col-sm-4" style="padding: 20px 0;">
							<form action="{{ url('expense/pay-detail') }}" method="GET"
							      id="search-form">
								<input type="hidden" id="search-hidden" name="order"
								       value="{{ $inputValue }}">
								<div class="input-group">
									<input type="text" class="input-sm form-control"
									       value="{{ $inputValue }}"
									       id="search-input" placeholder="用户 ID 或订单号">
									<div class="input-group-btn">
										<button data-toggle="dropdown"
										        class="btn btn-primary btn-sm dropdown-toggle"
										        type="submit">搜索 <span class="caret"></span>
										</button>
										<ul class="dropdown-menu pull-right" id="search-button">
											<li><a href="#" data-search-type="aid">用户 ID</a></li>
											<li><a href="#" data-search-type="serial">订单号</a></li>
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
									<th>用户 ID</th>
									<th>角色名</th>
									<th>订单号</th>
									<th>payid</th>
									<th>订单金额</th>
									<th>订单状态</th>
									<th>支付状态</th>
									<th>创建时间</th>
								</tr>
								</thead>
								<tbody>


								@foreach($orders as $order)
									<tr>
										<td><a data-toggle="tooltip" data-placement="top" title="查看该用户所有充值" href="{{ url('expense/pay-detail?aid=' . $order->aid) }}">{{ $order->aid}}</a></td>
										<td>{{ isset($order->role) ? $order->role->RoleName : '未定义' }}</td>
										<td>{{ $order->orderSerial }}</td>
										<td>{{ $order->goodsId }}</td>
										<td>{{ $order->orderMoney }}</td>
										<td>{{ $order->orderStatus }}</td>
										<td>{{ $order->payStatus }}</td>
										<td>{{ $order->createTime }}</td>

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
