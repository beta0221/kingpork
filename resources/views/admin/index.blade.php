@extends('admin_main')

@section('title','| 管理系統後台')

@section('stylesheets')
<style>
	
</style>
@endsection

@section('content')

<div class="mt-2 p-2" style="width:200px">
    <input id="monthlyReportDate" type="date" class="form-control">
    <button class="mt-2 btn btn-danger" onclick="monthlyReport()">月報表</button>
</div>

<div class="mt-2 p-2" style="width:200px">
    <input id="dailyReportDate" type="date" class="form-control">
    <button class="mt-2 btn btn-primary" onclick="dailyReport()">日報表</button>
</div>

<div class="mt-2 p-2">
    <h3>發票記錄</h3>
    {{$invoiceLogs->links()}}
    @foreach ($invoiceLogs as $log)
        <span>訂單編號：{{$log->bill_id}}</span><br>
        <span>{{$log->info}}</span><br>
    @endforeach

    {{$invoiceLogs->links()}}
</div>








@endsection

@section('scripts')
<script>
    function monthlyReport(){
        var date = $('#monthlyReportDate').val();
        if(date){
            window.open('/order/export/MonthlyReport/'+date);
        }
    }
    function dailyReport(){
        var date = $('#dailyReportDate').val();
        if(date){
            window.open('/order/export/DailyReport/'+date);
        }
    }
</script>
@endsection