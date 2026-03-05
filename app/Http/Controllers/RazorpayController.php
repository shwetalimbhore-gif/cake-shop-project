<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RazorpayController extends Controller
{
    protected $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(
            env('RAZORPAY_KEY_ID'),
            env('RAZORPAY_KEY_SECRET')
        );
    }

    /**
     * Create a Razorpay order and show payment page
     */
    public function checkout(Request $request, Order $order)
    {
        // Verify order belongs to authenticated user
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Calculate amount in paise (Razorpay accepts smallest currency unit)
        $amountInPaise = (int) ($order->total * 100);

        try {
            // Create Razorpay order
            $razorpayOrder = $this->razorpay->order->create([
                'receipt' => $order->order_number,
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'notes' => [
                    'order_id' => $order->id,
                    'customer_name' => $order->shipping_name,
                    'customer_email' => $order->shipping_email,
                ]
            ]);

            // Store Razorpay order ID in session
            session(['razorpay_order_id' => $razorpayOrder->id]);

            return view('front.razorpay-checkout', [
                'order' => $order,
                'razorpayOrder' => $razorpayOrder,
                'keyId' => env('RAZORPAY_KEY_ID'),
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'name' => setting('site_name', 'Cozy Cravings'),
                'description' => 'Payment for Order #' . $order->order_number,
                'prefill' => [
                    'name' => $order->shipping_name,
                    'email' => $order->shipping_email,
                    'contact' => $order->shipping_phone,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Razorpay Order Creation Failed: ' . $e->getMessage());
            return redirect()->route('checkout.index')
                ->with('error', 'Payment gateway error. Please try again.');
        }
    }

    /**
     * Handle payment success callback
     */
    public function success(Request $request)
    {
        DB::beginTransaction();

        try {
            $paymentData = $request->all();

            // Verify payment signature
            $this->verifySignature($paymentData);

            // Fetch payment details from Razorpay
            $payment = $this->razorpay->payment->fetch($paymentData['razorpay_payment_id']);

            // Find the order by Razorpay order ID
            $razorpayOrderId = $paymentData['razorpay_order_id'];
            $razorpayOrder = $this->razorpay->order->fetch($razorpayOrderId);

            // Get our order ID from notes
            $orderId = $razorpayOrder->notes->order_id ?? null;

            if (!$orderId) {
                throw new \Exception('Order ID not found in notes');
            }

            $order = Order::findOrFail($orderId);

            // Update order status
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
                'payment_method' => 'razorpay',
            ]);

            // Store payment record
            Payment::create([
                'razorpay_payment_id' => $payment->id,
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_signature' => $paymentData['razorpay_signature'] ?? null,
                'order_id' => $order->id,
                'method' => $payment->method,
                'currency' => $payment->currency,
                'amount' => $payment->amount / 100, // Convert paise to rupees
                'status' => 'success',
                'json_response' => json_encode($payment->toArray()),
            ]);

            // Clear cart
            $cart = Cart::getCart();
            CartItem::where('cart_id', $cart->id)->delete();
            $cart->update(['total_amount' => 0]);

            DB::commit();

            return redirect()->route('checkout.success', $order)
                ->with('success', 'Payment successful!');

        } catch (SignatureVerificationError $e) {
            DB::rollBack();
            Log::error('Razorpay Signature Verification Failed: ' . $e->getMessage());
            return redirect()->route('checkout.index')
                ->with('error', 'Payment verification failed. Please contact support.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Razorpay Payment Success Error: ' . $e->getMessage());
            return redirect()->route('checkout.index')
                ->with('error', 'Something went wrong. Please contact support.');
        }
    }

    /**
     * Handle payment failure
     */
    public function failure(Request $request)
    {
        $error = $request->input('error');

        Log::error('Razorpay Payment Failed: ' . json_encode($error));

        return redirect()->route('checkout.index')
            ->with('error', 'Payment failed. Please try again.');
    }

    /**
     * Verify Razorpay payment signature
     */
    private function verifySignature($data)
    {
        $generatedSignature = hash_hmac(
            'sha256',
            $data['razorpay_order_id'] . '|' . $data['razorpay_payment_id'],
            env('RAZORPAY_KEY_SECRET')
        );

        if (!isset($data['razorpay_signature']) || $generatedSignature !== $data['razorpay_signature']) {
            throw new SignatureVerificationError('Invalid signature');
        }
    }
}
