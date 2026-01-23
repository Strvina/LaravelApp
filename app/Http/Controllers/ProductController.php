<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;

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
if($products === null) {
    return redirect('/');//vrati na pocetnu stranu ako proizvod ne postoji
}
    return view('pages/product', [
            'products'=> $products
        ]);
    }

    public function allProducts()
    {
       //$products = Products::all();
       return view("pages.allProducts", [
           'products' => Products::all()//vraca sve proizvode iz tabele products
       ]);
    }

    public function addProduct(Request $request)
    {

//ovo maltene radi if(isset($_POST['name'])&& is_string($_POST['name']))
        $request->validate([
            'name' => 'required|string|max:64',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'description' => 'required|string|max:255|min:10'
        ]);//validacija podataka iz forme

        Products::create($request->all());//unos podataka u bazu, request->all() vraca sve podatke iz forme tj niz ovako ['name'=>..., 'price'=>..., 'quantity'=>..., 'description'=>...]
return redirect()->back();
        }

        public function delete($id)
        {
            $product = Products::firstWhere('id',$id);//trazi proizvod po id-u
            if($product=== null) {
                return redirect()->back();
            }
            $product->delete();
            return redirect()->route('products.all');
        }
}
