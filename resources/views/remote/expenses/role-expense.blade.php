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
						if (\Request::has('role_name')) {
							$appends = array_merge($appends, ['role_name' => \Request::get('role_name')]);
						}
						if (\Request::has('role_id')) {
							$appends = array_merge($appends, ['role_id' => \Request::get('role_id')]);
						}
						?>
						<div class="col-sm-4" style="padding: 20px 0;">
							<?php
							$inputValue = '';
							if ($roleName = \Request::get('role_name')) {
								$inputValue = $roleName;
							} elseif ($roleId = \Request::get('role_id'))
							{
								$inputValue = $roleId;
							}
								?>
							<form action="{{ url('expense/roles-expense') }}" method="GET"
							      id="search-form">
								<input type="hidden" id="search-hidden" name=""
								       value="{{ $inputValue }}">
							</form>
								<div class="input-group">
									<input type="text" class="input-sm form-control"
									       name="{{ \Request::has('id') ? 'id' : 'rolename' }}"
									       value="{{ $inputValue }}"
									       id="search-input" placeholder="ID 或角色名">
									<div class="input-group-btn">
										<button data-toggle="dropdown"
										        class="btn btn-primary btn-sm dropdown-toggle"
										        type="submit">搜索 <span class="caret"></span>
										</button>
										<ul class="dropdown-menu pull-right" id="search-button">
											<li><a href="#" data-search-type="role_id">角色 ID</a></li>
											<li><a href="#" data-search-type="role_name">角色名</a></li>
										</ul>
									</div>
								</div>
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
									<th>角色 ID</th>
									<th>角色</th>
									<th>消费钻石</th>
									<th>消费后剩余</th>
									<th>消费时间</th>
								</tr>
								</thead>
								<tbody>


								@foreach($orders as $order)
									<tr>

										<td><a data-toggle="tooltip" data-placement="top" title="查看该角色所有消费" href="{{ url('expense/roles-expense?role_id=' . $order->UserID) }}">{{ $order->UserID }}</a></td>
										<td><a data-toggle="tooltip" data-placement="top" title="查看该角色所有消费" href="{{ url('expense/roles-expense?role_id=' . $order->UserID) }}">{{ $order->role->RoleName }}</a></td>
										<td>{{ number_format($order->MoneyChanged) }}</td>
										<td>{{ number_format($order->MoneyLeft) }}</td>
										<td>{{ $order->LogTime }}</td>

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
