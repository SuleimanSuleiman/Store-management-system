<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ratting extends Model
{
    use HasFactory;

    protected $table = 'ratings';

    protected $fillable = ['product_id', 'user_id'];


    public function User(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'user_id');
    }
    public function Product(): BelongsTo
    {
        return $this->BelongsTo(Product::class, 'product_id');
    }
}
