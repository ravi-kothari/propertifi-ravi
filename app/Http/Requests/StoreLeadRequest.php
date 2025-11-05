<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreLeadRequest extends FormRequest
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
            // Property type
            'property_type' => 'required|string|in:single-family,multi-family,hoa-coa,commercial',

            // Address fields
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|size:2', // State code (e.g., "CA")
            'zip_code' => 'required|string|regex:/^\d{5}$/', // 5-digit zipcode

            // Property details (optional)
            'number_of_units' => 'nullable|integer|min:1|max:10000',
            'square_footage' => 'nullable|integer|min:1|max:1000000',
            'additional_services' => 'nullable|array',
            'additional_services.*' => 'string',

            // Contact information
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|regex:/^[0-9]{10}$/', // 10-digit phone
            'preferred_contact' => 'required|string|in:email,phone',

            // Optional source tracking
            'source' => 'nullable|string|max:50'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'property_type.required' => 'Please select a property type.',
            'property_type.in' => 'Invalid property type selected.',

            'street_address.required' => 'Street address is required.',
            'city.required' => 'City is required.',
            'state.required' => 'State is required.',
            'state.size' => 'State must be a 2-letter code.',
            'zip_code.required' => 'ZIP code is required.',
            'zip_code.regex' => 'ZIP code must be 5 digits.',

            'number_of_units.integer' => 'Number of units must be a number.',
            'number_of_units.min' => 'Number of units must be at least 1.',
            'square_footage.integer' => 'Square footage must be a number.',

            'full_name.required' => 'Full name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'phone.required' => 'Phone number is required.',
            'phone.regex' => 'Phone number must be 10 digits.',
            'preferred_contact.required' => 'Please select a preferred contact method.',
            'preferred_contact.in' => 'Invalid contact method selected.'
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
