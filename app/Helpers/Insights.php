<?php

namespace App\Helpers;

use App\Models\Expense;
use App\Models\Category;
use App\Models\Vendor;
use Carbon\Carbon;

class Insights
{
    public static function sum($model, string $column): float
    {
        return round($model->sum($column), 2);
    }

    public static function count($model): int
    {
        return $model->count();
    }

    public static function average($model, string $column): float
    {
        return round($model->average($column), 2);
    }

    public static function max($model, string $column): float
    {
        return round($model->max($column), 2);
    }

    public static function min($model, string $column): float
    {
        return round($model->min($column), 2);
    }

    /**
     * Get overview insights
     * 
     * @return array
     */
    public static function overview()
    {
        return [
            'total_expenses' => self::sum(Expense::all(), 'amount'),
            'count_of_categories' => self::count(Category::all()),
            'count_of_vendors' => self::count(Vendor::all()),
            'average_of_expenses' => self::average(Expense::all(), 'amount'),
            'max_of_expenses' => self::max(Expense::all(), 'amount'),
            'min_of_expenses' => self::min(Expense::all(), 'amount'),
        ];
    }

    /**
     * Get monthly overview insights
     * If no date is provided, it will return insights for the current month.
     * 
     * @param Carbon $from
     * @param Carbon $to
     * @param int $year
     * @return array
     */
    public static function monthlyOverview($from = null, $to = null, $year = null)
    {

        $from = $from ?? Carbon::now()->startOfMonth();
        $to = $to ?? Carbon::now()->endOfMonth();
        $year = $year ?? Carbon::now()->year;

        $expenses = Expense::whereYear('date', $year)->whereBetween('date', [$from, $to]);
        $vendors = Vendor::whereYear('created_at', $year)->whereBetween('created_at', [$from, $to]);

        return [
            'total_expenses' => self::sum($expenses, 'amount'),
            'count_of_vendors' => self::count($vendors),
            'average_of_expenses' => self::average($expenses, 'amount'),
            'max_of_expenses' => self::max($expenses, 'amount'),
            'min_of_expenses' => self::min($expenses, 'amount'),
        ];
    }

    /**
     * Get quarterly overview insights
     * If no quarter is provided, it will return insights for the current quarter.
     * 
     * @param int $quarter
     * @param int $year
     * @return array
     */
    public static function quarterlyOverview($quarter = null, $year = null)
    {
        $quarter = $quarter ?? 4;
        $year = $year ?? Carbon::now()->year;

        $expenses = Expense::whereRaw('QUARTER(date) = ?', [$quarter])->whereYear('date', $year)->get();
        $vendors = Vendor::whereRaw('QUARTER(created_at) = ?', [$quarter])->whereYear('created_at', $year)->get();

        return [
            'total_expenses' => self::sum($expenses, 'amount'),
            'count_of_vendors' => self::count($vendors),
            'average_of_expenses' => self::average($expenses, 'amount'),
            'max_of_expenses' => self::max($expenses, 'amount'),
            'min_of_expenses' => self::min($expenses, 'amount'),
        ];
    }

    /**
     * Get yearly overview insights
     * If no year is provided, it will return insights for the current year.
     * 
     * @param int $year
     * @return array
     */
    public static function yearlyOverview($year = null)
    {
        $year = $year ?? Carbon::now()->year;

        $expenses = Expense::whereYear('date', $year)->get();
        $vendors = Vendor::whereYear('created_at', $year)->get();

        return [
            'total_expenses' => self::sum($expenses, 'amount'),
            'count_of_vendors' => self::count($vendors),
            'average_of_expenses' => self::average($expenses, 'amount'),
            'max_of_expenses' => self::max($expenses, 'amount'),
            'min_of_expenses' => self::min($expenses, 'amount'),
        ];
    }

    /**
     * Get insights for expenses by category
     * If no category is provided, it will return insights for all categories.
     * 
     * @param string $category
     * @param Carbon $from
     * @param Carbon $to
     * @return array
     */
    public static function insightsExpensesByCategory($category, $from = null, $to = null)
    {
        $from = $from ?? Carbon::now()->startOfMonth();
        $to = $to ?? Carbon::now()->endOfMonth();

        $expenses = Expense::whereHas('category', function ($query) use ($category) {
            $query->where('name', $category);
        })->whereBetween('date', [$from, $to])->get();

        return [
            'total_expenses' => self::sum($expenses, 'amount'),
            'average_of_expenses' => self::average($expenses, 'amount'),
            'max_of_expenses' => self::max($expenses, 'amount'),
            'min_of_expenses' => self::min($expenses, 'amount'),
        ];
    }

}