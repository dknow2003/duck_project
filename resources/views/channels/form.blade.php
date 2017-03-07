<div class="mail-body">

  <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
    <label
        class="col-sm-2 control-label">渠道名称 * </label>

    <div class="col-sm-10">
      {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '渠道名称']) !!}
      @if ($errors->has('name'))
        <span class="help-block">
                  <strong>{{ $errors->first('name') }}</strong>
                </span>
      @endif
    </div>
  </div>

  <div class="form-group{{ $errors->has('channel_id') ? ' has-error' : '' }}">
    <label
        class="col-sm-2 control-label">渠道 ID * </label>

    <div class="col-sm-10">
      {!! Form::text('channel_id', old('channel_id'), ['class' => 'form-control', 'placeholder' => '渠道 ID']) !!}
      @if ($errors->has('channel_id'))
        <span class="help-block">
                  <strong>{{ $errors->first('channel_id') }}</strong>
                </span>
      @endif
    </div>
  </div>

  <div class="clearfix"></div>

</div>

<div class="mail-body text-left tooltip-demo">
  <a href="{{ url('admin/channels') }}" class="btn btn-white btn-sm"
     data-toggle="tooltip" data-placement="top" title="返回"><i
        class="fa fa-reply"></i> {{ trans('utilities.return') }}</a>
  <button type="submit" class="btn btn-sm btn-primary" data-toggle="tooltip"
          data-placement="top" title="{{ $submitName }}"><i
        class="fa fa-check"></i> {{ $submitName }}</button>
  {{--<a href="mailbox.html" class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="top" title="Move to draft folder"><i class="fa fa-pencil"></i> Draft</a>--}}
</div>
