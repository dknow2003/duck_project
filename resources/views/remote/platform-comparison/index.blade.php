@extends('layouts.app')

@section('content-top')

  <div class="wrapper wrapper-content">
    <div class="row">

      <div class="col-lg-12 animated fadeInRight">
        Orders check.
        {{--@include('advertisers.list-header')--}}
        {{--@include('advertisers.list')--}}
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
