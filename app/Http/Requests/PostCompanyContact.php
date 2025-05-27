<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostCompanyContact extends FormRequest
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
            'contact_id' => 'sometimes|string|exists:contacts,id',
            'first_name' => 'required_without:contact_id|string|max:255',
            'last_name' => 'required_without:contact_id|string|max:255',
            'email' => 'required_without:contact_id|email|unique:contacts,email',
            'phone_number' => 'required_without:contact_id|string|max:15',
        ];
    }
}
