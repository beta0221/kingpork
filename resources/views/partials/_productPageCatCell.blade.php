<div class="product-cell">
    <div>
        <div>
            <div class="catImg P-pork" id="{{Request::is('productCategory/' . $key) ? 'currentCat' : ''}}"  onclick="location.href='/productCategory/{{$key}}'">
                <a href="{{route('productCategory.show',$key)}}">		
                    <img style="font-size: 0" class="lazy-image" data-src="{{asset('images/cat/menu/' . $key . '.png')}}" alt="{{$item}}" loading="lazy">	
                </a>
                <div class="cat-title-div">{{$item}}</div>
            </div>
        </div>
    </div>
</div>