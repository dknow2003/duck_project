<!DOCTYPE html>
<html>

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>登录 - {{ app('config')->get('app.site_name') }} </title>

  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="font-awesome/css/font-awesome.css" rel="stylesheet">

  <link href="css/animate.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">

</head>

<body class="gray-bg">

<div class="middle-box text-center loginscreen animated fadeInDown">
  <div>
    <div>

      <h1 class="logo-name">{{ mb_substr(app('config')->get('app.site_name'), 0, 1) }}</h1>

    </div>
    <h3>{{ app('config')->get('app.site_name') }}</h3>
    {{--<p>Perfectly designed and precisely prepared admin theme with over 50 pages--}}
      {{--with extra new web app views.--}}
      {{--<!--Continually expanded and constantly improved Inspinia Admin Them (IN+)-->--}}
    {{--</p>--}}
    {{--<p>Login in. To see it in action.</p>--}}
    <form method="POST" class="m-t" role="form" action="{{ url('/login') }}">
      {{ csrf_field() }}

      <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
        <input type="text" class="form-control" placeholder="用户名或邮箱地址。" name="email"
               required="" value="{{ old('email') }}">

        @if ($errors->has('email'))
          <span class="help-block">
             <strong>{{ $errors->first('email') }}</strong>
          </span>
        @endif
      </div>

      <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
        <input type="password" class="form-control" placeholder="{{ trans('user.password') }}" name="password"
               required="" >

        @if ($errors->has('password'))
          <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
        @endif
      </div>

      <div class="form-group text-left">
          <div class="checkbox">
            <label>
              <input type="checkbox" name="remember"> {{ trans('user.remember_me') }}
            </label>
          </div>
      </div>

      <button type="submit" class="btn btn-primary block full-width m-b">
        {{ trans('user.login') }}
      </button>

      {{--<a href="#">--}}
        {{--<small>Forgot password?</small>--}}
      {{--</a>--}}
      {{--<p class="text-muted text-center">--}}
        {{--<small>Do not have an account?</small>--}}
      {{--</p>--}}
      {{--<a class="btn btn-sm btn-white btn-block" href="register.html">Create an--}}
        {{--account</a>--}}
    </form>
    <p class="m-t">
      <small> {{ app('config')->get('app.company') }} &copy; 2016</small>
    </p>
  </div>
</div>

<!-- Mainly scripts -->
<script src="js/jquery-2.1.1.js"></script>
<script src="js/bootstrap.min.js"></script>

</body>

</html>

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
          <div class="panel-heading">Login</div>
          <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST"
                  action="{{ url('/login') }}">
              {{ csrf_field() }}

              <div
                  class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <label for="email" class="col-md-4 control-label">E-Mail
                  Address</label>

                <div class="col-md-6">
                  <input id="email" type="email" class="form-control"
                         name="email" value="{{ old('email') }}">

                  @if ($errors->has('email'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                  @endif
                </div>
              </div>

              <div
                  class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="password"
                       class="col-md-4 control-label">Password</label>

                <div class="col-md-6">
                  <input id="password" type="password" class="form-control"
                         name="password">

                  @if ($errors->has('password'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                  @endif
                </div>
              </div>

              <div class="form-group">
                <div class="col-md-6 col-md-offset-4">
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" name="remember"> Remember Me
                    </label>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="col-md-6 col-md-offset-4">
                  <button type="submit" class="btn btn-primary">
                    <i class="fa fa-btn fa-sign-in"></i> Login
                  </button>

                  <a class="btn btn-link" href="{{ url('/password/reset') }}">Forgot
                    Your Password?</a>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
