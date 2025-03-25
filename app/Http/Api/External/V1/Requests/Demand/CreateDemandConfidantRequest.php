<?php

namespace App\Http\Api\External\V1\Requests\Demand;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class CreateDemandConfidantRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'phone' => ['nullable', 'string',],
            'email' => ['nullable', 'email', 'max:255'],
            'birth_date' => ['required', 'date'],
            'inn' => ['nullable', 'string',],
            'snils' => ['nullable', 'string',],
            'is_rus' => ['required', Rule::in([true, false])],
            'confidant_type' => ['required', Rule::in(['1', '2'])],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $errorResponse = [];

        foreach ($errors->toArray() as $field => $fieldErrors) {
            $errorResponse[] = [
                'field' => $field,
                'errors' => $fieldErrors,
                'message' => 'Invalid '.$field.'.',
            ];
        }
        throw new HttpResponseException(response()->json($errorResponse, Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
