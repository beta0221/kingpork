@extends('admin_main')

@section('title','| 優惠連結管理')

@section('content')

<div class="container-fluid mt-4">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>優惠連結管理</h2>
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
            <a href="{{ route('admin.promotional-links.create') }}" class="btn btn-primary">
                <span class="glyphicon glyphicon-plus"></span> 新增優惠連結
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
                                <th width="12%">優惠碼</th>
                                <th width="15%">活動名稱</th>
                                <th width="8%">折扣</th>
                                <th width="10%">適用類別</th>
                                <th width="12%">開始日期</th>
                                <th width="12%">結束日期</th>
                                <th width="6%">狀態</th>
                                <th width="6%">使用次數</th>
                                <th width="14%">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($promotionalLinks as $link)
                            <tr>
                                <td>{{ $link->id }}</td>
                                <td>
                                    <strong style="color: #007bff;">{{ $link->code }}</strong>
                                </td>
                                <td>{{ $link->name }}</td>
                                <td>
                                    <span class="badge badge-warning">{{ $link->discount_percentage }}% OFF</span>
                                </td>
                                <td>
                                    @if(empty($link->applicable_categories))
                                        <span class="text-muted">全部商品</span>
                                    @else
                                        <small>{{ count($link->applicable_categories) }} 個類別</small>
                                    @endif
                                </td>
                                <td>{{ $link->start_date->format('Y-m-d H:i') }}</td>
                                <td>{{ $link->end_date->format('Y-m-d H:i') }}</td>
                                <td>
                                    @php
                                        $statusText = $link->getStatusText();
                                    @endphp
                                    @if($statusText == '進行中')
                                        <span class="badge badge-success">進行中</span>
                                    @elseif($statusText == '已過期')
                                        <span class="badge badge-danger">已過期</span>
                                    @elseif($statusText == '未開始')
                                        <span class="badge badge-warning">未開始</span>
                                    @else
                                        <span class="badge badge-secondary">已停用</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info">{{ $link->usage_count }}</span>
                                </td>
                                <td>
                                    <div class="btn-group-vertical btn-group-sm" role="group">
                                        <button type="button" class="btn btn-sm btn-success copy-link-btn"
                                                data-code="{{ $link->code }}"
                                                data-url="{{ url('/promo/' . $link->code) }}"
                                                title="複製連結">
                                            <span class="glyphicon glyphicon-link"></span> 複製連結
                                        </button>

                                        <a href="{{ route('admin.promotional-links.edit', $link->id) }}"
                                           class="btn btn-sm btn-info">
                                            <span class="glyphicon glyphicon-pencil"></span> 編輯
                                        </a>

                                        <form action="{{ route('admin.promotional-links.toggle', $link->id) }}"
                                              method="POST" class="mb-1">
                                            {{ csrf_field() }}
                                            {{ method_field('PATCH') }}
                                            <button type="submit"
                                                    class="btn btn-sm btn-{{ $link->is_active ? 'warning' : 'primary' }} btn-block">
                                                <span class="glyphicon glyphicon-{{ $link->is_active ? 'pause' : 'play' }}"></span>
                                                {{ $link->is_active ? '停用' : '啟用' }}
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.promotional-links.destroy', $link->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('確定要刪除此優惠連結嗎？');">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-sm btn-danger btn-block">
                                                <span class="glyphicon glyphicon-trash"></span> 刪除
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center">目前沒有任何優惠連結</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-3">
                        {{ $promotionalLinks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const copyButtons = document.querySelectorAll('.copy-link-btn');

    copyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            const code = this.getAttribute('data-code');

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(() => {
                    alert('連結已複製: ' + url);
                }).catch(err => {
                    fallbackCopy(url);
                });
            } else {
                fallbackCopy(url);
            }
        });
    });

    function fallbackCopy(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.execCommand('copy');
            alert('連結已複製: ' + text);
        } catch (err) {
            alert('無法複製連結，請手動複製: ' + text);
        }
        document.body.removeChild(textarea);
    }
});
</script>

@endsection
