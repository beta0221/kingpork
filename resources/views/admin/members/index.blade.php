@extends('admin_main')

@section('title','| 會員管理')

@section('content')

<div class="container-fluid mt-4">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>會員管理 <span style="color: #666; font-size: 18px;">共 {{ number_format($members->total()) }} 位會員</span></h2>
        </div>
    </div>

    <!-- 搜尋表單 -->
    <div class="row mb-3">
        <div class="col-md-12">
            <form method="GET" action="{{ route('admin.members.index') }}" class="form-inline">
                <div class="form-group mr-2">
                    <input type="text"
                           name="name"
                           class="form-control"
                           placeholder="姓名"
                           value="{{ request('name') }}">
                </div>

                <div class="form-group mr-2">
                    <input type="text"
                           name="email"
                           class="form-control"
                           placeholder="Email"
                           value="{{ request('email') }}">
                </div>

                <button type="submit" class="btn btn-primary">
                    <span class="glyphicon glyphicon-search"></span> 搜尋
                </button>

                @if(request('name') || request('email'))
                <a href="{{ route('admin.members.index') }}" class="btn btn-default ml-2">
                    清除搜尋
                </a>
                @endif
            </form>
        </div>
    </div>

    <!-- 會員列表 -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">ID</th>
                                <th width="15%">姓名</th>
                                <th width="28%">Email</th>
                                <th width="12%">電話</th>
                                <th width="10%">紅利點數</th>
                                <th width="15%">註冊時間</th>
                                <th width="15%">最後更新</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($members as $member)
                            <tr>
                                <td>{{ $member->id }}</td>
                                <td>
                                    <a href="/order/history/{{ $member->id }}" target="_blank" style="color: #337ab7; text-decoration: none;">
                                        {{ $member->name }}
                                    </a>
                                </td>
                                <td>{{ $member->email }}</td>
                                <td>{{ $member->phone }}</td>
                                <td class="text-right">{{ number_format($member->bonus) }}</td>
                                <td>{{ $member->created_at ? $member->created_at->format('Y-m-d H:i') : '-' }}</td>
                                <td>{{ $member->updated_at ? $member->updated_at->format('Y-m-d H:i') : '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">無會員資料</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- 分頁 -->
                    <div class="mt-3">
                        {{ $members->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
