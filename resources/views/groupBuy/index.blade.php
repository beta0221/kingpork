@extends('main')

@section('title','| VIP團購區')

@section('stylesheets')
{{Html::style('css/_groupBuy.css')}}
{{Html::style('css/_kart.css')}}
{{Html::style('css/_groupBuy_kart.css')}}
@endsection

@section('content')



@endsection

@section('scripts')
{{ Html::script('js/bootstrap/bootstrap.min.js') }}
{{ Html::script('js/_kart.js') }}
{{ Html::script('js/_groupBuy_kart.js') }}
@endsection