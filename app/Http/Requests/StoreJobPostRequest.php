<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Set to true to allow this request
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'company' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'job_type' => 'required|string|max:50',
            'salary' => 'nullable|numeric|min:0',
            'contact_email' => 'required|email|max:255',
            'skills' => 'nullable|array',
            'skills.*' => 'string|max:100',
            'application_deadline' => 'nullable|date|after_or_equal:today',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The job title is required',
            'description.required' => 'The job description is required',
            'company.required' => 'The company name is required',
            'location.required' => 'The job location is required',
            'job_type.required' => 'The job type is required',
            'contact_email.required' => 'A contact email is required',
            'contact_email.email' => 'Please provide a valid email address',
            'application_deadline.after_or_equal' => 'The application deadline must be today or a future date',
        ];
    }
}
