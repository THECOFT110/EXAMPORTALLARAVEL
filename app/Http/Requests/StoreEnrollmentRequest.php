<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'STUDENT';
    }

    public function rules(): array
    {
        return [
            'program' => 'required|string|max:100',
            'session' => 'nullable|string|max:50',
            'semester' => 'nullable|string|max:20',
            'father_name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:100',
            'so_do_wo' => 'nullable|string|max:50',
            'dob' => 'nullable|date',
            'gender' => 'required|in:MALE,FEMALE,OTHER',
            'address' => 'required|string|max:500',
            'city' => 'nullable|string|max:100',
            'contact_number' => 'nullable|string|max:20',
            'postal_address' => 'nullable|string|max:500',
            'passing_year' => 'nullable|string|max:20',
            'division_obtained' => 'nullable|string|max:50',
            'name_of_board' => 'nullable|string|max:100',
            'nationality' => 'nullable|string|max:50',
            'religion' => 'nullable|string|max:50',
            'domicile_province' => 'nullable|string|max:50',
            'domicile_district' => 'nullable|string|max:50',
            'college_id' => 'nullable|uuid|exists:colleges,id',
            'photo' => 'nullable|image|max:2048',
            'doc_cnic' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'doc_matric' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'doc_inter' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }
}
