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

        if ($fee->status !== 'PAID') {
            return response()->json([
                'message' => 'Only paid fees can be verified.',
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
     * Process payment callback (placeholder)
     */
    public function processPayment(Request $request, string $feeId)
    {
        // This would handle the callback from payment gateway

        $fee = Fee::findOrFail($feeId);

        // Verify payment with gateway
        // If successful:
        $fee->markAsPaid('ONLINE', 'TXN-'.uniqid());

        return response()->json([
            'success' => true,
            'message' => 'Payment processed successfully.',
        ]);
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
}
