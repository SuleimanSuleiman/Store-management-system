<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'sub_categories';

    protected $fillable = ['name', 'type', 'Desc', 'main_category_id', 'model', 'size', 'market'];


    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function BelongMainCategory(): BelongsTo
    {
        return $this->belongsTo(MainCategory::class, 'main_category_id');
    }
}