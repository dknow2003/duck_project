<div class="mail-body">

  <div class="form-group{{ $errors->has('display_name') ? ' has-error' : '' }}">
    <label
        class="col-sm-2 control-label">{{ trans('role.display_name') }} * </label>

    <div class="col-sm-10">
      {!! Form::text('display_name', old('display_name'), ['class' => 'form-control', 'placeholder' => trans('role.display_name_placeholder')]) !!}
      @if ($errors->has('display_name'))
        <span class="help-block">
                  <strong>{{ $errors->first('display_name') }}</strong>
                </span>
      @endif
    </div>
  </div>

  <div class="form-group">
    <label
        class="col-sm-2 control-label">{{ trans('role.description') }}</label>

    <div class="col-sm-10">
      {!! Form::text('description', old('description'), ['class' => 'form-control', 'placeholder' => trans('role.description_placeholder')]) !!}
    </div>
  </div>

  <div class="clearfix"></div>

  <div class="form-group" style="line-height: 3.5em;">
    <label
        class="col-sm-2 control-label">{{ trans('权限') }}</label>

    <div class="col-sm-10">
      @foreach($permissions as $permission)
        <label style="margin-right: 20px;">
        {!! Form::checkbox('permissions[]', $permission->id, isset($role) ? $role->hasPermission($permission->name) : false, ['class' => 'js-switch']) !!}
        {{ $permission->display_name }}
        </label>
      @endforeach
    </div>
  </div>

</div>

<div class="mail-body text-left tooltip-demo">
  <a href="{{ url('admin/roles') }}" class="btn btn-white btn-sm"
     data-toggle="tooltip" data-placement="top" title="返回"><i
        class="fa fa-reply"></i> {{ trans('utilities.return') }}</a>
  <button type="submit" class="btn btn-sm btn-primary" data-toggle="tooltip"
          data-placement="top" title="{{ $submitName }}"><i
        class="fa fa-check"></i> {{ $submitName }}</button>
  {{--<a href="mailbox.html" class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="top" title="Move to draft folder"><i class="fa fa-pencil"></i> Draft</a>--}}
</div>
