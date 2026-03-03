@forelse ($products as $product)
    <div style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h3>{{ $product->name }}</h3>

        @if ($product->category)
            <p><strong>Kategorija:</strong> <span
                    style="background: #e9ecef; padding: 2px 6px; border-radius: 4px;">{{ $product->category }}</span>
            </p>
        @endif

        @if ($product->brand)
            <p><strong>Brend:</strong> {{ $product->brand }}</p>
        @endif

        <p><strong>Cena:</strong> <span style="font-size: 1.3em; color: #28a745;">{{ $product->price }} RSD</span>
        </p>

        <p><strong>Na stanju:</strong>
            @if ($product->stock > 0)
                <span style="color: #28a745; font-weight: bold;">✓ {{ $product->stock }} kom</span>
            @else
                <span style="color: #dc3545; font-weight: bold;">✗ Nema dostupno</span>
            @endif
        </p>

        <p>{{ Str::limit($product->description, 100) }}</p>

        <a href="{{ route('product.single', ['productName' => $product->name]) }}"
            style="display: inline-block; padding: 8px 12px; background: #007bff; color: white; text-decoration: none; border-radius: 4px;">
            Detalji →
        </a>
    </div>
@empty
    <div style="grid-column: 1/-1; text-align: center; padding: 20px; color: #666;">
        <p>Nema proizvoda koji odgovaraju navedenim filtrima.</p>
    </div>
@endforelse

{{-- pagination links --}}
@if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="w-full mt-6">
        {{ $products->links() }}
    </div>
@endif
