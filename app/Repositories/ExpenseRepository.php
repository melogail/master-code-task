<?php

namespace App\Repositories;

use App\Models\Expense;
use App\Http\Resources\ExpenseResource;

class ExpenseRepository
{
    public function create(array $data)
    {
        return Expense::create($data);
    }

    public function update(Expense $expense, array $data)
    {
        return $expense->update($data);
    }

    public function delete(Expense $expense)
    {
        return $expense->delete();
    }

    public function forceDelete(Expense $expense)
    {
        return $expense->forceDelete();
    }

    public function findById($id)
    {
        return ExpenseResource::make(Expense::findOrFail($id));
    }

    public function findTrashedById($id)
    {
        return Expense::onlyTrashed()->findOrFail($id);
    }

    public function getAll()
    {
        return ExpenseResource::collection(Expense::all());
    }

    public function findExpense(Expense $expense)
    {
        return ExpenseResource::make($expense);
    }

    public function getAllTrashed()
    {
        return ExpenseResource::collection(Expense::onlyTrashed()->get());
    }

    public function getFiltered()
    {
        return ExpenseResource::collection(Expense::filter()->with(['category', 'vendor'])->get());
    }

    public function restore(Expense $expense)
    {
        return $expense->restore();
    }

}
