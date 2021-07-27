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

            @foreach ($inventoryCats as $cat)
                <?php if(!isset($inventories[$cat])){ continue; } ?>
                <tr>
                    <td colspan="2" style="background-color: gray">
                        <h5 class="m-0">{{$cat}}</h5>
                    </td>
                </tr>
                @foreach ($inventories[$cat] as $inventory)
                    <tr>
                        <td>{{$inventory->name}}</td>
                        <td>{{$inventory->amount}}</td>
                    </tr>
                @endforeach

            @endforeach
            
        </table>
    </div>

    <div class="col-md-4">
        <div class="mb-2">
            <h3 class="d-inline-block m-0">進銷貨紀錄</h3>
            <button  class="btn btn-sm btn-success align-top" data-toggle="modal" data-target="#inventoryModal">新增</button>
            <button  class="btn btn-sm btn-primary align-top" data-toggle="modal" data-target="#reportModal">匯出報表</button>
        </div>
        
        <table class="table inventory-log-table">
            <tr style="font-weight: 800">
                <td>日期</td>
                <td>事件</td>
                <td>備註</td>
                <td>通路</td>
                <td>-</td>
            </tr>

            <?php 
                $color = [
                    'purchase'=>'green',
                    'produce'=>'#276ecc',
                    'pack'=>'orange',
                    'sale'=>'red',
                ];
            ?>
            @foreach ($inventoryLogs as $log)
            <tr style="color: {{$color[$log->action]}};cursor:pointer" class="log-row" data-id="{{$log->id}}">
                <td>{{$log->date}}</td>
                <td>{{$actions[$log->action]}}</td>
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
                <select class="form-control" name="action" id="action-selector">
                    <option value="">請選擇</option>
                    @foreach ($actions as $value => $name)
                        <option value="{{$value}}">{{$name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="retailer-row" class="mb-2" style="display: none">
                <select class="form-control" name="retailer_id">
                    <option value="">零售商</option>
                    @foreach ($retailers as $retailer)
                        <option value="{{$retailer->id}}">{{$retailer->name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-2">
                <span>備註</span>
                <input class="form-control" type="text" name="event" placeholder="備註">
            </div>

            <hr>
            
            <div>
                @foreach ($inventoryCats as $cat)
                    <?php if(!isset($inventories[$cat])){ continue; } ?>
                    <div class="cat cat-{{$cat}}">
                        <h6 class="mt-2">{{$cat}}<span id="{{$cat}}"></span></h6>
                        @foreach ($inventories[$cat] as $inventory)
                            <input id="inventory-check-{{$inventory->id}}" data-inventory-id="{{$inventory->id}}" class="inventory-check ml-2" type="checkbox">
                            <span>{{$inventory->name}}</span>
                            <input id="inventory-quantity-{{$inventory->id}}" class="inventory-quantity" type="number"><br>
                        @endforeach
                    </div>
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


<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModal" aria-hidden="true">
	<div class="modal-dialog" role="document">
	  <div class="modal-content">
		<div class="modal-header">
		  <h5 class="modal-title" id="reportModal">匯出報表</h5>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
        <form action="/psi/report" target="_blank">
            <div class="modal-body">
                <div class="mb-2">
                    <span>日期區間</span>
                    <input class="form-control" type="date" name="from_date">
                </div>

                <div class="mb-2">
                    <span>至</span>
                    <input class="form-control" type="date" name="to_date">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">匯出</button>
                <div type="button" class="btn btn-secondary" data-dismiss="modal">關閉</div>
            </div>
        </form>

	  </div>
	</div>
</div>


@endsection

@section('scripts')
<script>
    const inventoryDict = {!!json_encode($inventoryDict)!!};
    const actionMap = {!!json_encode($actionMap)!!};

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

        $('#action-selector').on('change',function(){
            $('.cat').show();
            let value = $(this).val();
            if(value == ''){ return; }

            let map = actionMap[value];
            Object.keys(map).forEach(function(key){
                let v = map[key];
                if(v != null){
                    $('span#' + key).html('('+ v +')');
                }else{
                    $('.cat-' + key).hide();
                }
            })

            hideRetailerRow();
            if(value == 'sale'){
                showRetailerRow();
            }
            
        });

        $('.inventory-quantity').on('change',function(){
            let value = $(this).val();
            if(value < 0){
                value = 0 - value;
                $(this).val(value);
            }
        });

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
        let action = $('select[name="action"]').val();
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
                // console.log(res);
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
                // console.log(res);
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
                // console.log(res);
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