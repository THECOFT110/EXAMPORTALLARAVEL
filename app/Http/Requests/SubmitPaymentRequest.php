<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'STUDENT';
    }

    public function rules(): array
    {
        return [
            'transaction_id' => 'required|string|max:100',
            'payment_method' => 'required|string|in:JazzCash,EasyPaisa,BankTransfer,ONLINE,BANK',
        ];
    }
}
