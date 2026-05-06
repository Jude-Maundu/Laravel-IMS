<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class MpesaController extends Controller
{
    /**
     * Generate Daraja Access Token
     */
    private function generateToken()
    {
        $consumerKey = config('services.mpesa.consumer_key');
        $consumerSecret = config('services.mpesa.consumer_secret');
        $env = config('services.mpesa.env');

        $url = ($env === 'production') 
            ? 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
            : 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $response = Http::withBasicAuth($consumerKey, $consumerSecret)->get($url);

        return $response->json('access_token');
    }

    /**
     * Trigger M-Pesa STK Push (Daraja API)
     */
    public function stkPush(Event $event, $phone)
    {
        // Sanitize phone number (ensure 2547XXXXXXXX)
        $phone = preg_replace('/^(\+254|0|254)/', '254', $phone);
        
        $token = $this->generateToken();
        if (!$token) {
            return back()->with('error', 'Could not connect to M-Pesa gateway. Please try again.');
        }

        $env = config('services.mpesa.env');
        $shortCode = config('services.mpesa.short_code');
        $passkey = config('services.mpesa.passkey');
        $callbackUrl = config('services.mpesa.callback_url');
        
        $timestamp = Carbon::now()->format('YmdHis');
        $password = base64_encode($shortCode . $passkey . $timestamp);
        
        $url = ($env === 'production')
            ? 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest'
            : 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

        $amount = (int) ($event->amount_due ?? $event->cost ?? 1); // KES 1 for testing if zero

        $response = Http::withToken($token)->post($url, [
            'BusinessShortCode' => $shortCode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'TransactionType'   => 'CustomerPayBillOnline',
            'Amount'            => $amount,
            'PartyA'            => $phone,
            'PartyB'            => $shortCode,
            'PhoneNumber'       => $phone,
            'CallBackURL'       => $callbackUrl,
            'AccountReference'  => $event->plan_ref,
            'TransactionDesc'   => 'Payment for Event ' . $event->plan_ref
        ]);

        $res = $response->json();

        if ($response->successful() && isset($res['ResponseCode']) && $res['ResponseCode'] === '0') {
            // Log the attempt
            Payment::create([
                'event_id' => $event->id,
                'amount'   => $amount,
                'phone'    => $phone,
                'status'   => 'pending',
                'merchant_request_id'  => $res['MerchantRequestID'],
                'checkout_request_id'  => $res['CheckoutRequestID'],
            ]);

            return redirect()->route('portal.event.show', $event)->with('info', 'Payment prompt sent to your phone. Please enter your PIN.');
        } else {
            Log::error('M-Pesa STK Push Failed', ['response' => $res, 'event' => $event->id]);
            return back()->with('error', 'M-Pesa gateway error: ' . ($res['errorMessage'] ?? 'Unknown error'));
        }
    }

    /**
     * M-Pesa Callback (Webhook from Safaricom)
     */
    public function callback(Request $request)
    {
        $payload = $request->all();
        Log::info('M-Pesa Callback received', $payload);

        $data = $payload['Body']['stkCallback'] ?? null;
        if (!$data) return response()->json(['status' => 'invalid payload']);

        $checkoutRequestId = $data['CheckoutRequestID'];
        $resultCode = $data['ResultCode'];
        $resultDesc = $data['ResultDesc'];

        $payment = Payment::where('checkout_request_id', $checkoutRequestId)->first();
        if (!$payment) {
            Log::error('Payment record not found for CheckoutRequestID: ' . $checkoutRequestId);
            return response()->json(['status' => 'not found']);
        }

        $event = $payment->event;

        if ($resultCode == 0) {
            // Success
            $meta = $data['CallbackMetadata']['Item'];
            $transactionId = null;
            
            foreach ($meta as $item) {
                if ($item['Name'] === 'MpesaReceiptNumber') {
                    $transactionId = $item['Value'];
                }
            }

            $payment->update([
                'status' => 'success',
                'transaction_id' => $transactionId,
                'response_data' => json_encode($payload)
            ]);

            $event->update([
                'payment_status' => 'paid',
                'status'         => 'Scheduled', // Move from Awaiting Payment to Scheduled
                'transaction_id' => $transactionId
            ]);

            // Log activity
            \App\Models\ActivityLog::create([
                'event_id' => $event->id,
                'action' => 'payment_received',
                'description' => "Payment of KES " . number_format($payment->amount, 2) . " received via M-Pesa. Ref: $transactionId",
                'user_id' => null,
            ]);

        } else {
            // Failed
            $payment->update([
                'status' => 'failed',
                'failure_reason' => $resultDesc,
                'response_data' => json_encode($payload)
            ]);

            $event->update(['payment_status' => 'failed']);
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }
}
