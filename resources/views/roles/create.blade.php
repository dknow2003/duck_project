@extends('layouts.app')

@section('content-top')

  <div class="wrapper wrapper-content">
    <div class="row">
      <div class="col-lg-12 animated fadeInRight">
        <div class="mail-box-header">
          {{--<div class="pull-right tooltip-demo">--}}
          {{--<a href="mailbox.html" class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="top" title="Move to draft folder"><i class="fa fa-pencil"></i> Draft</a>--}}
          {{--<a href="mailbox.html" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Discard email"><i class="fa fa-times"></i> Discard</a>--}}
          {{--</div>--}}
          <h2>
            {{ trans('role.create') }}
          </h2>
        </div>
        <div class="mail-box">

          {!! Form::open(['url' => url('admin/roles'), 'method' => 'POST', 'class' => 'form-horizontal']) !!}

          @include('roles.form', ['submitName' => '新建角色'])

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
