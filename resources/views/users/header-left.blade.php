<div class="ibox float-e-margins">
  <div class="ibox-content mailbox-content">
    <div class="file-manager">
      <a class="btn btn-block btn-primary compose-mail"
         href="{{ url('admin/users/create') }}">{{ trans('user.create') }}</a>
      <div class="space-25"></div>
      {{--<h5>Folders</h5>--}}
      {{--<ul class="folder-list m-b-md" style="padding: 0">--}}
      {{--<li><a href="#"> <i class="fa fa-inbox "></i> Inbox--}}
      {{--<span class="label label-warning pull-right">16</span> </a>--}}
      {{--</li>--}}
      {{--<li><a href="#"> <i class="fa fa-envelope-o"></i>--}}
      {{--Send Mail</a></li>--}}
      {{--<li><a href="#"> <i class="fa fa-certificate"></i>--}}
      {{--Important</a></li>--}}
      {{--<li><a href="#"> <i class="fa fa-file-text-o"></i>--}}
      {{--Drafts <span class="label label-danger pull-right">2</span></a>--}}
      {{--</li>--}}
      {{--<li><a href="#"> <i class="fa fa-trash-o"></i> Trash</a>--}}
      {{--</li>--}}
      {{--</ul>--}}

      @if(isset($roles) && count($roles) > 0)
        <style>
          .category-list a.selected span,
          .category-list a:hover span,
          .category-list a:active span {
            color: #188f71;
            border-bottom: 1px solid #188f71;
          }
        </style>

        @foreach($roles as $role)
          @if($role->usersCount)
            <?php $shouldShowRolesTitle = true; ?>
          @endif
        @endforeach
        @if(isset($shouldShowRolesTitle))
        <h5>按角色</h5>
        @endif
        <ul class="category-list" style="padding: 0">

          {{--<li><a href="{{ url('users') }}" class=" {{--}}
          {{--\Request::get('permission') || \Request::get('role')--}}
          {{--? ''--}}
          {{--: ' selected'--}}
          {{--}}"> <i--}}
          {{--class="fa fa-circle text-navy"></i> <span>全部</span>--}}
          {{--</a></li>--}}
          @foreach($roles as $role)
            @if($role->usersCount)
              @if(\Request::get('role') != $role->id)
                <li><a href="{{ url('users?role=' . $role->id) }}"> <i
                        class="fa fa-circle text-navy"></i> <span>{{ $role->display_name }}
                      {{ ' (' . $role->usersCount . ')'}}</span>
                  </a></li>
              @else
                <li><a href="{{ url('admin/users?role=' . $role->id) }}"
                       class="selected"> <i
                        class="fa fa-circle text-navy"></i> <span>{{ $role->display_name }}
                      {{ ' (' . $role->usersCount . ')'}}</span>
                  </a></li>
              @endif
            @endif
          @endforeach
        </ul>
      @endif


      @if(isset($permissions) && count($permissions) > 0)
        <style>
          .tag-list a.selected,
          .tag-list a:hover,
          .tag-list a:active {
            border-color: #18a689;
          }
        </style>
        @foreach($permissions as $permission)
          @if($permission->usersCount)
            <?php $shouldShowTitle = true; ?>
          @endif
        @endforeach
        @if(isset($shouldShowTitle))
        <h5 class="tag-title">按权限</h5>
        @endif
        <ul class="tag-list" style="padding: 0">
          {{--<li><a class="btn {{--}}
          {{--\Request::get('permission') || \Request::get('role')--}}
          {{--? ''--}}
          {{--: ' selected'--}}
          {{--}}"--}}
          {{--href="{{ url('users') }}"><i--}}
          {{--class="fa fa-tag"></i> 全部</a></li>--}}
          @foreach($permissions as $permission)
            @if($permission->usersCount)
              @if(\Request::get('permission') != $permission->id)
                <li><a class="btn btn-default"
                       href="{{ url('admin/users?permission=' . $permission->id) }}"><i
                        class="fa fa-tag"></i> {{ $permission->display_name }}
                    {{ ' (' .$permission->usersCount . ')'}}
                  </a></li>
              @else
                <li><a class="btn selected"
                       href="{{ url('admin/users?permission=' . $permission->id) }}"><i
                        class="fa fa-tag"></i> {{ $permission->display_name }}
                    {{ $permission->usersCount ? ' (' .$permission->usersCount . ')' : '' }}
                  </a></li>
              @endif
            @endif
          @endforeach
        </ul>
      @endif
      <div class="clearfix"></div>
    </div>
  </div>
</div>
