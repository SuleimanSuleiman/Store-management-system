<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;


    protected $dates = ['deleted_at'];
    protected $table = 'branches';

    protected $fillable = ['name', 'address', 'admin_id'];

    public function Admin(): HasOne
    {
        return $this->hasOne(Admin::class);
    }

    public function mainCategories(): HasMany
    {
        return $this->hasMany(MainCategory::class);
    }

    public function DeleteWithCategories()
    {
        $this->delete();
        $this->update(['deleted_at' => now()->addDays(30)]);
    }

    public function restoreDeleted()
    {
        $this->restore();
    }
}