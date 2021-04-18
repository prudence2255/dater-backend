<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MetaRequest extends FormRequest
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
            'want' => 'sometimes|string|nullable',
            'interest' => 'sometimes|string|nullable',
            'status' => 'sometimes|string|nullable',
            'education' => 'sometimes|string|nullable',
            'profession' => 'sometimes|string|nullable',
            'height' => 'sometimes|string|nullable',
            'eye_color' => 'sometimes|string|nullable',
            'hair_color' => 'sometimes|string|nullable',
            'self_summary' => 'sometimes|string|nullable',
            'f_music' => 'sometimes|string|nullable',
            'f_shows' => 'sometimes|string|nullable',
            'f_movies' => 'sometimes|string|nullable',
            'f_books' => 'sometimes|string|nullable',
            'religion' => 'sometimes|string|nullable',
        ];
    }
}
