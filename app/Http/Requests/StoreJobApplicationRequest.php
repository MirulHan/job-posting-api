<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobApplicationRequest extends FormRequest
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
            // @example 1
            'job_post_id' => 'required|exists:job_posts,id',
            // @example John Doe
            'full_name' => 'required|string|max:255',
            // @example +1234567890
            'phone_number' => 'required|string|regex:/^[\+]?[0-9\-\s\(\)]+$/|max:20',
            // @example contact@example.com
            'email' => 'nullable|email|max:255',
            // @example 5 years of experience in customer service and sales
            'work_experience' => 'required|string|max:1000',
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
            'job_post_id.required' => 'The job post ID is required',
            'job_post_id.exists' => 'The selected job post does not exist',
            'full_name.required' => 'Your full name is required',
            'phone_number.required' => 'Your phone number is required',
            'email.email' => 'Please provide a valid email address',
            'work_experience.required' => 'Please provide your work experience',
            'work_experience.max' => 'Your work experience is too long. Please limit it to 500 characters.',
        ];
    }
}
