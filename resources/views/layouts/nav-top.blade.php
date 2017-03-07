<nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
  <div class="navbar-header">
    <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
    <form role="search" class="navbar-form-custom" action="search_results.html">
      <div class="form-group">

      </div>
    </form>
  </div>
  <ul class="nav navbar-top-links navbar-right">
    {{--<li>--}}
      {{--<span class="m-r-sm text-muted welcome-message">Welcome to INSPINIA+ Admin Theme.</span>--}}
    {{--</li>--}}

    @include('layouts.nav-top-dropdown')

    <li>
      <a href="/logout">
        <i class="fa fa-sign-out"></i> {{ trans('user.logout') }}
      </a>
    </li>
    {{--<li>--}}
      {{--<a class="right-sidebar-toggle">--}}
        {{--<i class="fa fa-tasks"></i>--}}
      {{--</a>--}}
    {{--</li>--}}
  </ul>

</nav>
