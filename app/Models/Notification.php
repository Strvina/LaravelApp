<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id', 'product_id', 'type', 'message', 'read_at', 'resolved_at'];

    protected $casts = [
        'read_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Products::class)->withTrashed();
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeUnresolved($query)
    {
        return $query->whereNull('resolved_at');
    }
}
