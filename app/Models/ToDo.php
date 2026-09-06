<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ToDo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'todos';

    protected $fillable = [
        'task',
        'status',
        'user_id',
        'priority',
        'is_recurring',
        'recurrence',
        'last_generated_at',
    ];

    protected $casts = [
        'is_recurring' => 'boolean',
        'last_generated_at' => 'date',
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

    // Admin vidi sve, obican korisnik samo svoje
    public function scopeVisibleTo($query, User $user)
    {
        return $user->isAdmin() ? $query : $query->ownedBy($user->id);
    }

    // Scope za filter po statusu (opcionalno)
    public function scopeStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }

        return $query;
    }

    public function scopePriority($query, $priority)
    {
        if ($priority) {
            return $query->where('priority', $priority);
        }

        return $query;
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where('task', 'like', '%'.$search.'%');
        }

        return $query;
    }
}
