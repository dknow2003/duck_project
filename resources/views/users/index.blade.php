@extends('layouts.app')

@section('content-top')

  <div class="wrapper wrapper-content">
    <div class="row">

      <div class="col-lg-2">
        @include('users.header-left')
      </div>

      <div class="col-lg-10 animated fadeInRight">
        @include('users.user-list-header')
        @include('users.user-list')
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

      // Change users status form
      $('.check-users').on('ifChecked', function () {
        $('#change-status-type').val(1);
        $('#change-status-value').val($(this).val());
        $('#change-status-form').submit();
      });

      // Change users status form
      $('.check-users').on('ifUnchecked', function () {
        $('#change-status-type').val(0);
        $('#change-status-value').val($(this).val());
        $('#change-status-form').submit();
      });


    });
  </script>
@endsection
