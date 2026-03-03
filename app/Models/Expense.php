<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    protected $table = "expenses";
    protected $fillable = [
        'name',
        'amount',
        'type',
        'date',
        'user_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope za sve expense zapise određenog korisnika
    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope za konkretan zapis po ID-u i korisniku
    public function scopeOwnedByKey($query, $userId, $id)
    {
        return $query->where('user_id', $userId)->whereKey($id);
    }

    // Scope za filter po tipu ('income' ili 'expense')
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Scope za mesec (month 1-12)
    public function scopeMonth($query, $month)
    {
        return $query->whereMonth('date', $month);
    }
}
