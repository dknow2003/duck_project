<div class="row form-group {{ $errors->has('name') ? ' has-error' : '' }}"
     style="line-height: 2.5em;">
  <label class="col-sm-2 control-label text-right">服务器名称 *</label>
  <div class="col-sm-10">
    {!! Form::text('name', old('name'), ['class' => 'form-control', 'autocomplete' => 'off']) !!}
    @if($errors->has('name'))
      <span class="help-block">
    <strong>{{ $errors->first('name') }}</strong>
    </span>
    @endif
  </div>
</div>

<div class="row form-group {{ $errors->has('start_from') ? ' has-error' : '' }}"
     style="line-height: 2.5em;">
	<label class="col-sm-2 control-label text-right">开服时间 *</label>
	<div class="col-sm-10">
		{!! Form::text('start_from', old('start_from'), ['class' => 'form-control input-date', 'autocomplete' => 'off']) !!}
		@if($errors->has('start_from'))
			<span class="help-block">
    <strong>{{ $errors->first('start_from') }}</strong>
    </span>
		@endif
	</div>
</div>

<div class="ibox float-e-margins" style="margin-top: 20px;">
  <div class="ibox-content servers">
    <div class="row">
      <div class="col-sm-6 b-r"><h4 class="m-t-none m-b">连接信息一</h4>
        <p>游戏数据库：</p>
        <div
            class="form-group {{ $errors->has('host.1') ? ' has-error' : '' }}">
          <label class="control-label">主机</label>
          {!! Form::text('host[1]', old('host[1]'), ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '如 127.0.0.1 或 server.com']) !!}
          @if($errors->has('host.1'))
            <span class="help-block">
              <strong>{{ $errors->first('host.1') }}</strong>
            </span>
          @endif
        </div>
        <div
            class="form-group  {{ $errors->has('port.1') ? ' has-error' : '' }}">
          <label class="control-label">端口</label>
          {!! Form::text('port[1]', old('port[1]'), ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '如 3306']) !!}
          @if($errors->has('port.1'))
            <span class="help-block">
              <strong>{{ $errors->first('port.1') }}</strong>
            </span>
          @endif
        </div>
        <div
            class="form-group  {{ $errors->has('database.1') ? ' has-error' : '' }}">
          <label class="control-label">数据库</label>
          {!! Form::text('database[1]', old('database[1]'), ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '如 slg']) !!}
          @if($errors->has('database.1'))
            <span class="help-block">
              <strong>{{ $errors->first('database.1') }}</strong>
            </span>
          @endif
        </div>
        <div
            class="form-group  {{ $errors->has('username.1') ? ' has-error' : '' }}">
          <label class="control-label">用户名</label>
          {!! Form::text('username[1]', old('username[1]'), ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '数据库用户名']) !!}
        @if($errors->has('username.1'))
            <span class="help-block">
              <strong>{{ $errors->first('username.1') }}</strong>
            </span>
          @endif
        </div>
        <div
            class="form-group  {{ $errors->has('pwd.1') ? ' has-error' : '' }}">
          <label class="control-label">密码</label>
          {!! Form::text('pwd[1]', old('pwd[1]'), ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '数据库密码']) !!}
          @if($errors->has('pwd.1'))
            <span class="help-block">
              <strong>{{ $errors->first('pwd.1') }}</strong>
            </span>
          @endif
        </div>
        {{--<div>--}}
        {{--<button class="btn btn-sm btn-primary pull-right m-t-n-xs"--}}
        {{--type="submit"><strong>Log in</strong></button>--}}
        {{--<label> <input type="checkbox" class="i-checks"> Remember me--}}
        {{--</label>--}}
        {{--</div>--}}
      </div>

      <div class="col-sm-6"><h4 class="m-t-none m-b">连接信息二</h4>
        <p>充值数据库：</p>

        <div
            class="form-group  {{ $errors->has('host.2') ? ' has-error' : '' }}">
          <label class="control-label">主机</label>
          {!! Form::text('host[2]', old('host[2]'), ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '如 127.0.0.1 或 server.com']) !!}
        @if($errors->has('host.2'))
            <span class="help-block">
              <strong>{{ $errors->first('host.2') }}</strong>
            </span>
          @endif
        </div>
        <div
            class="form-group  {{ $errors->has('port.2') ? ' has-error' : '' }}">
          <label class="control-label">端口</label>
          {!! Form::text('port[2]', old('port[2]'), ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '如 3306']) !!}
        @if($errors->has('port.2'))
            <span class="help-block">
              <strong>{{ $errors->first('port.2') }}</strong>
            </span>
          @endif
        </div>
        <div
            class="form-group  {{ $errors->has('database.2') ? ' has-error' : '' }}">
          <label class="control-label">数据库</label>
          {!! Form::text('database[2]', old('database[2]'), ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '如 firegms']) !!}
        @if($errors->has('database.2'))
            <span class="help-block">
              <strong>{{ $errors->first('database.2') }}</strong>
            </span>
          @endif
        </div>
        <div
            class="form-group  {{ $errors->has('username.2') ? ' has-error' : '' }}">
          <label class="control-label">用户名</label>
          {!! Form::text('username[2]', old('username[2]'), ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '数据库用户名']) !!}
        @if($errors->has('username.2'))
            <span class="help-block">
              <strong>{{ $errors->first('username.2') }}</strong>
            </span>
          @endif
        </div>
        <div
            class="form-group  {{ $errors->has('pwd.2') ? ' has-error' : '' }}">
          <label class="control-label">密码</label>
          {!! Form::text('pwd[2]', old('pwd[2]'), ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '数据库密码']) !!}
        @if($errors->has('pwd.2'))
            <span class="help-block">
              <strong>{{ $errors->first('pwd.2') }}</strong>
            </span>
          @endif
        </div>

      </div>
    </div>
  </div>
</div>
<div class="mail-body text-left tooltip-demo">
  <a href="{{ url('admin/servers') }}" class="btn btn-white btn-sm"
     data-toggle="tooltip" data-placement="top" title="返回"><i
        class="fa fa-reply"></i> {{ trans('utilities.return') }}</a>
  <button type="submit" class="btn btn-sm btn-primary" data-toggle="tooltip"
          data-placement="top" title="{{ $submitName }}"><i
        class="fa fa-check"></i> {{ $submitName }}</button>
</div>

<style>
  .servers .form-group {
    padding: 0 15px;
  }

  .servers .control-label {
    margin: 5px 0;
  }
</style>
