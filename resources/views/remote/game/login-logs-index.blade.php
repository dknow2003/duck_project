@extends('layouts.app')

@section('content-top')


	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-12">
			<h2>{{ $menu->getActiveName() }} {{ isset($channel->name) ? " ({$channel->name})" : ''}}</h2>
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
						if (\Request::has('user_id')) {
							$appends = array_merge($appends, ['user_id' => \Request::get('user_id')]);
						}
						if (\Request::has('role_name')) {
							$appends = array_merge($appends, ['role_name' => \Request::get('role_name')]);
						}
						if (\Request::has('from')) {
							$appends = array_merge($appends, ['from' => \Request::get('from')]);
						}
						if (\Request::has('to')) {
							$appends = array_merge($appends, ['to' => \Request::get('to')]);
						}

						$inputValue = '';
						if ($roleName = \Request::get('role_name')) {
							$inputValue = $roleName;
						} elseif ($userID = \Request::get('user_id'))
						{
							$inputValue = $userID;
						}
						?>
							<form action="{{ url('game/login-logs') }}" method="GET"
							      id="search-form">
						<div class="col-sm-2" style="padding: 20px 0;">
								<input type="hidden" id="search-hidden" name="order"
								       value="{{ $inputValue }}">
								<div class="input-group">
									<input type="text" class="input-sm form-control"
									       value="{{ $inputValue }}"
									       id="search-input" placeholder="用户 ID 或角色名">
									<div class="input-group-btn">
										<button data-toggle="dropdown"
										        class="btn btn-primary btn-sm dropdown-toggle"
										        type="submit">搜索 <span class="caret"></span>
										</button>
										<ul class="dropdown-menu pull-right" id="search-button">
											<li><a href="#" data-search-type="user_id">用户 ID</a></li>
											<li><a href="#" data-search-type="role_name">角色名</a></li>
										</ul>
									</div>
								</div>
						</div>
								<div class="col-sm-4" style="padding:20px 0 20px 20px;">

									<div class="input-daterange input-group">
										<input id="from-input" type="text"
										       class="input-sm form-control active" name="from"
										       value="{{ \Request::get('from', '') }}" placeholder="开始时间">
										<span class="input-group-addon"> 到 </span>
										<input id="to-input" type="text"
										       class="input-sm form-control active" name="to"
										       value="{{ \Request::get('to', '') }}" placeholder="结束时间">
									</div>
								</div>
							</form>
							<div class="col-sm-6 text-right">{{ $loginLogs->appends($appends)->links() }}</div>
					</div>
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
									<th>玩家 ID</th>
									<th>角色</th>
									<th>登陆时间</th>
									<th>登陆 IP</th>
									<th>登出时间</th>
									<th>备注</th>
								</tr>
								</thead>
								<tbody>

								@foreach($loginLogs as $loginLog)
									<tr>
										<td><a data-toggle="tooltip" data-placement="top" title="查看该用户登录日志" href="{{ url('game/login-logs?user_id=' .$loginLog->UserID) }}">{{ $loginLog->UserID}}</a></td>
										@if( empty($loginLog->role) )
											<td><a data-toggle="tooltip" data-placement="top" title="查看该角色登录日志" href="{{ url('game/login-logs?user_id=' .$loginLog->UserID) }}"> 警告:查不到角色信息  </a></td>
										@else
											<td><a data-toggle="tooltip" data-placement="top" title="查看该角色登录日志" href="{{ url('game/login-logs?user_id=' .$loginLog->UserID) }}">{{ $loginLog->role->RoleName}}</a></td>
										@endif

										<td>{{ $loginLog->LoginTime }}</td>
										<td>{{ $loginLog->LoginIP }}</td>
										<td>{{ $loginLog->LogoutTime }}</td>
										<td>{{ $loginLog->Remark }}</td>
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
			// datepicker
			$('.input-daterange').datepicker({
				keyboardNavigation: false,
				forceParse: false,
				autoclose: true,
				language: 'zh-CN',
				format: 'yyyy-mm-dd'
			});

			// filter form
			$('.input-daterange').on('changeDate', function () {
				$('#search-form').submit();
			});
		});
	</script>
@endsection
