<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;
    protected $table = 'product_reviews';

    protected $fillable = ['product_id', 'user_id', 'Review', 'submission_date', 'Approved_by_Admin', 'store_response'];

    public function User(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'user_id');
    }
    public function Product(): BelongsTo
    {
        return $this->BelongsTo(Product::class, 'product_id');
    }
}
