<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Scopes\CategoryScope;

#[ScopedBy(CategoryScope::class)]
class Category extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = ['name', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * ====================
     * == RELATIONSHIPS ==
     * ====================
     */

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

}
