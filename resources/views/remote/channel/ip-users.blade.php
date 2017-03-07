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
					{{-- search --}}
					<div class="row" style="padding-right: 15px;">
						<?php
						$appends = [];
						if (\Request::has('ip')) {
							$appends = array_merge($appends, ['ip' => \Request::get('ip')]);
						}

						$inputValue = '';
						if ($roleName = \Request::get('ip')) {
							$inputValue = $roleName;
						}
						?>
						<div class="col-sm-4" style="padding: 20px 0;">
							<form action="{{ url('channel/ip-users') }}" method="GET"
							      id="search-form">
								<div class="input-group">
									<input type="text" class="input-sm form-control"
									       value="{{ $inputValue }}"
									       id="search-input" placeholder="IP" name="ip">
									<div class="input-group-btn">
										<button id="form-submit" class="btn btn-primary btn-sm " type="submit">搜索
										</button>
									</div>
								</div>
							</form>
						</div>
						<div class="col-sm-8 text-right">{{ $loginLogs->appends($appends)->links() }}</div>
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
										<td><a data-toggle="tooltip" data-placement="top" title="查看该用户登录日志" href="{{ url('channel/login-log?user_id=' .$loginLog->UserID) }}">{{ $loginLog->UserID}}</a></td>
										<td><a data-toggle="tooltip" data-placement="top" title="查看该角色登录日志" href="{{ url('channel/login-log?user_id=' .$loginLog->UserID) }}">{{ $loginLog->role->RoleName or '无角色' }}</a></td>
										<td>{{ $loginLog->LoginTime }}</td>
										<td><a data-toggle="tooltip" data-placement="top" title="查看 {{ $loginLog->LoginIP }} 所登录的帐号" href="{{ url('channel/ip-users?ip=' .$loginLog->LoginIP) }}">{{ $loginLog->LoginIP}}</a></td>
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
