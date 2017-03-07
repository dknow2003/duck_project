<nav class="navbar-default navbar-static-side" role="navigation">
  <div class="sidebar-collapse">
    <ul class="nav metismenu" id="side-menu">
      <li class="nav-header">
        <div class="dropdown profile-element"> <span>
                            {{--<img alt="image" class="img-circle" src="{{ asset('img/profile_small.jpg') }}" />--}}
                             </span>
          <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold">{{ Auth::user()->username }}</strong>
                             </span> <span class="text-muted text-xs block">{{ Auth::user()->is_super ? '超级管理员' : Auth::user()->roles()->first()->display_name }} <b class="caret"></b></span> </span> </a>
          <ul class="dropdown-menu animated fadeInRight m-t-xs">
            {{--<li><a href="profile.html">Profile</a></li>--}}
            {{--<li><a href="contacts.html">Contacts</a></li>--}}
            {{--<li><a href="mailbox.html">Mailbox</a></li>--}}
            {{--<li class="divider"></li>--}}
            <li><a href="/logout">{{ trans('user.logout') }}</a></li>
          </ul>
        </div>
        <div class="logo-element">
          IN+
        </div>
      </li>

      @include('layouts.sidebar-nav-left')

    </ul>

  </div>
</nav>
