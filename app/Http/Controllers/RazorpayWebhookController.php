<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RazorpayWebhookController extends Controller
{
    /**
     * Handle incoming Razorpay webhooks
     */
    public function handleWebhook(Request $request)
    {
        // Get webhook secret from .env
        $webhookSecret = env('RAZORPAY_WEBHOOK_SECRET');

        // Get signature from headers
        $signature = $request->header('X-Razorpay-Signature');
        $payload = $request->getContent();

        // Verify webhook signature
        if (!$this->verifySignature($payload, $signature, $webhookSecret)) {
            Log::error('Razorpay webhook: Invalid signature');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        try {
            $event = json_decode($payload, true);

            // Log webhook received (for debugging)
            Log::info('Razorpay webhook received', ['event' => $event['event'] ?? 'unknown']);

            // Handle different event types
            switch ($event['event']) {
                case 'payment.captured':
                    $this->handlePaymentCaptured($event['payload']['payment']['entity']);
                    break;

                case 'payment.failed':
                    $this->handlePaymentFailed($event['payload']['payment']['entity']);
                    break;

                case 'order.paid':
                    $this->handleOrderPaid($event['payload']['order']['entity']);
                    break;

                case 'refund.processed':
                    $this->handleRefundProcessed($event['payload']['refund']['entity']);
                    break;

                default:
                    Log::info('Unhandled webhook event', ['event' => $event['event']]);
            }

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('Razorpay webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Verify webhook signature
     */
    private function verifySignature($payload, $signature, $secret)
    {
        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Handle payment.captured event
     */
    private function handlePaymentCaptured($payment)
    {
        DB::beginTransaction();
        try {
            // Extract order ID from notes (you stored it during checkout)
            $orderId = $payment['notes']['order_id'] ?? null;

            if (!$orderId) {
                Log::error('Webhook: Order ID not found in payment notes');
                return;
            }

            $order = Order::find($orderId);

            if (!$order) {
                Log::error('Webhook: Order not found', ['order_id' => $orderId]);
                return;
            }

            // Update order status
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
            ]);

            // Store payment record
            Payment::create([
                'razorpay_payment_id' => $payment['id'],
                'razorpay_order_id' => $payment['order_id'],
                'order_id' => $order->id,
                'method' => $payment['method'],
                'currency' => $payment['currency'],
                'amount' => $payment['amount'] / 100, // Convert paise to rupees
                'status' => 'success',
                'json_response' => json_encode($payment),
            ]);

            Log::info('Webhook: Payment captured successfully', [
                'order_id' => $order->id,
                'payment_id' => $payment['id']
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Webhook: Error handling payment.captured', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle payment.failed event
     */
    private function handlePaymentFailed($payment)
    {
        DB::beginTransaction();
        try {
            $orderId = $payment['notes']['order_id'] ?? null;

            if (!$orderId) {
                Log::error('Webhook: Order ID not found in failed payment');
                return;
            }

            $order = Order::find($orderId);

            if ($order) {
                $order->update([
                    'payment_status' => 'failed',
                    'admin_notes' => 'Payment failed: ' . ($payment['error_description'] ?? 'Unknown error')
                ]);

                Log::info('Webhook: Payment failed recorded', [
                    'order_id' => $order->id
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Webhook: Error handling payment.failed', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle order.paid event
     */
    private function handleOrderPaid($orderData)
    {
        // Similar to payment.captured but for orders
        Log::info('Webhook: Order paid event received', ['order_data' => $orderData]);
    }

    /**
     * Handle refund.processed event
     */
    private function handleRefundProcessed($refund)
    {
        DB::beginTransaction();
        try {
            $paymentId = $refund['payment_id'];

            $payment = Payment::where('razorpay_payment_id', $paymentId)->first();

            if ($payment && $payment->order) {
                $payment->order->update([
                    'payment_status' => 'refunded',
                    'status' => 'refunded',
                    'admin_notes' => 'Refund processed: ₹' . ($refund['amount'] / 100)
                ]);

                Log::info('Webhook: Refund processed', [
                    'order_id' => $payment->order->id,
                    'refund_id' => $refund['id']
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Webhook: Error handling refund.processed', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
