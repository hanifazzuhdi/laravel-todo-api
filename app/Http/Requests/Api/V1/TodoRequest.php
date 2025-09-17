<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Todo;
use Illuminate\Foundation\Http\FormRequest;

class TodoRequest extends FormRequest
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
            'title'    => 'required|string|max:255',
            'status'   => 'in:' . implode(',', Todo::STATUSES),
            'priority' => 'required|in:' . implode(',', Todo::PRIORITIES),
            'due_date' => 'required|date|after_or_equal:today',
        ];
    }
}
