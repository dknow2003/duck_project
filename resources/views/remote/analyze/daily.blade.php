@extends('layouts.app')

@section('content-top')


  <div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
      <h2>月度汇总 {{-- ({{ $orders->total() ?: 0 }}) --}} </h2>
    </div>
  </div>

  <div class="wrapper wrapper-content">
    <div class="row">

      <div class="col-lg-12 animated fadeInRight">
        <div class="ibox float-e-margins">


          placeholder 占位符

          {{-- search --}}
          <div class="row" style="padding-right: 15px;">
            {{--<form action="{{ url('game/activecards') }}" method="GET" id="search-form">--}}
            {{--<div class="col-sm-3">--}}
            {{--<div class="input-group"  style="margin: 20px 0;">--}}
            {{--<input type="text" class="input-sm form-control" name="code" value="{{ \Request::get('code') }}" id="search-input" placeholder="卡号">--}}
            {{--<div class="input-group-btn">--}}
            {{--<button  class="btn btn-primary btn-sm " type="submit">搜索 </button>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</form>--}}
            {{--<div class="pull-right">{{ $orders->links() }}</div>--}}
          </div>
          {{--<div class="row">--}}

          {{--<style>--}}
          {{--.table-striped a {--}}
          {{--color: #333;--}}
          {{--}--}}
          {{--</style>--}}
          {{--<div class="table-responsive">--}}
          {{--<table class="table table-striped">--}}
          {{--<thead>--}}
          {{--<tr>--}}
          {{--<th>用户 ID</th>--}}
          {{--<th>角色 ID</th>--}}
          {{--<th>payid</th>--}}
          {{--<th>订单金额</th>--}}
          {{--<th>订单状态</th>--}}
          {{--<th>支付状态</th>--}}
          {{--</tr>--}}
          {{--</thead>--}}
          {{--<tbody>--}}


          {{--@foreach($orders as $order)--}}
          {{--<tr>--}}

          {{--<td>{{ $order->aid }}</td>--}}
          {{--<td>{{ $order->pid }}</td>--}}
          {{--<td>{{ $order->goodsId }}</td>--}}
          {{--<td>{{ $order->orderMoney }}</td>--}}
          {{--<td>{{ $order->orderStatus }}</td>--}}
          {{--<td>{{ $order->payStatus }}</td>--}}

          {{--</tr>--}}
          {{--@endforeach--}}

          {{--</tbody>--}}
          {{--</table>--}}
          {{--</div>--}}

          {{--</div>--}}
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
    });
  </script>
@endsection
