@extends('layouts.app')

@section('content-top')

	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-12">
			<h2>货币流水 ({{ $currencies->total() ?: 0 }})</h2>
		</div>
	</div>

	<div class="wrapper wrapper-content">
		<div class="row">

			<div class="col-lg-12 animated fadeInRight">
				<div class="ibox float-e-margins">
					<div class="row" style="padding-right: 15px;">
						{{-- search --}}
						<?php
						$appends = [];
						if (\Request::has('role_name')) {
							$appends = array_merge($appends, ['role_name' => \Request::get('role_name')]);
						}
						if (\Request::has('role_id')) {
							$appends = array_merge($appends, ['role_id' => \Request::get('role_id')]);
						}
						if (\Request::has('remark')) {
							$appends = array_merge($appends, ['remark' => \Request::get('remark')]);
						}
						?>
						<form action="{{ url('game/currencies') }}" method="GET" id="search-form">
						<div class="col-sm-2" style="padding: 20px 0;">
							<?php
							$inputValue = '';
							if ($roleName = \Request::get('role_name')) {
								$inputValue = $roleName;
							} elseif ($roleId = \Request::get('role_id'))
							{
								$inputValue = $roleId;
							}
							?>
								<input type="hidden" id="search-hidden" name=""
								       value="{{ $inputValue }}">
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
						<div class="col-sm-2" style="padding-top: 15px;">
							<select class="form-control " name="remark"
							        id="remark-list" style="margin: auto 0;">
								<option value="">操作分类</option>
								@foreach($remarks as $remarkKey => $remark)
									<option
											value="{{ $remarkKey }}" {{ $remarkKey == \Request::get('remark') ? ' selected' : '' }}>{{ $remark }}</option>
								@endforeach
							</select>
						</div>
						</form>
						{{--// search--}}
						<div class="col-sm-8 text-right">{{ $currencies->appends($appends)->links() }}</div>
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
									<th>操作类型</th>
									<th>货币类型</th>
									<th>操作的货币数</th>
									<th>操作后的货币数</th>
									<th>备注</th>
									<th>记录时间</th>
								</tr>
								</thead>
								<tbody>


								@foreach($currencies as $currency)
									<tr>
										<td>{{ $currency->UserID }}</td>
										<td>{{ $currency->role->RoleName or '未定义' }}</td>
										<td>{{ $currency->presentOperType($currency->OperType) }}</td>
										<td>{{ $currency->presentMoneyType($currency->MoneyType) }}</td>
										<td>{{ number_format($currency->MoneyChanged) }}</td>
										<td>{{ number_format($currency->MoneyLeft) }}</td>
										<td>{{ $currency->presentRemark($currency->Remark) }}</td>
										<td>{{ $currency->LogTime }}</td>

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

			$('#remark-list').on('change', function (e) {
					$('#search-form').submit();
			});

		});
	</script>
@endsection
