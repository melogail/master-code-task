<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Scopes\ExpenseScope;

#[ScopedBy(ExpenseScope::class)]
class Expense extends Model
{
    /** @use HasFactory<\Database\Factories\ExpenseFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'vendor_id',
        'amount',
        'date',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * ===================
     * == RELATIONSHIPS ==
     * ===================
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * ===================
     * == SCOPES ========
     * ===================
     */

    public function scopeFilter($query)
    {
        // if category is set 
        if (request()->has('category')) {
            $query->whereHas('category', function ($query) {
                $query->where('name', 'like', '%' . request('category') . '%');
            });
        }

        // if vendor is set 
        if (request()->has('vendor')) {
            $query->whereHas('vendor', function ($query) {
                $query->where('name', 'like', '%' . request('vendor') . '%');
            });
        }

        // if from is set 
        if (request()->has('from')) {
            $query->where('date', '>=', request('from'));
        }

        // if to is set 
        if (request()->has('to')) {
            $query->where('date', '<=', request('to'));
        }

        return $query;
    }
}
