@extends('admin_main')

@section('title','| 紅利活動管理')

@section('content')

<div class="container-fluid mt-4">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>紅利倍數活動管理 <span style="color: orange">目前紅利 {{\App\BonusPromotion::getCurrentMultiplier()}} 倍</span></h2>

            
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="row mb-3">
        <div class="col-md-12">
            <a href="{{ route('admin.bonus-promotions.create') }}" class="btn btn-primary">
                <span class="glyphicon glyphicon-plus"></span> 新增活動
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">ID</th>
                                <th width="20%">活動名稱</th>
                                <th width="10%">倍數</th>
                                <th width="15%">開始時間</th>
                                <th width="15%">結束時間</th>
                                <th width="10%">狀態</th>
                                <th width="10%">進行狀態</th>
                                <th width="15%">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($promotions as $promotion)
                            <tr>
                                <td>{{ $promotion->id }}</td>
                                <td>{{ $promotion->name }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $promotion->multiplier }}x</span>
                                </td>
                                <td>{{ $promotion->start_time->format('Y-m-d H:i') }}</td>
                                <td>{{ $promotion->end_time->format('Y-m-d H:i') }}</td>
                                <td>
                                    @if($promotion->is_active)
                                        <span class="badge badge-success">已啟用</span>
                                    @else
                                        <span class="badge badge-danger">已停用</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $now = new \DateTime('now', new \DateTimeZone('Asia/Taipei'));
                                        $endTime = new \DateTime($promotion->end_time, new \DateTimeZone('Asia/Taipei'));
                                    @endphp
                                    @if($promotion->isOngoing())
                                        <span class="badge badge-success">進行中</span>
                                    @elseif($endTime < $now)
                                        <span class="badge badge-danger">已結束</span>
                                    @else
                                        <span class="badge badge-warning">未開始</span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <a href="{{ route('admin.bonus-promotions.edit', $promotion->id) }}"
                                           class="btn btn-sm btn-info">
                                            <span class="glyphicon glyphicon-pencil">編輯</span>
                                        </a>

                                        <form action="{{ route('admin.bonus-promotions.toggle', $promotion->id) }}"
                                              method="POST" class="d-inline">
                                            {{ csrf_field() }}
                                            {{ method_field('PATCH') }}
                                            <button type="submit"
                                                    class="btn btn-sm btn-{{ $promotion->is_active ? 'warning' : 'success' }}">
                                                <span class="glyphicon glyphicon-{{ $promotion->is_active ? 'pause' : 'play' }}">
                                                    {{ $promotion->is_active ? '停用' : '啟用' }}
                                                </span>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.bonus-promotions.destroy', $promotion->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('確定要刪除此活動嗎？');">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <span class="glyphicon glyphicon-trash">
                                                    刪除
                                                </span> 
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">目前沒有任何活動</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-3">
                        {{ $promotions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
