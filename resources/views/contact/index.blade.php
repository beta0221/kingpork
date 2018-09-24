@extends('admin_main')

@section('title','| 客服管理')

@section('stylesheets')
{{Html::style('css/_contact_index.css')}}
@endsection

@section('content')
<div class="left-outter">
@foreach($contacts as $contact)
	
	<div class="contact-stack">
		<div class="stack-left">
			<div class="left-circle"></div>
		</div>
		<div class="stack-right">
			<div class="stack-top">{{$contact->name}} <font size="2">< {{$contact->email}} ></font> <font size="2">{{$contact->created_at}}</font></div>
			<div class="stack-middle">主旨：{{$contact->title}}</div>
			<div class="stack-bottom">{{$contact->message}}</div>
		</div>
	</div>
		
@endforeach
</div>
<div class="right-outter">
	<div class="conversation-box">
		<div class="conversation-stack left-stack">
			<div class="message">
				<div class="message-top">唐博勝 sjdfksf@gmail.com 2018-05-32 18:00</div>
				<div class="message-middle">無法下單</div>
				<div class="message-bottom">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magni, odit.</div>
			</div>
		</div>
		<div class="conversation-stack right-stack">
			<div class="message">
				<div class="message-top">金園排骨 kingpork@gmail.com 2018-05-32 18:00</div>
				<div class="message-middle">客服回覆</div>
				<div class="message-bottom">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur repellat tempora rem voluptate amet dolore, laudantium explicabo ex enim iste ut est suscipit quo odio eum facilis, eos cupiditate non.</div>
			</div>
		</div>
		<div class="conversation-stack left-stack">
			<div class="message">
				<div class="message-top">唐博勝 sjdfksf@gmail.com 2018-05-32 18:00</div>
				<div class="message-middle">無法下單</div>
				<div class="message-bottom">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magni, odit.</div>
			</div>
		</div>
		<div class="conversation-stack right-stack">
			<div class="message">
				<div class="message-top">金園排骨 kingpork@gmail.com 2018-05-32 18:00</div>
				<div class="message-middle">客服回覆</div>
				<div class="message-bottom">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur repellat tempora rem voluptate amet dolore, laudantium explicabo ex enim iste ut est suscipit quo odio eum facilis, eos cupiditate non.</div>
			</div>
		</div>
	</div>
	<div class="textarea-box">
		<textarea id=""></textarea>
		<button class="btn btn-primary">送出</button>
	</div>
</div>











@endsection

@section('scripts')

@endsection