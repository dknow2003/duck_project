@extends('layouts.app')

@section('content-top')

	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-12">
			<h2>{{ $menu->getActiveName() }} - ({{ $role->RoleName }})</h2>
		</div>
	</div>

	<div class="wrapper wrapper-content">
		<div class="row">

			<div class="col-lg-12 animated fadeInRight">
				<div class="ibox float-e-margins">

					{{-- 角色详情--}}
					<div class="row">
						<style>
							.table-striped a {
								color: #333;
							}
						</style>
						<table class="table table-bordered">
							<thead>
							<tr>
								<th colspan="4">角色详情</th>
							</tr>
							</thead>
							<tbody>
							<?php $i = 0;?>
							<tr>
								@foreach($role->mappedComment as $k => $v)
									<td style="border-right: none;">{{ $k }}</td>
									<td style="border-left: none;">{{ $v }}</td>
									@if(++$i % 2 == 0)</tr><tr> @endif
								@endforeach
							</tr>
							</tbody>
						</table>
					</div>
					{{--// 角色详情--}}
					{{-- 角色物品--}}
					<div class="row">
						<table class="table">
							<thead>
							<tr>
								<th>玩家 ID</th>
								<th>道具 ID</th>
								<th>数量</th>
								<th>唯一标识</th>
								<th>失效时间</th>
							</tr>
							</thead>
							<tbody>
							@foreach($goodsList as $goods)
								<tr>
									<td>{{ $goods->UserID }}</td>
									<td>{{ $goods->GoodsID }}</td>
									<td>{{ $goods->Num }}</td>
									<td>{{ $goods->UniqueID }}</td>
									<td>{{ $goods->ExpireTime }}</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
					{{--// 角色物品--}}

					{{-- 角色装备--}}
					<div class="row">
						<table class="table">
							<thead>
							<tr>
								<th>玩家 ID</th>
								<th>装备 ID</th>
								<th>装备唯一 ID</th>
								<th>强化等级</th>
								<th>品阶</th>
								<th>技能列表</th>
								<th>装备该装备的英雄</th>
								<th>生成时间</th>
							</tr>
							</thead>
							<tbody>
							@foreach($equipmentsList as $equipments)
								<tr>
									<td>{{ $equipments->UserID }}</td>
									<td>{{ $equipments->EquipID }}</td>
									<td>{{ $equipments->UniqueID }}</td>
									<td>{{ $equipments->Level }}</td>
									<td>{{ $equipments->Quality }}</td>
									<td>{{ $equipments->SkillList }}</td>
									<td>{{ $equipments->EquipedHeroId }}</td>
									<td>{{ $equipments->CreateTime }}</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
					{{--// 角色装备--}}

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
