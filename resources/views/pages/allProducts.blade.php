@extends('layouts.nav-layout')
@section('content')

@foreach ($products as $product)
    <h2>{{ $product->name }}</h2>
    <p>Price: {{ $product->price }}</p>
    <p>Description: {{ $product->description }}</p>
    <a href="{{ route('product.single', ['productName' => $product->name]) }}">Pogledaj proizvod</a>
@endforeach

<form action="{{ route('product.add') }}" method="POST">
    @csrf
    @if($errors->any())
        <div style="color:red;">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <label for="name">Naziv proizvoda</label><br>
    <input type="text" id="name" name="name">
    <br><br>

    <label for="price">Cena</label><br>
    <input type="number" id="price" name="price">
    <br><br>

    <label for="quantity">Količina</label><br>
    <input type="number" id="quantity" name="quantity">
    <br><br>

    <label for="on_sale">Na akciji</label><br>
    <select id="on_sale" name="on_sale">
        <option value="0">Ne</option>
        <option value="1">Da</option>
    </select>
    <br><br>

    <label for="description">Opis</label><br>
    <textarea id="description" name="description" rows="4"></textarea>
    <br><br>

    <button type="submit">Sačuvaj proizvod</button>

</form>

@endsection

