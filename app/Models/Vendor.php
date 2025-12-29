<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Scopes\VendorScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ScopedBy(VendorScope::class)]
class Vendor extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = ['name', 'contact_info', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * ===================
     * == RELATIONSHIPS ==
     * ===================
     */

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

}
