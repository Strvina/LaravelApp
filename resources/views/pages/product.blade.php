@extends('layouts.nav-layout')

@section('content')
<h1>Ime proizvoda je {{ $products->name }}</h1>
<p>ovaj proizvod kosta {{ $products->price }}</p>
<a href="{{ route('product.delete', ['id' => $products->id]) }}">Obriši proizvod</a>
@endsection
