@extends('layouts.app')

@section('content-top')

  <div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
      <h2>{{ $menu->getActiveName() }}</h2>
    </div>
  </div>

  <div class="wrapper wrapper-content">
    <div class="row">

      <div class="col-lg-12 animated fadeInRight">
        <div class="ibox float-e-margins">


          {{-- search --}}
          <form method="GET" action="{{ url('online/new') }}" id="fetch-form">
            <input type="hidden" name="fetch"
                   value="{{ $fetch = \Request::get('fetch') }}" id="fetch-input">
            <div class="row" style="">
              <div class="col-sm-12">
                <p class="font-bold">最近</p>
                <div data-toggle="buttons" class="btn-group"
                     style="margin: 20px 0;">

                  <label class="btn btn-sm btn-white fetch-button {{ !$fetch || $fetch == "all" ? ' active' : ''}}">
                    <input type="radio" id="option1" name="" value="all">所有
                  </label>

                  <label class="btn btn-sm btn-white fetch-button {{ $fetch == "year" ? ' active' : ''}}">
                    <input type="radio" id="option1" name="" value="year">年
                  </label>

                  <label class="btn btn-sm btn-white fetch-button {{ $fetch == "month" ? ' active' : ''}}">
                    <input type="radio" id="option1" name="" value="month">月
                  </label>

                  <label class="btn btn-sm btn-white fetch-button {{ $fetch == "week" ? ' active' : ''}}">
                    <input type="radio" id="option1" name="" value="week">周
                  </label>

                  <label class="btn btn-sm btn-white fetch-button {{ $fetch == "day" ? ' active' : ''}}">
                    <input type="radio" id="option1" name="" value="day">天
                  </label>
                </div>
              </div>
            </div>
          </form>


          <div class="row">
            <div class="ibox-content">
              <div id="morris-line-chart"></div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

@endsection

@section('style')

  <!-- morris -->
  <link href="{{ asset('css/plugins/morris/morris-0.4.3.min.css') }}"
        rel="stylesheet">
@endsection

@section('script')

  <!-- Morris -->
  <script src="{{ asset('js/plugins/morris/raphael-2.1.0.min.js') }}"></script>
  <script src="{{ asset('js/plugins/morris/morris.js') }}"></script>

  <script>
    $(document).ready(function () {

      // 点击选择按什么排序
      $('.fetch-button').click(function (e) {
        var type = $(this).find('input').val();
        $('#fetch-input').val(type);
        $('#fetch-form').submit();
      });

      <!-- Morris -->
      Morris.Line({
        element: 'morris-line-chart',
        data: [
            @foreach($newPlayers as $newPlayer)
          {
            x: '{{ $newPlayer->x }}', y: parseInt({{ $newPlayer->avg }})
          },
          @endforeach
        ],
        xkey: 'x',
        ykeys: 'y',
        postUnits: ' 人',
        parseTime: false,
        resize: true,
        lineWidth: 4,
        labels: ['注册'],
        lineColors: ['#1ab394'],
        pointSize: 5,
        gridTextSize: 13,
        continuousLine: false
      });

    });
  </script>
@endsection
