<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ExpenseScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->user()?->is_admin) {
            return;
        }

        $builder->whereHas('category', function (Builder $builder) {
            $builder->where('is_active', true);
        })->where('amount', '>', 0);
    }
}
