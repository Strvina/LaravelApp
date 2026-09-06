@forelse ($products as $product)
    <article class="panel flex h-full flex-col justify-between">
        <div>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="stat-label">{{ $product->category ?: 'General' }}</p>
                    <h3 class="mt-2 text-2xl font-bold text-slate-900">{{ $product->name }}</h3>
                </div>
                <span class="badge {{ $product->stock > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-700' }}">
                    {{ $product->stock > 0 ? 'In stock' : 'Out of stock' }}
                </span>
            </div>

            @if ($product->brand)
                <p class="mt-4 text-sm font-medium text-slate-500">Brand: {{ $product->brand }}</p>
            @endif

            <p class="mt-4 text-sm leading-6 text-slate-600">{{ Str::limit($product->description, 120) }}</p>
        </div>

        <div class="mt-6 flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Price</p>
                <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($product->price, 2) }} RSD</p>
            </div>
            <div class="text-right">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Units</p>
                <p class="mt-1 text-lg font-semibold text-slate-700">{{ $product->stock }}</p>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('product.single', ['product' => $product->slug]) }}" class="secondary-btn">View details</a>
            @if (auth()->user()->isAdmin())
                <a href="{{ route('product.edit', $product->id) }}" class="secondary-btn">Edit</a>
                <form method="POST" action="{{ route('product.delete', $product->id) }}"
                    onsubmit="return confirm('Delete this product? It can be restored from the trash later.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="danger-btn">Delete</button>
                </form>
            @endif
        </div>
    </article>
@empty
    <div class="panel md:col-span-2 xl:col-span-3">
        <p class="text-lg font-semibold text-slate-900">No products matched the current filters.</p>
        <p class="mt-2 text-sm text-slate-600">Try widening the price range or clearing one of the category or brand filters.</p>
    </div>
@endforelse

@if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="md:col-span-2 xl:col-span-3">
        {{ $products->links() }}
    </div>
@endif
