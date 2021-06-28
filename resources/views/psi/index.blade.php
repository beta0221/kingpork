@extends('admin_main')

@section('title','| 進銷存')

@section('stylesheets')
<style>
	.radio-input{
        transform: scale(1.8);
    }
    .inventory-log-table tr:hover{
        background: lightgray;
    }
    .selected-row{
        background: lightgray;
    }
</style>
@endsection

@section('content')

<div class="row p-2">

    <div class="col-md-4">
        <h3>庫存總計</h3>
        <table class="table">
            <tr style="font-weight: 800">
                <th>庫存</th>
                <td>數量</td>
            </tr>
            @foreach ($inventories as $i => $inventory)
            <tr>
                <td>{{$i+1}}.{{$inventory->name}}</td>
                <td>{{$inventory->amount}}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="col-md-4">
        <div class="mb-2">
            <h3 class="d-inline-block m-0">進銷貨紀錄</h3>
            <button  class="btn btn-sm btn-success align-top" data-toggle="modal" data-target="#inventoryModal">新增</button>
        </div>
        
        <table class="table inventory-log-table">
            <tr style="font-weight: 800">
                <td>日期</td>
                <td>事件</td>
                <td>通路</td>
                <td>-</td>
            </tr>
            @foreach ($inventoryLogs as $log)
            <tr style="color: {{($log->action=='sale')?"red":"green"}};cursor:pointer" class="log-row" data-id="{{$log->id}}">
                <td>{{$log->date}}</td>
                <td>{{$log->event}}</td>
                <td>
                    @if (isset($retailerDict[$log->retailer_id]))
                    {{$retailerDict[$log->retailer_id]}}    
                    @endif
                </td>
                <td>
                    <button class="btn btn-sm btn-danger btn-reverse" data-id="{{$log->id}}">
                        回朔
                    </button>
                </td>
            </tr>
            @endforeach
        </table>
        {{$inventoryLogs->links()}}
    </div>

    <div class="col-md-4">
        <h3 class="">紀錄詳情</h3>
        <table id="log-detail-table" class="table">
            <tr style="font-weight: 800">
                <td>庫存</td>
                <td>數量</td>
            </tr>
        </table>
    </div>


</div>





<div class="modal fade" id="inventoryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
	  <div class="modal-content">
		<div class="modal-header">
		  <h5 class="modal-title" id="exampleModalLabel">進銷貨</h5>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">

            <div class="mb-2">
                <span>日期</span>
                <input class="form-control" type="date" name="date">
            </div>

            <div class="mb-2">
                <span>事件</span>
                <input class="form-control" type="text" name="event" placeholder="事件">
            </div>

            <div class="mb-2">
                <span class="mr-2">銷貨(-)</span>
                <input class="radio-input mr-2" type="radio" name="action" value="sale" onclick="showRetailerRow()">
                <span class="mr-2">進貨(+)</span>
                <input class="radio-input mr-2" type="radio" name="action" value="purchase" onclick="hideRetailerRow()">
            </div>

            <div class="retailer-row" class="mb-2" style="display: none">
                <select class="form-control" name="retailer_id">
                    <option value="">零售商</option>
                    @foreach ($retailers as $retailer)
                        <option value="{{$retailer->id}}">{{$retailer->name}}</option>
                    @endforeach
                </select>
            </div>

            <hr>
            
            <div>
                @foreach ($inventories as $inventory)
                    <input id="inventory-check-{{$inventory->id}}" data-inventory-id="{{$inventory->id}}" class="inventory-check" type="checkbox">
                    <span>{{$inventory->name}}</span>
                    <input id="inventory-quantity-{{$inventory->id}}" class="inventory-quantity" type="number"><br>
                @endforeach
            </div>
			
		</div>
		<div class="modal-footer">
			<button onclick="submitInventoryLog();" type="button" class="btn btn-primary">送出</button>
		  <button type="button" class="btn btn-secondary" data-dismiss="modal">關閉</button>
		</div>
	  </div>
	</div>
</div>

@endsection

@section('scripts')
<script>
    const inventoryDict = {!!json_encode($inventoryDict)!!};

    $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.log-row').on('click',function(event){
            event.stopPropagation();
            let id = $(this).data('id');

            $('.cell').remove();
            $('.selected-row').removeClass('selected-row');
            $(this).addClass('selected-row');

            showDetail(id);
        })

        $('.btn-reverse').on('click',function(event){
            event.stopPropagation();
            let id = $(this).data('id');
            reverseInventoryLog(id);
        })

    });


    function showRetailerRow(){
        $('.retailer-row').show();
    }
    function hideRetailerRow(){
        $('.retailer-row').hide();
    }

    function submitInventoryLog(){
        let inventory = getInventory();
        let event = $('input[name="event"]').val();
        let date = $('input[name="date"]').val();
        let action = $('input[name="action"]:checked').val();
        let retailer_id = $('select[name="retailer_id"]').val();

        $.ajax({
            type:'POST',
            url:'/psi',
            dataType:'json',
            data:{
                inventory:inventory,
                event:event,
                action:action,
                date:date,
                retailer_id:retailer_id
            },
            success:function(res){
                console.log(res);
                window.location.reload();
            },
            error:function(error){
                console.log(error);
                alert('錯誤');
            }
        });
    }
    function showDetail(id){
        
        $.ajax({
            type:'GET',
            url:'/psi/show/' + id,
            dataType:'json',
            success:function(res){
                console.log(res);
                Object.keys(res).forEach(key => {
                    let quantity = res[key];
                    let name = inventoryDict[key];
                    $('#log-detail-table').append(`<tr class='cell'><td>${name}</td><td>${quantity}</td></tr>`);
                });
            },
            error:function(error){
                console.log(error);
                alert('錯誤');
            }
        });
    }

    function getInventory(){
        let inventory = {};
        $('.inventory-check').each(function(){
            if($(this).prop('checked') == true){
                let id = $(this).data('inventory-id');
                let quantity = $('#inventory-quantity-'+id).val();
                if (quantity == ''){ return false; }
                inventory[id] = parseInt(quantity);
            }
        });
        return inventory;
    }

    function reverseInventoryLog(id){
        
        if(!confirm('是否確定回朔？')){ return false; }

        $.ajax({
            type:'POST',
            url:'/psi/reverse/'+id,
            dataType:'json',
            data:{
                _method:'DELETE', 
            },
            success:function(res){
                console.log(res);
                window.location.reload();
            },
            error:function(error){
                console.log(error);
                alert('錯誤');
            }
        });

    }

</script>
@endsection