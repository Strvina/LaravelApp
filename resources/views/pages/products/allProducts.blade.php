@extends('layouts.nav-layout')

@section('content')
    <section class="page-shell">
        <div class="page-header">
            <div>
                <p class="stat-label">Inventory module</p>
                <h1 class="page-title">Product catalog</h1>
                <p class="page-subtitle">
                    Browse, filter, and manage product records with category, brand, price-range, and stock-aware views.
                </p>
            </div>
            <a href="{{ route('products.export') }}" class="secondary-btn">Export CSV</a>
        </div>

        @if (auth()->user()->isAdmin())
            <div class="soft-panel mb-8">
                <div class="mb-5">
                    <h2 class="section-title">Add a product</h2>
                    <p class="mt-2 text-sm text-slate-600">Create a new inventory record with the key details your dashboard uses.</p>
                </div>

                <form action="{{ route('product.add') }}" method="POST" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @csrf

                    @if ($errors->any())
                        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700 md:col-span-2 xl:col-span-3">
                            <ul class="space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div>
                        <label for="name" class="field-label">Product name</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" class="text-input">
                    </div>

                    <div>
                        <label for="price" class="field-label">Price</label>
                        <input type="number" id="price" name="price" step="0.01" value="{{ old('price') }}" class="text-input">
                    </div>

                    <div>
                        <label for="stock" class="field-label">Stock</label>
                        <input type="number" id="stock" name="stock" value="{{ old('stock') }}" class="text-input">
                    </div>

                    <div>
                        <label for="category" class="field-label">Category</label>
                        <input type="text" id="category" name="category" value="{{ old('category') }}" class="text-input" placeholder="Food, Tech, Office">
                    </div>

                    <div>
                        <label for="brand" class="field-label">Brand</label>
                        <input type="text" id="brand" name="brand" value="{{ old('brand') }}" class="text-input" placeholder="Apple, Samsung, Local">
                    </div>

                    <div class="md:col-span-2 xl:col-span-3">
                        <label for="description" class="field-label">Description</label>
                        <textarea id="description" name="description" rows="4" class="text-input">{{ old('description') }}</textarea>
                    </div>

                    <div class="md:col-span-2 xl:col-span-3">
                        <button type="submit" class="primary-btn">Save product</button>
                    </div>
                </form>
            </div>

            <div class="panel mb-8">
                <div class="mb-5">
                    <h2 class="section-title">Import products</h2>
                    <p class="mt-2 text-sm text-slate-600">Upload a CSV with columns: name, price, stock, category, brand, description.</p>
                </div>
                <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4 md:flex-row md:items-end">
                    @csrf
                    <div class="flex-1">
                        <label for="csv_file" class="field-label">CSV file</label>
                        <input type="file" id="csv_file" name="csv_file" accept=".csv,text/csv" class="text-input">
                    </div>
                    <button type="submit" class="primary-btn">Import CSV</button>
                </form>
            </div>
        @endif

        <div class="panel mb-8">
            <div class="mb-5">
                <h2 class="section-title">Filter inventory</h2>
                <p class="mt-2 text-sm text-slate-600">Refine the list in real time using the controls below.</p>
            </div>

            <div id="filterForm" data-url="{{ route('products.all') }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                <div class="xl:col-span-2">
                    <label for="search" class="field-label">Search</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" class="text-input"
                        placeholder="Search by product name">
                </div>

                <div>
                    <label for="category-filter" class="field-label">Category</label>
                    <select id="category-filter" name="category" class="select-input">
                        <option value="">All categories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="brand-filter" class="field-label">Brand</label>
                    <select id="brand-filter" name="brand" class="select-input">
                        <option value="">All brands</option>
                        @foreach ($brands as $br)
                            <option value="{{ $br }}" {{ request('brand') == $br ? 'selected' : '' }}>{{ $br }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="min_price" class="field-label">Min price</label>
                    <input type="number" id="min_price" name="min_price" value="{{ request('min_price') }}" class="text-input" placeholder="0">
                </div>

                <div>
                    <label for="max_price" class="field-label">Max price</label>
                    <input type="number" id="max_price" name="max_price" value="{{ request('max_price') }}" class="text-input" placeholder="10000">
                </div>

                <div class="flex items-end gap-3 xl:col-span-6">
                    <label class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700">
                        <input type="checkbox" id="in_stock" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }}>
                        Show only in-stock items
                    </label>

                    <button type="button" id="resetBtn" class="secondary-btn">Reset filters</button>
                </div>
            </div>
        </div>

        <div id="productsList" class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @include('pages.products.partials.products-list', ['products' => $products])
        </div>
    </section>
@endsection
