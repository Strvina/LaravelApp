@extends('layouts.nav-layout')

@section('content')
    <section class="page-shell">
        <div class="page-header">
            <div>
                <p class="stat-label">Inventory module</p>
                <h1 class="page-title">Deleted products</h1>
                <p class="page-subtitle">Restore products that were removed by mistake, or permanently delete them.</p>
            </div>
            <a href="{{ route('products.all') }}" class="secondary-btn">Back to inventory</a>
        </div>

        <div class="panel">
            <div class="-mx-6 overflow-x-auto px-6 sm:mx-0 sm:px-0">
                <table class="min-w-[40rem] text-sm sm:min-w-full">
                    <thead class="border-b border-slate-200 text-left text-slate-500">
                        <tr>
                            <th class="px-3 py-3">Name</th>
                            <th class="px-3 py-3">SKU</th>
                            <th class="px-3 py-3">Deleted at</th>
                            <th class="px-3 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr class="border-b border-slate-100">
                                <td class="px-3 py-4 font-medium text-slate-900">{{ $product->name }}</td>
                                <td class="px-3 py-4 text-slate-600">{{ $product->sku ?? '—' }}</td>
                                <td class="px-3 py-4 text-slate-600">{{ $product->deleted_at->format('Y-m-d H:i') }}</td>
                                <td class="px-3 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <form method="POST" action="{{ route('product.restore', $product->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="secondary-btn whitespace-nowrap">Restore</button>
                                        </form>
                                        <form method="POST" action="{{ route('product.force-delete', $product->id) }}"
                                            onsubmit="return confirm('Permanently delete this product? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="danger-btn whitespace-nowrap">Delete forever</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-8 text-center text-slate-500">
                                    No deleted products.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($products->hasPages())
                <div class="mt-6">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
