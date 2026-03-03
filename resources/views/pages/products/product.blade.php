@extends('layouts.nav-layout')

@section('content')
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="margin-bottom: 20px;">{{ $products->name }}</h1>

        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">

            @if ($products->category)
                <p><strong>Kategorija:</strong> <span
                        style="background: #e9ecef; padding: 4px 8px; border-radius: 4px;">{{ $products->category }}</span>
                </p>
            @endif

            @if ($products->brand)
                <p><strong>Brend:</strong> {{ $products->brand }}</p>
            @endif

            <p><strong>Cena:</strong> <span style="font-size: 1.5em; color: #dc3545;">{{ $products->price }} RSD</span></p>

            @if ($products->stock > 0)
                <p><strong>Na stanju:</strong> <span style="color: #28a745; font-weight: bold;">✓ {{ $products->stock }} kom
                        dostupno</span></p>
            @else
                <p><strong>Na stanju:</strong> <span style="color: #dc3545; font-weight: bold;">✗ Trenutno nema
                        dostupno</span></p>
            @endif

            <p><strong>Opis:</strong></p>
            <p style="background: white; padding: 15px; border-radius: 4px;">{{ $products->description }}</p>

            <p style="font-size: 0.9em; color: #6c757d;">
                <strong>Dodano:</strong> {{ $products->created_at->format('d.m.Y H:i') }}
            </p>
        </div>

        <div style="margin-top: 30px;">
            <a href="{{ route('products.all') }}"
                style="display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px;">
                ← Nazad na listu
            </a>
            <a href="{{ route('product.delete', ['id' => $products->id]) }}"
                style="display: inline-block; padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 4px; onclick=\"return confirm('Sigurno obrisati?')\";">
                🗑️ Obriši proizvod
            </a>
        </div>
    </div>
@endsection
