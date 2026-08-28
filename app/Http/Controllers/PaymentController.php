<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Fee;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Get fee details
     */
    public function getFee(Request $request, string $feeId)
    {
        $fee = Fee::with(['enrollment.user', 'enrollment.academicYear'])
            ->findOrFail($feeId);

        // Check access
        if ($request->user()->role === 'STUDENT' &&
            $fee->enrollment->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access.');
        }

        return response()->json([
            'id' => $fee->id,
            'challan_number' => $fee->challan_number,
            'amount' => $fee->amount,
            'status' => $fee->status,
            'due_date' => $fee->due_date,
            'paid_at' => $fee->paid_at,
            'payment_method' => $fee->payment_method,
            'transaction_id' => $fee->transaction_id,
            'enrollment' => [
                'id' => $fee->enrollment->id,
                'program' => $fee->enrollment->program,
                'student_name' => $fee->enrollment->user->full_name,
            ],
            'is_expired' => $fee->isExpired(),
        ]);
    }

    /**
     * Mark fee as paid (Manual payment verification)
     */
    public function markAsPaid(Request $request, string $feeId)
    {
        $validated = $request->validate([
            'payment_method' => 'required|string|in:BANK,ONLINE,CASH',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $fee = Fee::with('enrollment')->findOrFail($feeId);

        if ($fee->isPaid()) {
            return response()->json([
                'message' => 'Fee is already paid.',
            ], 400);
        }

        $fee->markAsPaid(
            $validated['payment_method'],
            $validated['transaction_id'] ?? null
        );

        if ($request->filled('notes')) {
            $fee->notes = $validated['notes'];
            $fee->save();
        }

        AuditLog::log(
            $request->user()->id,
            'MARK_FEE_PAID',
            'Fee',
            $feeId,
            "Fee marked as paid. Method: {$validated['payment_method']}",
            $request->ip()
        );

        return response()->json([
            'message' => 'Fee marked as paid successfully.',
            'fee' => $fee->fresh(),
        ]);
    }

    /**
     * Verify payment (Admin verification)
     */
    public function verifyPayment(Request $request, string $feeId)
    {
        $fee = Fee::findOrFail($feeId);

        if (! in_array($fee->status, ['PAID', 'PENDING_VERIFICATION'])) {
            return response()->json([
                'message' => 'Only paid or pending-verification fees can be verified.',
            ], 400);
        }

        $fee->markAsVerified();

        AuditLog::log(
            $request->user()->id,
            'VERIFY_PAYMENT',
            'Fee',
            $feeId,
            'Payment verified by admin',
            $request->ip()
        );

        return response()->json([
            'message' => 'Payment verified successfully.',
        ]);
    }

    /**
     * Simulate online payment (placeholder for payment gateway integration)
     */
    public function initiatePayment(Request $request, string $feeId)
    {
        $fee = Fee::with('enrollment.user')->findOrFail($feeId);

        // Check access
        if ($request->user()->role === 'STUDENT' &&
            $fee->enrollment->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access.');
        }

        if ($fee->isPaid()) {
            return response()->json([
                'message' => 'Fee is already paid.',
            ], 400);
        }

        // This would integrate with a payment gateway like JazzCash, EasyPaisa, etc.
        // For now, return a placeholder response

        return response()->json([
            'message' => 'Payment gateway integration pending.',
            'payment_url' => url("/payment/process/{$feeId}"),
            'amount' => $fee->amount,
            'challan_number' => $fee->challan_number,
        ]);
    }

    /**
     * Process payment callback (development/testing only)
     */
    public function processPayment(Request $request, string $feeId)
    {
        // Gated against non-local environments to prevent unauthorized payment marking
        if (! app()->isLocal() && ! app()->environment('testing')) {
            abort(403, 'Mock payment processing is disabled in this environment.');
        }

        $fee = Fee::findOrFail($feeId);

        $fee->markAsPaid('ONLINE', 'TXN-'.\Illuminate\Support\Str::random(12));

        return response()->json([
            'success' => true,
            'message' => 'Payment processed successfully.',
        ]);
    }

    /**
     * Handle payment gateway webhook callback.
     *
     * Only providers with implemented signature verification are accepted;
     * every other provider is rejected to prevent forged payment webhooks.
     */
    public function handleWebhook(Request $request, string $provider, \App\Services\PaymentGatewayService $gatewayService)
    {
        $payload = $request->all();

        $supportedProviders = ['jazzcash'];

        if (! in_array($provider, $supportedProviders, true)) {
            \Illuminate\Support\Facades\Log::warning("Rejected payment webhook from unsupported provider: {$provider}");

            return response()->json(['error' => 'Unsupported payment provider'], 400);
        }

        if (! $gatewayService->isJazzCashConfigured()) {
            return response()->json(['error' => 'Payment gateway is not configured'], 503);
        }

        if (! $gatewayService->verifyJazzCashSignature($payload)) {
            return response()->json(['error' => 'Invalid HMAC signature'], 400);
        }

        $challanNumber = $payload['pp_BillReference'] ?? null;
        $txnId = $payload['pp_TxnRefNo'] ?? null;
        $amount = ((float) ($payload['pp_Amount'] ?? 0)) / 100;

        if (! $challanNumber || ! $txnId) {
            return response()->json(['error' => 'Missing challan or transaction reference'], 422);
        }

        $result = $gatewayService->processWebhookPayment($provider, $challanNumber, $txnId, $amount);

        return match ($result) {
            'ok', 'already_paid' => response()->json(['success' => true, 'message' => 'Webhook processed successfully']),
            'not_found' => response()->json(['error' => 'Fee record not found'], 404),
            'amount_mismatch' => response()->json(['error' => 'Payment amount does not match the challan amount'], 422),
            default => response()->json(['error' => 'Payment could not be processed'], 422),
        };
    }

    /**
     * Get payment history for a user
     */
    public function paymentHistory(Request $request)
    {
        $user = $request->user();

        $fees = Fee::whereHas('enrollment', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with('enrollment')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($f) => [
            'id' => $f->id,
            'challan_number' => $f->challan_number,
            'amount' => $f->amount,
            'status' => $f->status,
            'due_date' => $f->due_date,
            'paid_at' => $f->paid_at,
            'payment_method' => $f->payment_method,
            'enrollment_program' => $f->enrollment->program,
            'is_expired' => $f->isExpired(),
        ]);

        return response()->json($fees);
    }

    /**
     * Web Checkout page
     */
    public function webCheckout(Request $request, string $feeId)
    {
        $fee = Fee::with('enrollment.user')->findOrFail($feeId);

        if ($fee->enrollment->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        return view('student.fees', compact('fee'));
    }

    /**
     * Web Payment submission handler
     */
    public function webSubmitPayment(\App\Http\Requests\SubmitPaymentRequest $request, string $feeId)
    {
        $fee = Fee::with('enrollment')->findOrFail($feeId);

        if ($fee->enrollment->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validated();

        $fee->markAsPendingVerification($validated['payment_method'], $validated['transaction_id']);

        AuditLog::log(
            auth()->id(),
            'SUBMIT_FEE_PAYMENT',
            'Fee',
            $feeId,
            "Payment proof submitted: {$validated['transaction_id']}",
            $request->ip()
        );

        return redirect()->route('student.dashboard')->with(
            'success',
            'Payment proof submitted successfully! Your payment is now pending verification by administration.'
        );
    }
}
