<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToDo extends Model
{
    protected $table = "ToDo";
    protected $fillable = [
        "task",
        "status"
        ];

        
}
