<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Fee;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentGatewayService
{
    /**
     * Generate payment request parameters for JazzCash API
     */
    public function initiateJazzCashPayment(Fee $fee, string $returnUrl): array
    {
        $merchantId = config('services.jazzcash.merchant_id', env('JAZZCASH_MERCHANT_ID', 'MC_DEMO'));
        $password = config('services.jazzcash.password', env('JAZZCASH_PASSWORD', 'pass_demo'));
        $integritySalt = config('services.jazzcash.salt', env('JAZZCASH_SALT', 'salt_demo'));

        $txnRefNo = 'T' . date('YmdHis') . Str::random(4);
        $amount = (int) ($fee->amount * 100); // In paisas

        $payload = [
            'pp_Version' => '1.1',
            'pp_TxnType' => 'MWALLET',
            'pp_MerchantID' => $merchantId,
            'pp_Password' => $password,
            'pp_TxnRefNo' => $txnRefNo,
            'pp_Amount' => (string) $amount,
            'pp_TxnCurrency' => 'PKR',
            'pp_TxnDateTime' => date('YmdHis'),
            'pp_BillReference' => $fee->challan_number,
            'pp_Description' => 'SALU Examination Fee: ' . $fee->challan_number,
            'pp_ReturnURL' => $returnUrl,
        ];

        ksort($payload);
        $hashString = $integritySalt . '&' . implode('&', $payload);
        $payload['pp_SecureHash'] = strtoupper(hash_hmac('sha256', $hashString, $integritySalt));

        return $payload;
    }

    /**
     * Whether JazzCash credentials are configured for real use.
     *
     * The demo salt would let anyone forge signatures, so webhooks are
     * refused in production until a real salt is configured.
     */
    public function isJazzCashConfigured(): bool
    {
        $salt = config('services.jazzcash.salt', env('JAZZCASH_SALT', 'salt_demo'));

        return $salt !== 'salt_demo' || app()->isLocal() || app()->runningUnitTests();
    }

    /**
     * Verify JazzCash webhook callback signature
     */
    public function verifyJazzCashSignature(array $data): bool
    {
        $receivedHash = $data['pp_SecureHash'] ?? null;
        if (! $receivedHash) {
            return false;
        }

        $integritySalt = config('services.jazzcash.salt', env('JAZZCASH_SALT', 'salt_demo'));
        unset($data['pp_SecureHash']);

        ksort($data);
        $hashString = $integritySalt . '&' . implode('&', $data);
        $calculatedHash = strtoupper(hash_hmac('sha256', $hashString, $integritySalt));

        return hash_equals($calculatedHash, $receivedHash);
    }

    /**
     * Process a signature-verified payment webhook.
     *
     * Returns a result code: ok, already_paid, not_found, amount_mismatch.
     */
    public function processWebhookPayment(string $provider, string $challanNumber, string $transactionId, float $amount): string
    {
        $fee = Fee::where('challan_number', $challanNumber)->first();
        if (! $fee) {
            Log::warning("Payment webhook received for non-existent challan: {$challanNumber}");
            return 'not_found';
        }

        // Idempotency check: if already paid/verified, skip
        if ($fee->isPaid()) {
            Log::info("Payment webhook ignored: Fee {$challanNumber} is already paid.");
            return 'already_paid';
        }

        // A webhook paying less than the challan amount must not clear the fee
        if (abs($amount - (float) $fee->amount) > 0.01) {
            Log::warning("Payment webhook amount mismatch for {$challanNumber}: received {$amount}, expected {$fee->amount}");
            return 'amount_mismatch';
        }

        $fee->markAsPaid($provider, $transactionId);
        $fee->markAsVerified();

        AuditLog::log(
            $fee->enrollment?->user_id ?? 'SYSTEM',
            'GATEWAY_PAYMENT_VERIFIED',
            'Fee',
            $fee->id,
            "Automated payment verified via {$provider}. Txn: {$transactionId}, Amount: {$amount}",
            request()->ip() ?? '127.0.0.1'
        );

        return 'ok';
    }
}
