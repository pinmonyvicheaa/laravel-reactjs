<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'description', 'price', 'stock', 'image_url', 'category_id'];
    protected $dates = ['deleted_at'];

    public function category(){
        return $this->belongsTo(Category::class);
    }
}
