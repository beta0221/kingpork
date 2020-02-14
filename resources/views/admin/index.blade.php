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








@endsection

@section('scripts')
<script>
    function monthlyReport(){
        var date = $('#monthlyReportDate').val();
        window.open('/order/export/MonthlyReport/'+date);
    }
    function dailyReport(){
        var date = $('#dailyReportDate').val();
        window.open('/order/export/DailyReport/'+date);
    }
</script>
@endsection