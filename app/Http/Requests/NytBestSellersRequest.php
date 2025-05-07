<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Contracts\Validation\Validator;

class NytBestSellersRequest extends FormRequest
{
    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            'author'   => ['nullable','string'],
            'title'    => ['nullable','string'],
            'isbn'     => ['nullable','array'],
            'isbn.*'   => ['regex:/^(\d{10}|\d{13})$/'],
            'offset'   => ['nullable','integer'],
        ];
    }

    /**
     * @param Validator $validator
     * @return mixed
     */
    protected function failedValidation(Validator $validator):mixed
    {
        $errors = $validator->errors()->toArray();
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message'  => $errors,
        ], 422));
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'isbn.*.regex' => 'A best seller may have both 10-digit and 13-digit ISBNs',
        ];
    }
}
