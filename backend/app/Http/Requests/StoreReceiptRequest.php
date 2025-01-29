<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreReceiptRequest extends FormRequest
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
            'receipt_type_id' => 'required|integer',
            'quantity' => 'nullable|numeric', // Make `quantity` nullable initially
            'rate' => 'nullable|numeric', // Make `rate` nullable initially
            'hall' => 'nullable', // Make `rate` nullable initially
        ];
    }

    //   /**
    //  * Custom error messages for validation rules.
    //  *
    //  * @return array
    //  */
    // public function messages(): array
    // {
    //     return [
    //         'saree_draping_date_morning.exists' => 'The selected morning saree draping date is already taken.',
    //         'saree_draping_date_evening.exists' => 'The selected evening saree draping date is already taken.',
    //     ];
    // }

    protected function withValidator(Validator $validator)
    {
        $validator->sometimes('quantity', 'required|numeric|min:1', function ($input) {
            return $input->receipt_type_id == 1 || $input->receipt_type_id == 2; // Only require `quantity` if `receipt_type_id` is 1|2
        });

        $validator->sometimes('rate', 'required|numeric|min:1', function ($input) {
            return $input->receipt_type_id == 1 || $input->receipt_type_id == 2; // Only require `rate` if `receipt_type_id` is 1|2
        });

        $validator->sometimes('hall', 'required', function ($input) {
            return $input->receipt_type_id == 9; // Only require `hall` if `receipt_type_id` is 9
        });
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }    
}