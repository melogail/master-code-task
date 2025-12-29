<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "category_id" => ["required", "integer", "exists:categories,id"],
            "vendor_id" => ["nullable", "integer", "exists:vendors,id"],
            "amount" => ["required", "numeric", "min:0"],
            "date" => ["sometimes", "date"],
            "description" => ["nullable", "string", "max:255"],
        ];
    }
}
