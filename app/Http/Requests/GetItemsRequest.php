<?php

namespace App\Http\Requests;

use App\Helpers\LangHelper;
use Illuminate\Foundation\Http\FormRequest;

class GetItemsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:item_categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => LangHelper::msg('category_id_required'),
            'category_id.exists' => LangHelper::msg('category_not_found'),
        ];
    }
}

