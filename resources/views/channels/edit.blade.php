@extends('layouts.app')

@section('content-top')

  <div class="wrapper wrapper-content">
    <div class="row">
      <div class="col-lg-12 animated fadeInRight">
      <div class="mail-box-header">
        <h2>
          {{ trans('修改渠道') }}
        </h2>
      </div>
      <div class="mail-box">


        {!! Form::model($channel, ['route' => ['admin.channels.update', $channel->id],'method' => 'PUT', 'class' => 'form-horizontal']) !!}

        @include('channels.form', ['submitName' => '修改渠道'])

        <div class="clearfix"></div>
        {!! Form::close() !!}

      </div>
    </div>

    </div>
  </div>

@endsection

@section('style')
  <link href="{{ asset('css/plugins/switchery/switchery.css') }}"
        rel="stylesheet">
@endsection

@section('script')
  <!-- Switchery -->
  <script src="{{ asset('js/plugins/switchery/switchery.js') }}"></script>
  <script>
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

    elems.forEach(function (html) {
      var switchery = new Switchery(html, {color: '#1AB394'});
    });
  </script>
@endsection
