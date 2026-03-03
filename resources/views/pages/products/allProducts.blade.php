@extends('layouts.nav-layout')
@section('content')

    <!-- FILTER SECTION -->
    <div style="background: #f5f5f5; padding: 20px; margin-bottom: 30px; border-radius: 8px;">
        <h3>🔍 Filtriranje proizvoda (real-time)</h3>
        <div id="filterForm" data-url="{{ route('products.all') }}"
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">

            <!-- Category Filter -->
            <div>
                <label for="category">Kategorija:</label>
                <select id="category" name="category" style="width: 100%; padding: 8px;">
                    <option value="">-- Sve kategorije --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Brand Filter -->
            <div>
                <label for="brand">Brend:</label>
                <select id="brand" name="brand" style="width: 100%; padding: 8px;">
                    <option value="">-- Svi brendovi --</option>
                    @foreach ($brands as $br)
                        <option value="{{ $br }}" {{ request('brand') == $br ? 'selected' : '' }}>
                            {{ $br }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Min Price -->
            <div>
                <label for="min_price">Min. cena:</label>
                <input type="number" id="min_price" name="min_price" placeholder="0" value="{{ request('min_price') }}"
                    style="width: 100%; padding: 8px;">
            </div>

            <!-- Max Price -->
            <div>
                <label for="max_price">Max. cena:</label>
                <input type="number" id="max_price" name="max_price" placeholder="10000" value="{{ request('max_price') }}"
                    style="width: 100%; padding: 8px;">
            </div>

            <!-- In Stock Checkbox -->
            <div>
                <label>
                    <input type="checkbox" id="in_stock" name="in_stock" value="1"
                        {{ request('in_stock') ? 'checked' : '' }}>
                    Samo dostupni
                </label>
            </div>

            <!-- Reset Button -->
            <div style="display: flex; gap: 10px; align-items: flex-end;">
                <button type="button" id="resetBtn"
                    style="padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    ⟳ Reset
                </button>
            </div>
        </div>
    </div>

    <!-- PRODUCTS LIST -->
    <div id="productsList"
        style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-bottom: 40px;">
        @include('pages.products.partials.products-list', ['products' => $products])
    </div>

    <!-- ADD NEW PRODUCT FORM -->
    <div style="background: #fff3cd; padding: 20px; border-radius: 8px;">
        <h3>➕ Dodaj novi proizvod</h3>

        <form action="{{ route('product.add') }}" method="POST"
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            @csrf

            @if ($errors->any())
                <div style="grid-column: 1/-1; color: #721c24; background: #f8d7da; padding: 12px; border-radius: 4px;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <label for="name">Naziv proizvoda *</label><br>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                    style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>

            <div>
                <label for="price">Cena *</label><br>
                <input type="number" id="price" name="price" step="0.01" value="{{ old('price') }}"
                    style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>

            <div>
                <label for="stock">Količina na stanju *</label><br>
                <input type="number" id="stock" name="stock" value="{{ old('stock') }}"
                    style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>

            <div>
                <label for="category">Kategorija</label><br>
                <input type="text" id="category" name="category" placeholder="npr. Hrana, Tehnologija..."
                    value="{{ old('category') }}" style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>

            <div>
                <label for="brand">Brend</label><br>
                <input type="text" id="brand" name="brand" placeholder="npr. Apple, Samsung..."
                    value="{{ old('brand') }}" style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>

            <div>
                <label for="description">Opis *</label><br>
                <textarea id="description" name="description" rows="3" style="width: 100%; padding: 8px; margin-top: 5px;">{{ old('description') }}</textarea>
            </div>

            <div style="grid-column: 1/-1;">
                <button type="submit"
                    style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1em;">
                    ✓ Sačuvaj proizvod
                </button>
            </div>
        </form>
    </div>


@endsection
