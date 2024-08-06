<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    public function initiatePaystackPayment($data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
                'Cache-Control' => 'no-cache',
            ])->post('https://api.paystack.co/transaction/initialize', [
                'email' => $data['email'],
                'plan' => $data['plan_code'],
                'reference' => $data['reference'],
                'callback_url' => url('/api/v1/payments/paystack/verify/'.$data['plan_id']),
                'metadata' => [
                    'cancel_action' => route('payment.cancel')
                ]
            ]);

            if (!$response->successful()) {
                throw new Exception('Paystack Payment Initialization Failed: ' . $response->body());
            }

            return $response->json()['data']['authorization_url'];
        } catch (Exception $e) {
            throw new Exception('Paystack Payment Initialization Failed: ' . $e->getMessage());
        }
    }

    public function verifyPaystackTransaction($reference)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
                'Cache-Control' => 'no-cache',
            ])->get("https://api.paystack.co/transaction/verify/{$reference}");

            if (!$response->successful()) {
                throw new Exception('Paystack Transaction Verification Failed: ' . $response->body());
            }

            return $response->json()['data'];
        } catch (Exception $e) {
            throw new Exception('Paystack Transaction Verification Failed: ' . $e->getMessage());
        }
    }

    public function initiateFlutterwavePayment($data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('FLUTTERWAVE_SECRET_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.flutterwave.com/v3/payments', [
                'tx_ref' => $data['reference'],
                'amount' => $data['amount'], // Flutterwave still needs the amount
                'currency' => 'USD',
                'redirect_url' => url('/api/v1/payments/flutterwave/verify/'.$data['plan_id']),
                'customer' => [
                    'email' => $data['email'],
                    'name' => $data['full_name']
                ],
                'customizations' => [
                    'title' => 'Your Payment Title',
                    'billing_option' => $data['billing_option'] // Include billing_option in customizations
                ],
                'meta' => [
                    'source' => 'laravel-flutterwave',
                    'plan_code' => $data['plan_code'] // Include plan_code in meta
                ]
            ]);

            if (!$response->successful()) {
                throw new Exception('Flutterwave Payment Initialization Failed: ' . $response->body());
            }

            return $response->json()['data']['link'];
        } catch (Exception $e) {
            throw new Exception('Flutterwave Payment Initialization Failed: ' . $e->getMessage());
        }
    }

    public function verifyFlutterwaveTransaction($transaction_id)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('FLUTTERWAVE_SECRET_KEY'),
                'Cache-Control' => 'no-cache',
            ])->get("https://api.flutterwave.com/v3/transactions/{$transaction_id}/verify");

            if (!$response->successful()) {
                throw new Exception('Flutterwave Transaction Verification Failed: ' . $response->body());
            }

            return $response->json()['data'];
        } catch (Exception $e) {
            throw new Exception('Flutterwave Transaction Verification Failed: ' . $e->getMessage());
        }
    }
}
