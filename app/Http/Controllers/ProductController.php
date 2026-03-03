<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductRequest;

class ProductController extends Controller
{
    public function index($productName)
    {


        //**$productList = ['hleb'=> 100, 'mleko' => 150, 'jogurt' => 80];
        //if(!array_key_exists($productName, $productList)) {
        //    die("Proizvod ne postoji");
        //}

        // $productPrice= $productList[$productName];
        //dd($productPrice);//dd radi var_dump + die
        //

        // umesto da pisem SELECT * FROM users
        // kada mapiramo pomocu modela mi samo napisemo User::all();broken
        //znaci automatski se povezujemo na bazu, ne moramo da pisemo query osim ako su bas kompleksni
        //Nema escape podataka, nema brige oko toga dal ce zajebemo nesto

        //hocemo ovo da uradimo SELECT * FROM products WHERE name = $productName
        $products = Products::firstWhere("name", $productName);//get vraca niz, a first vraca Objekat(s 1 podatkom)
        if ($products === null) {
            return redirect('/');//vrati na pocetnu stranu ako proizvod ne postoji
        }
        return view('pages/products/product', [
            'products' => $products
        ]);
    }

    public function allProducts(Request $request)
    {
        $query = Products::query();

        // Filtriranje po kategoriji
        if ($request->has('category') && $request->category) {
            $query->byCategory($request->category);
        }

        // Filtriranje po brendu
        if ($request->has('brand') && $request->brand) {
            $query->byBrand($request->brand);
        }

        // Filtriranje po ceni
        $minPrice = $request->has('min_price') && $request->min_price ? (int) $request->min_price : null;
        $maxPrice = $request->has('max_price') && $request->max_price ? (int) $request->max_price : null;
        $query->byPriceRange($minPrice, $maxPrice);

        // Filtriranje dostupnosti (samo na stanju)
        if ($request->has('in_stock') && $request->in_stock) {
            $query->inStock();
        }

        // Get all unique categories and brands for filter UI
        $categories = Products::distinct()->pluck('category')->filter();
        $brands = Products::distinct()->pluck('brand')->filter();

        // paginate results to 10 per page, keep filters in query string
        $products = $query->paginate(8)->appends($request->except('page'));

        // Ako je AJAX zahtev, vrati HTML fragment sa proizvodima iz prave putanje
        if ($request->ajax()) {
            return view("pages.products.partials.products-list", [
                'products' => $products
            ]);
        }

        return view("pages.products.allProducts", [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'filters' => $request->all() // Pass current filters to view
        ]);
    }

    public function addProduct(StoreProductRequest $request)
    {
        // Samo validirani podaci
        Products::create($request->validated());

        return redirect()->back()->with('success', 'Proizvod dodat!');
    }

    public function delete($id)
    {
        $product = Products::firstWhere('id', $id);//trazi proizvod po id-u
        if ($product === null) {
            return redirect()->back();
        }
        $product->delete();
        return redirect()->route('products.all');
    }
}
