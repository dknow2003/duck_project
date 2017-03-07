<div class="mail-body">

  <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
    <label class="col-sm-2 control-label">{{ trans('user.username') }}
      * </label>
    <div class="col-sm-10">
      {!! Form::text('username', old('username'), ['class' => 'form-control', 'autocomplete' => 'off']) !!}
      @if ($errors->has('username'))
        <span class="help-block">
                  <strong>{{ $errors->first('username') }}</strong>
                </span>
      @endif
    </div>
  </div>

  <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
    <label class="col-sm-2 control-label">{{ trans('user.email') }} * </label>

    <div class="col-sm-10">
      {!! Form::text('email', old('email'), ['class' => 'form-control', 'autocomplete' => 'off']) !!}
      @if ($errors->has('email'))
        <span class="help-block">
                  <strong>{{ $errors->first('email') }}</strong>
                </span>
      @endif
    </div>
  </div>

  <div class="form-group{{ $errors->has('pwd') ? ' has-error' : '' }}">
    <label class="col-sm-2 control-label">{{ trans('user.password') }}
      * </label>

    <div class="col-sm-10">
      @if(isset($isEdit) && $isEdit)
        {!! Form::text('pwd', '***********', ['class' => 'form-control', 'autocomplete' => 'off', 'disabled', 'id' => 'password-input']) !!}
      @else
        {!! Form::text('pwd', old('pwd') , ['class' => 'form-control', 'autocomplete' => 'off']) !!}
      @endif

      @if ($errors->has('pwd'))
        <span class="help-block">
                  <strong>{{ $errors->first('pwd') }}</strong>
                </span>
      @endif
    </div>
  </div>

  <div class="form-group{{ $errors->has('full_name') ? ' has-error' : '' }}">
    <label class="col-sm-2 control-label">{{ trans('user.full_name') }}
      * </label>

    <div class="col-sm-10">
      {!! Form::text('full_name', $originPassword = old('full_name'), ['class' => 'form-control', 'autocomplete' => 'off']) !!}
      @if ($errors->has('full_name'))
        <span class="help-block">
                  <strong>{{ $errors->first('full_name') }}</strong>
                </span>
      @endif
    </div>
  </div>

  {{-- Allowed to edit user password. --}}
  @if(isset($isEdit) && $isEdit)
    {!! Form::hidden('original_password', isset($user) ? $user->password : '') !!}
    <div class="form-group" style="line-height: 3.5em;">
      <label class="col-sm-2 control-label"></label>
      <div class="col-sm-10">
        <label>
          {!! Form::checkbox('change_password', 0, false, ['class' => 'i-checks', 'id' => 'change-password']) !!}
          修改密码
        </label>
      </div>
    </div>
  @endif

  @foreach($roles as $role)
    @if($role->name != 'superadmin')
      <?php $shouldShowSelecteRoles = true; ?>
    @endif
  @endforeach
  @if(isset($shouldShowSelecteRoles))
    <div class="form-group" style="line-height: 3.5em;">
      <label
          class="col-sm-2 control-label">{{ trans('角色') }}</label>

      <div class="col-sm-10">
        @foreach($roles as $role)
          @if($role->name != 'superadmin')
            <label style="margin-right: 20px;">
              {!! Form::checkbox('roles[]', $role->id, isset($role) && isset($user) ? $user->hasRole($role->name) : false, ['class' => 'i-checks']) !!}
              {{ $role->display_name }}
            </label>
          @endif
        @endforeach
      </div>
    </div>
  @endif


{{-- 渠道设置 --}}
	<div class="form-group">
		<label class="col-sm-2 control-label">渠道限制</label>

		<div class="col-sm-10">
			{!! Form::select('channel', $channelList, old('channel'), ['class' => 'form-control', 'placeholder' => '所有渠道']) !!}
		</div>
	</div>
{{--// 渠道设置--}}


	{{-- 服务器 --}}
	<div class="form-group">
		<label class="col-sm-2 control-label">服务器限制</label>

		<div class="col-sm-10">
			{!! Form::select('available_servers[]', $available_servers, old('available_servers'), ['class' => 'form-control', 'placeholder' => '所有服务器', 'multiple' => 'true']) !!}
		</div>
	</div>
	{{--// 服务器--}}

</div>

<div class="mail-body text-left tooltip-demo">
  <a href="{{ url('admin/users') }}" class="btn btn-white btn-sm"
     data-toggle="tooltip" data-placement="top" title="返回"><i
        class="fa fa-reply"></i> {{ trans('utilities.return') }}</a>
  <button type="submit" class="btn btn-sm btn-primary" data-toggle="tooltip"
          data-placement="top" title="{{ $submitName }}"><i
        class="fa fa-check"></i> {{ $submitName }}</button>
</div>
