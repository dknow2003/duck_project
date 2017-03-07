@extends('layouts.app')

@section('content-top')

  <div class="wrapper wrapper-content">
    <div class="row">

      <div class="col-lg-12 animated fadeInRight">
        @include('channels.list-header')
        @include('channels.list')
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
