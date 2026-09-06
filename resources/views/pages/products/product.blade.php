@extends('layouts.nav-layout')

@section('content')
    <section class="page-shell">
        <div class="panel">
            <div class="flex flex-col gap-8 lg:flex-row lg:items-start lg:justify-between">
                <div class="max-w-2xl">
                    <p class="stat-label">{{ $product->category ?: 'General inventory' }}</p>
                    <h1 class="mt-3 text-4xl font-bold text-slate-900">{{ $product->name }}</h1>

                    <div class="mt-5 flex flex-wrap gap-3">
                        @if ($product->brand)
                            <span class="badge bg-slate-100 text-slate-700">{{ $product->brand }}</span>
                        @endif
                        <span class="badge {{ $product->stock > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-700' }}">
                            {{ $product->stock > 0 ? 'Available' : 'Unavailable' }}
                        </span>
                    </div>

                    <p class="mt-6 text-base leading-7 text-slate-600">{{ $product->description }}</p>
                </div>

                <div class="w-full max-w-sm rounded-3xl border border-slate-200 bg-slate-50 p-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Product snapshot</p>
                    <div class="mt-5 space-y-4">
                        <div>
                            <p class="text-sm text-slate-500">Price</p>
                            <p class="text-3xl font-bold text-slate-900">{{ number_format($product->price, 2) }} RSD</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Stock</p>
                            <p class="text-xl font-semibold text-slate-900">{{ $product->stock }} units</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Brand</p>
                            <p class="text-base font-medium text-slate-900">{{ $product->brand ?: 'Not specified' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('products.all') }}" class="secondary-btn">Back to catalog</a>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('product.edit', $product->id) }}" class="secondary-btn">Edit product</a>
                    <form method="POST" action="{{ route('product.delete', $product->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="danger-btn">Delete product</button>
                    </form>
                @endif
            </div>
        </div>
    </section>
@endsection
