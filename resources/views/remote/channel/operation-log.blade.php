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

						if (\Request::has('operation_class')) {
							$appends = array_merge($appends, ['operation_class' => \Request::get('operation_class')]);
						}

						$inputValue = '';
						if ($roleName = \Request::get('user_id')) {
							$inputValue = $roleName;
						}
						?>
						<form action="{{ url('channel/operation-log') }}" method="GET"
						      id="search-form">
							<div class="col-sm-2" style="padding-top: 15px;">
								<div class="input-group" style="margin: auto 0;">
									<input type="text" class="input-sm form-control"
									       value="{{ $inputValue }}" id="search-input"
									       placeholder="玩家 ID" name="user_id">
									<div class="input-group-btn">
										<button id="form-submit" class="btn btn-primary btn-sm "
										        type="submit">搜索
										</button>
									</div>
								</div>
							</div>
							<div class="col-sm-2" style="padding-top: 15px;">
								<select class="form-control " name="operation_class"
								        id="operation-list" style="margin: auto 0;">
									<option value="">操作分类</option>
									@foreach($operations as $operationKey => $operation)
										<option
												value="{{ $operationKey }}" {{ $operationKey == \Request::get('operation_class') ? ' selected' : '' }}>{{ $operation }}</option>
									@endforeach
								</select>
							</div>
							<div
									class="col-sm-8 text-right">{{ $logs->appends($appends)->links() }}</div>
					</div>
					</form>
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
									<th>操作分类</th>
									<th>动作</th>
									<th>首次操作时间</th>
									<th>最后操作时间</th>
									<th>最后一次统计数字</th>
									<th>日统计</th>
									<th>周统计</th>
									<th>月统计</th>
									<th>年统计</th>
									<th>总统计数</th>

								</tr>
								</thead>
								<tbody>

								@foreach($logs as $log)
									<tr>
										<td><a data-toggle="tooltip" data-placement="top"
										       title="查看该帐号所有操作"
										       href="{{ url('channel/operation-log?user_id=' .$log->UserID) }}">{{ $log->UserID }}</a>
										</td>
										<td><a data-toggle="tooltip" data-placement="top"
										       title="查看该角色所有操作"
										       href="{{ url('channel/operation-log?user_id=' .$log->UserID) }}">{{ $log->role->RoleName or '未定义' }}</a>
										</td>
										<td>{{ \App\Http\Controllers\Remote\Channel\ChannelController::getOperationNameOrValue($log->OperClass) }}</td>
										<td>{{ \App\Http\Controllers\Remote\Channel\ChannelController::getOperationNameOrValue($log->OperAct) }}</td>
										<td>{{ $log->FirstTime }}</td>
										<td>{{ $log->LastTime }}</td>
										<td>{{ $log->LastStatNum }}</td>
										<td>{{ $log->DayStatNum }}</td>
										<td>{{ $log->WeekStatNum }}</td>
										<td>{{ $log->MonthStatNum }}</td>
										<td>{{ $log->YearStatNum }}</td>
										<td>{{ $log->TotalStatNum }}</td>
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
			$('#form-submit').click(function (e) {
				if ($('#search-input').val()) {
					$('#search-form').submit();
				}
				e.preventDefault();
			});
			$('#operation-list').on('change', function (e) {
					$('#search-form').submit();
			});

		});
	</script>
@endsection
