<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|unique:clients',
            'password' => 'required|string|min:6',
            'c_password' => 'required|same:password',
            'country' => 'required|string',
            'city' => 'required|string',
            'gender' => 'required|string',
            'birth_date' => 'required|date',
        ];
    }
}
