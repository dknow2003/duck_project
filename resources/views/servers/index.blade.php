@extends('layouts.app')

@section('content-top')

  <div class="wrapper wrapper-content">
    <div class="row">

      {{--<div class="col-lg-2">--}}
        {{--@include('servers.header-left')--}}
      {{--</div>--}}

      <div class="col-lg-12 fadeInRight">
        @include('servers.user-list-header')
        @include('servers.user-list')
      </div>

    </div>
  </div>

@endsection

@section('style')
@endsection

@section('script')
@endsection
