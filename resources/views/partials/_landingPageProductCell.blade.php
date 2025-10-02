<div class="product-cell">
    <div>
        <div>
            <div class="catImg">
                <a href="{{route('productCategory.show',$key)}}">
                    <img data-src="{{asset('images/cat/landing/'.$key.'.png')}}" 
                         alt="{{$item}}"
                         class="lazy-image"
                         loading="lazy">
                </a>
                <div onclick="location.href='{{route('productCategory.show',$key)}}';" class="P-buy">我要買</div>
            </div>
        </div>
    </div>
</div>