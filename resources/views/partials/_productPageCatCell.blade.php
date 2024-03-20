<div class="product-cell">
    <div>
        <div>
            <div class="catImg P-pork" id="{{Request::is('productCategory/' . $key) ? 'currentCat' : ''}}"  onclick="location.href='/productCategory/{{$key}}'">
                <a href="{{route('productCategory.show',$key)}}">		
                    <img src="{{asset('images/cat/menu/' . $key . '.png')}}" alt="{{$item}}">	
                </a>
                <div class="cat-title-div">{{$item}}</div>
            </div>
        </div>
    </div>
</div>