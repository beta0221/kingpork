@foreach($dataList as $i => $data)

	@foreach ($columns as $name=>$title)

        @if ($name=="selector")
            @foreach ($columns['selector'] as $_name => $options)
                <select name="{{$_name}}" class="select-row select-row-{{$data->id}}" style="display:none;width: 160px">
                    @foreach ($options as $option)
                        <option value="{{$option}}">{{$option}}</option>
                    @endforeach
                </select>	
            @endforeach
            
        @else

        <span class="span-row span-row-{{$data->id}} btn btn-sm mb-2">{{$data->$name}}</span>
        <input class="input-row input-row-{{$data->id}}" type="text" value="{{$data->$name}}" name="{{$name}}" style="display:none;">

        @endif


			
	@endforeach
	<button data-id="{{$data->id}}" class="button-edit button-edit-{{$data->id}} btn btn-sm btn-warning">編輯</button>
	<button data-id="{{$data->id}}" class="button-cancel button-cancel-{{$data->id}} btn btn-sm btn-dark" style="display: none">取消</button>
	<button data-id="{{$data->id}}" class="button-submit button-submit-{{$data->id}} btn btn-sm btn-success" style="display: none">送出</button>
	<br>

@endforeach