<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
   protected $fillable = [
    'category_id',
    'name',
    'type',
    'description',
    'stock',
    'price',
    'status',
    'health',
    'size',
    'gender',
    'availability_date',
    'image'
];

public function category()
    {
        return $this->belongsTo(Category::class);
    }

}