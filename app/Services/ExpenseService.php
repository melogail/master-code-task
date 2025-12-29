<?php

namespace App\Services;

use App\Repositories\ExpenseRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Expense;
use App\Http\Resources\ExpenseResource;

class ExpenseService
{
    use AuthorizesRequests;
    /**
     * Create a new class instance.
     */
    public function __construct(protected ExpenseRepository $expenseRepository)
    {
        //
    }

    public function create(array $data)
    {
        $this->authorize('create', Expense::class);
        return $this->expenseRepository->create($data);
    }

    public function update(Expense $expense, array $data)
    {
        $this->authorize('update', $expense);
        return $this->expenseRepository->update($expense, $data);
    }

    public function delete(Expense $expense)
    {
        $this->authorize('delete', $expense);
        return $this->expenseRepository->delete($expense);
    }

    public function forceDelete($id)
    {
        $expense = $this->expenseRepository->findTrashedById($id);

        $this->authorize('forceDelete', $expense);
        return $this->expenseRepository->forceDelete($expense);
    }

    public function findById($id)
    {
        return $this->expenseRepository->findById($id);
    }

    public function findTrashedById($id)
    {
        $expense = $this->expenseRepository->findTrashedById($id);
        $this->authorize('viewTrashed', $expense);
        return ExpenseResource::make($expense);
    }

    public function getAll()
    {
        return $this->expenseRepository->getAll();
    }

    public function findExpense(Expense $expense)
    {
        return $this->expenseRepository->findExpense($expense);
    }

    public function getAllTrashed()
    {
        $this->authorize('viewAllTrashed', Expense::class);
        return $this->expenseRepository->getAllTrashed();
    }

    public function getFiltered()
    {
        return $this->expenseRepository->getFiltered();
    }

    public function restore($id)
    {
        $expense = $this->expenseRepository->findTrashedById($id);

        $this->authorize('restore', $expense);
        return $this->expenseRepository->restore($expense);
    }
}
