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
    // Scope vraca sve zadatke koje korisnik poseduje
    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope za traženje zadatka po ID i korisnik
    public function scopeOwnedByKey($query, $userId, $id)
    {
        return $query->where('user_id', $userId)->whereKey($id);
    }

    // Scope za filter po statusu (opcionalno)
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
