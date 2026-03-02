<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ToDo extends Model
{
    use HasFactory;
    protected $table = "ToDo";
    protected $fillable = [
        "task",
        "status",
        "user_id",
        "priority",
        "is_recurring",
        "recurrence",
        "last_generated_at",
        ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
