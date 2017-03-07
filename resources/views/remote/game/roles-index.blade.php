@extends('layouts.app')

@section('content-top')

	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-12">
			<h2>{{ $menu->getActiveName() }} ({{ $roles->total() ?: 0 }})</h2>
		</div>
	</div>

	<div class="wrapper wrapper-content">
		<div class="row">

			<div class="col-lg-12 animated fadeInRight">
				<div class="ibox float-e-margins">

					<div class="row">
						{{-- order by --}}
						<div class="col-sm-2 m-b" style="margin: 20px 0;">
							<div data-toggle="buttons" class="btn-group">
								<label
										class="btn btn-sm btn-white order-button {{ \Request::get('order') != 'crystal' && \Request::get('order') != 'level' ? ' active' : '' }}">
									<input type="radio" id="option1" name="order" value="time">
									创建时间
								</label>
								<label
										class="btn btn-sm btn-white order-button {{ \Request::get('order') == 'crystal' ? ' active' : '' }}">
									<input type="radio" id="option2" name="order" value="crystal">
									水晶 </label>
								<label
										class="btn btn-sm btn-white order-button {{ \Request::get('order') == 'level' ? ' active' : '' }}">
									<input type="radio" id="option3" name="order" value="level">
									等级 </label>
							</div>
						</div>

						{{-- search --}}
						<?php
						$appends = [];
						if (\Request::has('user_id')) {
							$appends = array_merge($appends, ['user_id' => \Request::get('user_id')]);
						}
						if (\Request::has('role_name')) {
							$appends = array_merge($appends, ['role_name' => \Request::get('role_name')]);
						}
						if (\Request::has('order')) {
							$appends = array_merge($appends, ['order' => \Request::get('order')]);
						}

						$inputValue = '';
						if ($roleName = \Request::get('role_name')) {
							$inputValue = $roleName;
						} elseif ($userId = \Request::get('user_id'))
						{
							$inputValue = $userId;
						}
						?>
						<form action="{{ url('game/roles') }}" method="GET"
						      id="search-form">
							<input type="hidden" id="order-input" name="order"
							       value="{{ \Request::get('order') }}">
							<div class="col-sm-2" style="margin: 20px 0;">
								<div class="input-group">
									<input type="text" class="input-sm form-control"
									       name=""
									       value="{{ $inputValue }}"
									       id="search-input" placeholder="ID 或角色名">
									<div class="input-group-btn">
										<button data-toggle="dropdown"
										        class="btn btn-primary btn-sm dropdown-toggle"
										        type="submit">搜索 <span class="caret"></span>
										</button>
										<ul class="dropdown-menu pull-right" id="search-button">
											<li><a href="#" data-search-type="user_id">ID</a></li>
											<li><a href="#" data-search-type="role_name">角色名</a></li>
										</ul>
									</div>
								</div>
							</div>
						</form>


						<div class="col-sm-8" style="padding-right: 15px;">
							<div
									class="pull-right">{{ $roles->appends($appends)->links() }}</div>
						</div>
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
									<th>角色名</th>
									<th>性别</th>
									<th>等级</th>
									<th>装备物品</th>
									<th>金币</th>
									<th>水晶</th>
									<th>经验值</th>
									<th>奖杯数</th>
									<th>创建时间</th>
								</tr>
								</thead>
								<tbody>


								@foreach($roles as $role)
									<tr>
										<td><a
													href="{{ url('game/roles/' . $role->UserID) }}">{{ $role->UserID }}</a>
										</td>
										<td><a
													href="{{ url('game/roles/' . $role->UserID) }}">{{ $role->RoleName }}</a>
										</td>
										<td>
											@if($role->Gender == 1)
												男
											@elseif($role->Gender ==2)
												女
											@else
												未知
											@endif
										</td>

										{{--<td><span class="pie">4,9</span></td>--}}
										<td>{{ $role->Level }}</td>
										<td><a href="{{ url('game/roles/' . $role->UserID) }}">物品和装备</a></td>
										<td>{{ number_format($role->Gold) }}</td>
										<td>
                    <span class="{{ $role->Crystal ? 'crystal' : '' }}"
                          data-placement="top"
                          data-original-title="水晶：{{ $role->Crystal }}<br>绑定水晶：{{ $role->BindingCrystal }}"
                          rel="tooltip"><span class="pie">{{ $role->Crystal }}/{{ $role->Crystal + $role->BindingCrystal }}</span> {{ $role->Crystal + $role->BindingCrystal }} </span>
										</td>
										<td>{{ $role->Exp }}</td>
										<td>{{ $role->Cup }}</td>
										<td>{{ $role->CreateTime }}</td>
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
	<script src="{{ asset('js/plugins/peity/jquery.peity.min.js') }}"></script>

	<script>
		// search
		$(function () {
			// 搜索
			$('#search-button a').click(function (e) {
				var type = $(this).data('search-type');
				if ($('#search-input').val()) {
					$('#search-input').attr('name', type);
					$('#search-form').submit();
				}
				e.preventDefault();
			});
			// 排序
			$('.order-button').click(function (e) {
				var order = $(this).find('input').val();
				$('#order-input').val(order);
				$('#search-form').submit();
			})
		});

		//peity
		$(function () {
			$("span.pie").peity("pie", {
				fill: ['#1ab394', '#d7d7d7', '#ffffff']
			});

			$(".line").peity("line", {
				fill: '#1ab394',
				stroke: '#169c81',
			});

			$(".bar").peity("bar", {
				fill: ["#1ab394", "#d7d7d7"]
			});

			$(".bar_dashboard").peity("bar", {
				fill: ["#1ab394", "#d7d7d7"],
				width: 100
			});

			var updatingChart = $(".updating-chart").peity("line", {
				fill: '#1ab394',
				stroke: '#169c81',
				width: 64
			})

			setInterval(function () {
				var random = Math.round(Math.random() * 10);
				var values = updatingChart.text().split(",");
				values.shift();
				values.push(random);

				updatingChart
						.text(values.join(","))
						.change()
			}, 1000);


		});

		// tooltip
		$(document).ready(function () {
			$('.crystal').tooltip({html: true});
		});
	</script>
@endsection
