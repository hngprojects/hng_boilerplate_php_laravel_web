<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    //
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function initiatePaymentForPayStack(Request $request)
    {
        // return response()->json(['h'=> 'ng']);
        $validator = Validator::make($request->all(), [
            'plan_id' =>'required',
            'billing_option' => 'required|in:monthly,yearly',
            'full_name' => 'required',
            'redirect_url' => 'required|url'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation error: ' . $validator->errors()->first()
            ], 400);
        }

        // $gateway_id = Gateway::where('code', 'paystack')->first()->id;
        $subscriptionPlan = SubscriptionPlan::find($request->plan_id);
        $data = $validator->validated();
        $data['email'] = auth()->user()->email;
        $data['reference'] = Str::uuid();
        $data['plan_code'] = $subscriptionPlan->paystack_plan_code;

        try {

            $paymentUrl = $this->paymentService->initiatePaystackPayment($data);


            // Save payment details in the database
            $payment = new Payment();
            $payment->user_id = auth()->id();
            // $payment->gateway_id = $gateway_id;
            $payment->amount = $data['amount'];
            $payment->status = 'pending';
            $payment->transaction_id = $data['reference'];
            $payment->save();

            return response()->json([
                'status' => 200,
                'message' => 'Payment initiated successfully',
                'data' => [
                    'payment_url' => $paymentUrl
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Payment Initialization Failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function handlePaystackCallback(Request $request)
    {
        $reference = $request->query('reference');

        try {
            $transaction = $this->paymentService->verifyPaystackTransaction($reference);

            // Find the payment in the database
            $payment = Payment::where('transaction_id', $reference)->firstOrFail();

            // Update payment status based on transaction status
            if ($transaction['status'] === 'success') {
                $payment->status = 'success';
            } else {
                $payment->status = 'failed';
            }

            $payment->save();

            // Redirect to the specified URL with status
            return redirect()->to($payment->redirect_url . '?status=' . $payment->status);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Transaction Verification Failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function initiatePaymentForFlutterWave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' =>'required',
            'billing_option' => 'required|in:monthly,yearly',
            'full_name' => 'required',
            'redirect_url' => 'required|url'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation error: ' . $validator->errors()->first()
            ], 400);
        }
        // $gateway_id = Gateway::where('code', 'flutterwave')->first()->id;
        $subscriptionPlan = SubscriptionPlan::find($request->plan_id);

        $data = $validator->validated();
        $data['email'] = auth()->user()->email;
        $data['reference'] = Str::uuid();
        $data['plan_code'] = $subscriptionPlan->flutterwave_plan_code;
        $data['amount'] = $subscriptionPlan->amount;

        try {
            // Retrieve the gateway name



            $paymentUrl = $this->paymentService->initiateFlutterwavePayment($data);


            // Save payment details in the database
            $payment = new Payment();
            $payment->user_id = auth()->id();
            // $payment->gateway_id = $gateway_id;
            $payment->amount = $data['amount'];
            $payment->status = 'pending';
            $payment->transaction_id = $data['reference'];
            $payment->save();

            return response()->json([
                'status' => 200,
                'message' => 'Payment initiated successfully',
                'data' => [
                    'payment_url' => $paymentUrl
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Payment Initialization Failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function handleFlutterwaveCallback(Request $request)
    {
        $transaction_id = $request->query('transaction_id');

        try {
            $transaction = $this->paymentService->verifyFlutterwaveTransaction($transaction_id);

            // Find the payment in the database
            $payment = Payment::where('transaction_id', $transaction['tx_ref'])->firstOrFail();

            // Update payment status based on transaction status
            if ($transaction['status'] === 'successful') {
                $payment->status = 'success';
            } else {
                $payment->status = 'failed';
            }

            $payment->save();

            // Redirect to the specified URL with status
            return redirect()->to($payment->redirect_url . '?status=' . $payment->status);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Transaction Verification Failed: ' . $e->getMessage()
            ], 500);
        }
    }


    public function initiatePaymentGeneral(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gateway_id' => 'required|uuid|exists:gateways,id',
            'amount' => 'required|numeric|min:1',
            'redirect_url' => 'required|url'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation error: ' . $validator->errors()->first()
            ], 400);
        }

        $data = $validator->validated();
        $data['email'] = auth()->user()->email;
        $data['reference'] = Str::uuid();

        try {
            // Retrieve the gateway name
            // $gateway = Gateway::find($data['gateway_id']);

            if ($gateway->name === 'paystack') {
                $paymentUrl = $this->paymentService->initiatePaystackPayment($data);
            } elseif ($gateway->name === 'flutterwave') {
                $paymentUrl = $this->paymentService->initiateFlutterwavePayment($data);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Unsupported payment gateway'
                ], 400);
            }

            // Save payment details in the database
            $payment = new Payment();
            $payment->user_id = auth()->id();
            $payment->gateway_id = $data['gateway_id'];
            $payment->amount = $data['amount'];
            $payment->status = 'pending';
            $payment->transaction_id = $data['reference'];
            $payment->save();

            return response()->json([
                'status' => 200,
                'message' => 'Payment initiated successfully',
                'data' => [
                    'payment_url' => $paymentUrl
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Payment Initialization Failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancel(Request $request)
    {

        return response()->json([
            'status' => 200,
            'message' => 'Payment was cancelled.',
        ], 200);
    }


}
