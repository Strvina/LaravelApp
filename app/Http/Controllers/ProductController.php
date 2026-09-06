<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Products;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    public function show(Products $product): View
    {
        return view('pages.products.product', [
            'product' => $product,
        ]);
    }

    public function allProducts(Request $request)
    {
        $query = Products::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('brand')) {
            $query->byBrand($request->brand);
        }

        $minPrice = $request->filled('min_price') ? (int) $request->min_price : null;
        $maxPrice = $request->filled('max_price') ? (int) $request->max_price : null;
        $query->byPriceRange($minPrice, $maxPrice);

        if ($request->boolean('in_stock')) {
            $query->inStock();
        }

        $categories = Products::distinct()->pluck('category')->filter();
        $brands = Products::distinct()->pluck('brand')->filter();
        $products = $query->paginate(8)->appends($request->except('page'));

        if ($request->ajax()) {
            return view('pages.products.partials.products-list', [
                'products' => $products,
            ]);
        }

        return view('pages.products.allProducts', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'filters' => $request->all(),
        ]);
    }

    public function addProduct(StoreProductRequest $request)
    {
        $this->authorize('create', Products::class);

        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product = Products::create($data);
        NotificationService::createLowStockAlert($product);

        ActivityLogService::log(
            'created',
            $product,
            null,
            $product->only(['name', 'price', 'stock', 'category', 'brand', 'on_sale', 'description']),
            'Product created'
        );

        return redirect()->back()->with('success', 'Product added successfully.');
    }

    public function edit($id)
    {
        $product = Products::findOrFail($id);
        $this->authorize('update', $product);

        return view('pages.products.edit', compact('product'));
    }

    public function update(StoreProductRequest $request, $id)
    {
        $product = Products::findOrFail($id);
        $this->authorize('update', $product);
        $oldValues = $product->only(['name', 'slug', 'sku', 'price', 'stock', 'category', 'brand', 'on_sale', 'description']);

        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        NotificationService::createLowStockAlert($product);
        NotificationService::resolveLowStockAlert($product);

        ActivityLogService::log(
            'updated',
            $product,
            $oldValues,
            $product->only(['name', 'slug', 'sku', 'price', 'stock', 'category', 'brand', 'on_sale', 'description']),
            'Product updated'
        );

        return redirect()->route('products.all')->with('success', 'Product updated successfully.');
    }

    public function delete($id)
    {
        $product = Products::firstWhere('id', $id);
        if ($product === null) {
            return redirect()->back();
        }
        $this->authorize('delete', $product);

        $oldValues = $product->only(['name', 'slug', 'sku', 'price', 'stock', 'category', 'brand', 'on_sale', 'description']);
        $product->delete();

        ActivityLogService::log(
            'deleted',
            $product,
            $oldValues,
            null,
            'Product deleted'
        );

        return redirect()->route('products.all')->with('success', 'Product deleted successfully.');
    }

    public function export()
    {
        $filename = 'products-export-'.now()->format('Y-m-d-His').'.csv';
        $products = Products::orderBy('name')->get(['name', 'price', 'stock', 'category', 'brand', 'description']);

        return response()->streamDownload(function () use ($products) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Price', 'Stock', 'Category', 'Brand', 'Description']);

            foreach ($products as $product) {
                fputcsv($handle, [
                    $product->name,
                    $product->price,
                    $product->stock,
                    $product->category,
                    $product->brand,
                    $product->description,
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function template(): StreamedResponse
    {
        $this->authorize('import', Products::class);

        $filename = 'products-import-template.csv';

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['sku', 'name', 'price', 'stock', 'category', 'brand', 'description']);
            fputcsv($handle, ['SKU-001', 'Example product', '1000', '12', 'Office', 'OpsDesk', 'Example product description.']);
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function import(Request $request)
    {
        $this->authorize('import', Products::class);

        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
            'mode' => ['nullable', 'in:create,upsert'],
        ]);

        $handle = fopen($request->file('csv_file')->getRealPath(), 'r');
        if ($handle === false) {
            return redirect()->back()->withErrors(['csv_file' => 'CSV file could not be opened.']);
        }

        $headers = fgetcsv($handle);
        if ($headers === false) {
            fclose($handle);

            return redirect()->back()->withErrors(['csv_file' => 'CSV file is empty.']);
        }

        $headers = array_map(fn ($header) => strtolower(trim((string) $header)), $headers);
        $requiredHeaders = ['name', 'price', 'stock', 'description'];
        $missingHeaders = array_diff($requiredHeaders, $headers);

        if ($missingHeaders !== []) {
            fclose($handle);

            return redirect()->back()->withErrors([
                'csv_file' => 'CSV file is missing required columns: '.implode(', ', $missingHeaders).'.',
            ]);
        }

        $created = 0;
        $updated = 0;
        $failed = 0;
        $mode = $request->input('mode', 'create');

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($headers)) {
                $failed++;

                continue;
            }

            $data = array_combine($headers, $row);
            $data['sku'] = blank($data['sku'] ?? null) ? null : trim($data['sku']);

            $skuRule = $mode === 'upsert'
                ? ['nullable', 'string', 'max:64']
                : ['nullable', 'string', 'max:64', Rule::unique('products', 'sku')];

            $validator = Validator::make($data, [
                'name' => ['required', 'string', 'max:64'],
                'sku' => $skuRule,
                'price' => ['required', 'numeric', 'min:0'],
                'stock' => ['required', 'integer', 'min:0'],
                'category' => ['nullable', 'string', 'max:64'],
                'brand' => ['nullable', 'string', 'max:64'],
                'description' => ['required', 'string', 'min:10', 'max:255'],
            ]);

            if ($validator->fails()) {
                $failed++;

                continue;
            }

            $validated = $validator->validated();
            $lookup = $validated['sku'] ?? null
                ? ['sku' => $validated['sku']]
                : ['name' => $validated['name']];

            if ($mode === 'upsert') {
                $product = Products::withTrashed()->firstOrNew($lookup);
                $wasRecentlyCreated = ! $product->exists;
                $product->fill($validated);

                if ($product->trashed()) {
                    $product->restore();
                } else {
                    $product->save();
                }

                $wasRecentlyCreated ? $created++ : $updated++;
            } else {
                $product = Products::create($validated);
                $created++;
            }

            NotificationService::createLowStockAlert($product);
            NotificationService::resolveLowStockAlert($product);
        }

        fclose($handle);

        ActivityLogService::log('imported', null, null, [
            'created' => $created,
            'updated' => $updated,
            'failed' => $failed,
        ], 'Products imported from CSV');

        return redirect()->route('products.all')->with('success', "{$created} products imported. {$updated} products updated. {$failed} rows skipped.");
    }

    public function trash(): View
    {
        $this->authorize('import', Products::class);

        $products = Products::onlyTrashed()->latest('deleted_at')->paginate(10);

        return view('pages.products.trash', compact('products'));
    }

    public function restore($id): RedirectResponse
    {
        $product = Products::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $product);

        $product->restore();

        ActivityLogService::log(
            'restored',
            $product,
            null,
            $product->only(['name', 'slug', 'sku', 'price', 'stock', 'category', 'brand']),
            'Product restored'
        );

        return redirect()->route('products.trash')->with('success', 'Product restored successfully.');
    }

    public function forceDelete($id): RedirectResponse
    {
        $product = Products::onlyTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $product);
        $oldValues = $product->only(['name', 'slug', 'sku', 'price', 'stock', 'category', 'brand', 'on_sale', 'description']);

        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->forceDelete();

        ActivityLogService::log(
            'force_deleted',
            $product,
            $oldValues,
            null,
            'Product permanently deleted'
        );

        return redirect()->route('products.trash')->with('success', 'Product permanently deleted.');
    }
}
