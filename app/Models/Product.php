<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;

    // protected $dates = ['deleted_at'];
    protected $table = 'products';

    protected $fillable = ['name', 'price', 'rating', 'image_path', 'sub_category_id', 'main_category_id'];

    public function BelongSubCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }
    public function BelongMainCategory(): BelongsTo
    {
        return $this->belongsTo(MainCategory::class, 'main_category_id');
    }

    public function Rattings()
    {
        return $this->hasMany(Ratting::class);
    }
    public function Reviws()
    {
        return $this->hasMany(Review::class);
    }

    public function User(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'user_id');
    }
    public function Product(): BelongsTo
    {
        return $this->BelongsTo(Product::class, 'product_id');
    }
}
