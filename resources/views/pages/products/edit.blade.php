@extends('layouts.nav-layout')

@section('content')
    <section class="page-shell">
        <div class="page-header">
            <div>
                <p class="stat-label">Inventory module</p>
                <h1 class="page-title">Edit product</h1>
                <p class="page-subtitle">Update inventory details without leaving the product management flow.</p>
            </div>
            <a href="{{ route('products.all') }}" class="secondary-btn">Back to inventory</a>
        </div>

        <div class="panel max-w-4xl">
            <form action="{{ route('product.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="field-label">Product name</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $product->name) }}" class="text-input">
                    @error('name')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="sku" class="field-label">SKU</label>
                    <input id="sku" name="sku" type="text" value="{{ old('sku', $product->sku) }}" class="text-input" placeholder="Optional">
                    @error('sku')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="price" class="field-label">Price</label>
                    <input id="price" name="price" type="number" step="0.01" value="{{ old('price', $product->price) }}" class="text-input">
                    @error('price')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="stock" class="field-label">Stock</label>
                    <input id="stock" name="stock" type="number" value="{{ old('stock', $product->stock) }}" class="text-input">
                    @error('stock')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="category" class="field-label">Category</label>
                    <input id="category" name="category" type="text" value="{{ old('category', $product->category) }}" class="text-input">
                    @error('category')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="brand" class="field-label">Brand</label>
                    <input id="brand" name="brand" type="text" value="{{ old('brand', $product->brand) }}" class="text-input">
                    @error('brand')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="description" class="field-label">Description</label>
                    <textarea id="description" name="description" rows="5" class="text-input">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="image" class="field-label">Product image</label>
                    @if ($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="mb-3 h-32 w-32 rounded-2xl border border-slate-200 object-cover">
                    @endif
                    <input id="image" name="image" type="file" accept="image/*" class="text-input">
                    <p class="mt-2 text-xs text-slate-500">Optional. Uploading a new image replaces the current one.</p>
                    @error('image')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2 flex gap-3">
                    <button type="submit" class="primary-btn">Save changes</button>
                    <a href="{{ route('product.single', ['product' => $product->slug]) }}" class="secondary-btn">View product</a>
                </div>
            </form>
        </div>
    </section>
@endsection
