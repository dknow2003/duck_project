@extends('layouts.app')

@section('content-top')

  <div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
      <h2>工会 ({{ $guilds->total() ?: 0 }})</h2>
    </div>
  </div>

  <div class="wrapper wrapper-content">
    <div class="row">

      <div class="col-lg-12 animated fadeInRight">
        <div class="ibox float-e-margins">

          <div class="row">
            {{-- search --}}
            <div class="row" style="padding-right: 15px;">
              <div class="pull-right">{{ $guilds->links() }}</div>
            </div>

            <style>
              .table-striped a {
                color: #333;
              }
            </style>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                <tr>
                  <th>工会 ID</th>
                  <th>名称</th>
                  <th>等级</th>
                  <th>会长 ID</th>
                  <th>会长名称</th>
                  <th>当前经验值</th>
                  <th>所需经验值</th>
                  <th>当前成员数</th>
                  <th>最大成员数</th>
                  <th>创建者 ID</th>
                  <th>创建者角色名</th>
                  <th>旗帜</th>
                  <th>宣言</th>
                  <th>公告</th>
                  <th>加入等级限制</th>
                  <th>审核方式</th>
                  <th>状态</th>
                  <th>创建时间</th>
                </tr>
                </thead>
                <tbody>


                @foreach($guilds as $guild)
                  <tr>
                    <td><a
                          href="{{ url('game/roles/' . $guild->GuildID) }}">{{ $guild->GuildID }}</a>
                    </td>
                    <td><a
                          href="{{ url('game/roles/' . $guild->GuildID) }}">{{ $guild->GuildName }}</a>
                    </td>
                    <td>{{ $guild->Level }}</td>
                    <td>{{ $guild->ChairUserID }}</td>
                    <td>{{ $guild->ChairRoleName }}</td>
                    <td>{{ $guild->Exp }}</td>
                    <td>{{ $guild->UpdNeedExp }}</td>
                    <td>{{ $guild->MemberCount }}</td>
                    <td>{{ $guild->MaxMemberLimit }}</td>
                    <td>{{ $guild->CreateUserID }}</td>
                    <td>{{ $guild->CreateRoleName }}</td>
                    <td>{{ $guild->GuildFlag }}</td>
                    <td>{{ $guild->GuildPurpose }}</td>
                    <td>{{ $guild->GuildBroad }}</td>
                    <td>{{ $guild->LevelLimit }}</td>
                    <?php
                    $joinAuditType = '';
                    if ($guild->JoinAuditType == 0) {
                      $joinAuditType = '关闭';
                    } elseif ($guild->JoinAuditType == 1) {
                      $joinAuditType = '需会长同意';
                    } elseif ($guild->JoinAuditType == 2) {
                      $joinAuditType = '任意进入';
                    }
                        ?>
                    <td>{{ $joinAuditType }}</td>
                    <?php
                    $status = '';
                    if ($guild->Status == 0) {
                      $status = '正常';
                    } elseif ($guild->Status == 1) {
                      $status = '锁定中';
                    }
                        ?>
                    <td>{{ $status }}</td>
                    <td>{{ $guild->CreateTime }}</td>
                  </tr>
                @endforeach

                </tbody>
              </table>
            </div>

          </div>
        </div>
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
