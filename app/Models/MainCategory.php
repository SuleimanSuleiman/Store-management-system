<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MainCategory extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'main_categories';

    protected $fillable = ['name', 'branch_id'];

    public function subcategories(): HasMany
    {
        return $this->hasMany(SubCategory::class);
    }

    public function BelongBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function DeleteWithSubCategories()
    {
        $this->delete();
        $this->update(['deleted_at' => now()->addDays(30)]);
    }

    public function restoreDeletedMainCategory()
    {
        $this->restore();
    }
}