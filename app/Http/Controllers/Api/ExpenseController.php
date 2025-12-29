<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ExpenseService;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;

class ExpenseController extends Controller
{
    public function __construct(protected ExpenseService $expenseService)
    {
        //
    }

    public function index()
    {
        $expenses = $this->expenseService->getFiltered();
        return response()->json([
            'expenses' => $expenses,
        ]);
    }

    public function store(StoreExpenseRequest $request)
    {
        $expense = $this->expenseService->create($request->validated());
        return response()->json([
            'expense' => $expense,
        ]);
    }

    public function show(Expense $expense)
    {
        $expense = $this->expenseService->findExpense($expense);
        return response()->json([
            'expense' => $expense,
        ]);
    }

    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        $this->expenseService->update($expense, $request->validated());

        return response()->json([
            'expense' => $expense->refresh(),
        ]);
    }

    public function destroy(Expense $expense)
    {
        $expense = $this->expenseService->delete($expense);
        return response()->json([
            'message' => 'Expense deleted successfully',
        ]);
    }

    public function trashed(Expense $expense)
    {
        $expense = $this->expenseService->getAllTrashed();
        return response()->json([
            'expense' => $expense,
        ]);
    }

    public function showTrashed($id)
    {
        $expense = $this->expenseService->findTrashedById($id);
        return response()->json([
            'expense' => $expense,
        ]);
    }

    public function restore($id)
    {
        $expense = $this->expenseService->restore($id);
        return response()->json([
            'message' => 'Expense restored successfully',
        ]);
    }

    public function forceDelete($id)
    {
        $expense = $this->expenseService->forceDelete($id);
        return response()->json([
            'message' => 'Expense permanently deleted successfully',
        ]);
    }
}
