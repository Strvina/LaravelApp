<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Products;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index($productName)
    {
        $product = Products::firstWhere('name', $productName);

        if ($product === null) {
            return redirect('/');
        }

        return view('pages.products.product', [
            'product' => $product,
        ]);
    }

    public function allProducts(Request $request)
    {
        $query = Products::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
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
        $product = Products::create($request->validated());
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

        return view('pages.products.edit', compact('product'));
    }

    public function update(StoreProductRequest $request, $id)
    {
        $product = Products::findOrFail($id);
        $oldValues = $product->only(['name', 'price', 'stock', 'category', 'brand', 'on_sale', 'description']);

        $product->update($request->validated());
        NotificationService::createLowStockAlert($product);

        ActivityLogService::log(
            'updated',
            $product,
            $oldValues,
            $product->only(['name', 'price', 'stock', 'category', 'brand', 'on_sale', 'description']),
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

        $oldValues = $product->only(['name', 'price', 'stock', 'category', 'brand', 'on_sale', 'description']);
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
        $filename = 'products-export-' . now()->format('Y-m-d-His') . '.csv';
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

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $handle = fopen($request->file('csv_file')->getRealPath(), 'r');
        $headers = fgetcsv($handle);
        $created = 0;
        $failed = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($headers, $row);

            $validator = Validator::make($data, [
                'name' => ['required', 'string', 'max:64'],
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

            $product = Products::create($validator->validated());
            NotificationService::createLowStockAlert($product);
            $created++;
        }

        fclose($handle);

        ActivityLogService::log('imported', null, null, [
            'created' => $created,
            'failed' => $failed,
        ], 'Products imported from CSV');

        return redirect()->route('products.all')->with('success', "{$created} products imported. {$failed} rows skipped.");
    }
}
