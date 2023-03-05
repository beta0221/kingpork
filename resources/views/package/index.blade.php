@extends('main')

@section('title','| 組合自由配')

@section('stylesheets')

@endsection

@section('content')




<div class="container">
    <div class="row">
        <div class="col-md-6 col-12">

            @if(Session::has('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
		    @endif

            <h5>Products</h5>

            <div>
                @foreach ($products as $product)
                <div class="btn btn-primary package-product" data-amount="{{$product->item_amount}}" data-id="{{$product->id}}">
                    {{$product->item_amount}} 入
                </div>
                @endforeach
            </div>
            
        
            <h5>Packages</h5>
            <div>
                @foreach ($packageItems as $item)
                <div>
                    <div class="d-inline-block">
                        {{$item->name}}
                    </div>
                    <div class="d-inline-block">
                        <input data-item-id="{{$item->id}}" type="number" class="form-control package-item-input" value="0">
                    </div>
                </div>
                @endforeach
            </div>

            <h6>-</h6>
            <div>
                <div class="btn btn-success package-submit-btn">確定加入</div>
            </div>
            



        </div>
    </div>

    <div class="row d-none">
        <div class="col-12">
            <form id="package-form" action="{{route('addPackageToKart')}}" method="POSt">
                {{ csrf_field() }}
                <div>
                    <input id="form-input-product-id" type="text" name="product_id">
                </div>

                @foreach ($packageItems as $item)
                <div>
                    <input id="form-input-item-{{$item->id}}" type="text" name="kartItems[{{$item->id}}]">    
                </div>
                @endforeach
                
            </form>
        </div>
    </div>
</div>


    




@endsection

@section('scripts')

<script>

var itemAmount = null;


$(document).ready(function(){

    $('.package-product').on('click',function(){
        itemAmount = $(this).data('amount');
        let id = $(this).data('id');
        $('#form-input-product-id').val(id);

        $('.package-item-input').each( (index, element) => {
            $(element).val(0);
        });
    });

    $('.package-item-input').on('change',function(){

        if(!itemAmount) { return }

        let itemId = $(this).data('item-id');
        let value = parseInt($(this).val());

        var total = 0;
        $('.package-item-input').each( (index, element) => {
            if ($(element).data('item-id') == itemId) { return; }
            let _value = parseInt($(element).val());
            total += _value;
        });

        if (total + value > itemAmount) {
            $(this).val(itemAmount - total);
        }
    });


    $('.package-submit-btn').on('click',function(){

        $(this).prop('disabled',true);

        let totalAmount = 0;
        $('.package-item-input').each( (index, element) => {
            totalAmount += parseInt(element.value);
            let itemId = $(element).data('item-id');
            $('#form-input-item-' + itemId).val(element.value);
        });

        if(totalAmount < itemAmount){ 
            return;
        }

        $('#package-form').submit();
    });

});
</script>
@endsection