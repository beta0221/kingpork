@extends('admin_main')

@section('title','| 管理系統後台')

@section('stylesheets')
<style>
	
</style>
@endsection

@section('content')


<?php 
    $venders = [
        '官網' => 'official',
        '為美' => 'waymay',
        '寶媽咪' => 'bawmami'
    ]
?>
<div class="mt-2 p-2">
    <div>
        <select id="monthlyReportVendor" class="form-control d-inline-block" style="width:120px">
            @foreach ($venders as $name => $key)
            <option value="{{$key}}">{{$name}}</option>    
            @endforeach
        </select>
        <input id="monthlyReportDate" type="date" class="form-control d-inline-block" style="width:200px">
    </div>
    
    <button class="mt-2 btn btn-danger" onclick="monthlyReport()">月報表</button>
</div>

<div class="mt-2 p-2">
    <div>
        <select id="dailyReportVendor" class="form-control d-inline-block" style="width:120px">
            @foreach ($venders as $name => $key)
            <option value="{{$key}}">{{$name}}</option>    
            @endforeach
        </select>
        <input id="dailyReportDate" type="date" class="form-control d-inline-block" style="width:200px">
    </div>
    
    <button class="mt-2 btn btn-primary" onclick="dailyReport()">日報表</button>
</div>

<div class="mt-2 p-2" style="">
    <div>
        <select id="bestSellerVendor" class="form-control d-inline-block" style="width:120px">
            @foreach ($venders as $name => $key)
            <option value="{{$key}}">{{$name}}</option>    
            @endforeach
        </select>
        <input id="bestSellerDate_from" type="date" class="form-control d-inline-block" style="width:200px">
        <span>-</span>
        <input id="bestSellerDate_to" type="date" class="form-control d-inline-block" style="width:200px">
    </div>
    
    <button class="mt-2 btn btn-success" onclick="bestSeller()">銷售排行</button>
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
        let date = $('#monthlyReportDate').val();
        let vendor = $('#monthlyReportVendor').val();
        if(date){
            window.open('/order/export/MonthlyReport/'+ vendor + '/' + date);
        }
    }
    function dailyReport(){
        let date = $('#dailyReportDate').val();
        let vendor = $('#dailyReportVendor').val();
        if(date){
            window.open('/order/export/DailyReport/' + vendor + '/' + date);
        }
    }

    function bestSeller() {
        let vendor = $('#bestSellerVendor').val();
        let from = $('#bestSellerDate_from').val();
        let to = $('#bestSellerDate_to').val();
        if (!vendor) { return }
        if (!from) { return }
        if (!to) { return }
        window.open('/order/stats/bestSeller/' + vendor + '/' + from + '/' + to);
    }
</script>
@endsection