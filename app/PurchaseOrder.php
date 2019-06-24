<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'number',
        'is_uploaded',
        'uploaded_at'
    ];

    protected $casts = [
        'is_uploaded' => 'boolean',
        'uploaded_at' => 'date'
    ];

    public function connection()
    {
        return $this->hasOne(Connection::class);
    }

    public function getIsUploadedAttribute()
    {
        return $this->uploaded_at;
    }
}
