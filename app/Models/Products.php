<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $table = "products";//ovim kazem o ova klasa je vezana za tabelu products u bazi
    protected $fillable = [
        'name',
        'price',
        'quantity',
        'on_sale',
        'description'
    ];//polja koja se mogu uneti, modifikovati itd.
}
