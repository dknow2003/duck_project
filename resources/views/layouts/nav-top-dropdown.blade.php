@if(app()['request']->route()->getName() != 'home')
	<style>
		.scrollable-menu {
			height: auto;
			max-height: 200px;
			overflow-x: hidden;
		}

		.first td {
			border-top: none !important;
		}

		.shouldLight {
			color: #18A689;
		}
	</style>

	<li class="dropdown">
		<a class="dropdown-toggle count-info " data-toggle="dropdown"
		   data-placement="bottom" title="当前服务器" href="#">
			<i class="fa fa-chain shouldLight"></i> <span
					class="shouldLight">{{ $menu->selectedServer() ?: '服务器' }}</span>
		</a>
		<ul class="dropdown-menu dropdown-messages scrollable-menu">
			<form action="{{ url('switch-server') }}" id="switch-server">
				<input type="hidden" name="server" id="server-id">
				<table class="table">
					<tbody>
					{{-- 如果是超级管理员或没有限制服务器 --}}
					@if(\Auth::user()->is_super || !(\Auth::user()->available_servers))
						<?php $i = 0; ?>
						@foreach($menu->servers() as $id => $server)
							@if(\Auth::user()->selected_server == $id)
								<tr class="{{ ++$i == 1 ? 'first' : '' }}">
									<td>
										<input class="i-checks switch-server" checked
										       checkedname="id" type="radio" value="{{ $id }}">
									</td>
									<td>{{ $server }}</td>
								</tr>
							@else
								<tr class="{{ ++$i == 1 ? 'first' : '' }}">
									<td>
										<input class="i-checks switch-server" name="id" type="radio"
										       value="{{ $id }}">
									</td>
									<td>{{ $server }}</td>
								</tr>
							@endif
						@endforeach
						{{-- 否则选择对当前用户可用的服务器 --}}
					@else
						<?php $i = 0; ?>
						@foreach($menu->availableServers() as $id => $server)
							<tr class="{{ ++$i == 1 ? 'first' : '' }}">
								<td>
									<input class="i-checks switch-server" name="id" type="radio"
									       value="{{ $id }}">
								</td>
								<td>{{ $server }}</td>
							</tr>
						@endforeach
					@endif
					</tbody>
				</table>
			</form>

		</ul>
	</li>
@endif
