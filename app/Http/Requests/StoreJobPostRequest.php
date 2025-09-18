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
            // @example Test Job Post
            'title' => 'required|string|max:255',
            // @example Test Job Post Description
            'description' => 'required|string',
            // @example Test Job Post Company
            'company' => 'required|string|max:255',
            // @example Test Job Post Location
            'location' => 'required|string|max:255',
            // @example Full-time
            'job_type' => 'required|string|max:50',
            // @example 60000.00
            'salary' => 'nullable|numeric|min:0',
            // @example contact@example.com
            'contact_email' => 'required|email|max:255',
            // @example ["PHP", "Laravel", "JavaScript"]
            'skills' => 'nullable|array',
            'skills.*' => 'string|max:100',
            // @example 2025-12-31
            'application_deadline' => 'nullable|date|after_or_equal:today',
            // @example true
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
