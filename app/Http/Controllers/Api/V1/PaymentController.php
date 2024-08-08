<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Models\Organisation;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
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
        if (!auth()->check()) {
            return response()->json([
                'status_code' => 401,
                'message' => 'Unauthorized'
            ], 401);
        }
        // return response()->json(['h'=> 'ng']);
        $validator = Validator::make($request->all(), [
            'organisation_id' => 'required',
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
        $userIsAnAdminInOrganisation = Organisation::where('user_id', auth()->user()->id)
                                            ->where('org_id', $request->organisation_id)
                                            ->exists();
        if (!$userIsAnAdminInOrganisation) {
            return response()->json([
                'status' => 403,
                'message' => 'You do not have permission to initiate this payment'
            ], 403);
        }

        // $gateway_id = Gateway::where('code', 'paystack')->first()->id;
        $subscriptionPlan = SubscriptionPlan::find($request->plan_id);
        if(!$subscriptionPlan) {
            return response()->json([
                'status' => 404,
                'message' => 'Subscription Plan not found'
            ], 404);
        }
        $data = $validator->validated();
        $data['email'] = auth()->user()->email;
        $data['reference'] = Str::uuid();
        $data['plan_code'] = $subscriptionPlan->paystack_plan_code;
        $data['plan_id'] = $subscriptionPlan->id;
        $data['amount'] = $subscriptionPlan->price;
        $data['organisation_id'] = $request->organisation_id;

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
                'message' => 'An unexpected error occurred. Please try again later.'
                // 'message' => 'Payment Initialization Failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function handlePaystackCallback($organisation_id, $id, Request $request)
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
            $user = Organisation::find($organisation_id)->first();
            if(!$user) {
                return response()->json([
                    'status' => 404,
                    'message' => 'User Not found'
                ], 404);
            }
            $user_id = $user->id;

            $userSubscription = new UserSubscription;
            $userSubscription->user_id = $user_id;
            $userSubscription->subscription_plan_id = $id;
            $userSubscription->org_id = $organisation_id;
            $userSubscription->save();


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
        if (!auth()->check()) {
            return response()->json([
                'status_code' => 401,
                'message' => 'Unauthorized'
            ], 401);
        }
        
        $validator = Validator::make($request->all(), [
            'organisation_id' => 'required',
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

        $userIsAnAdminInOrganisation = Organisation::where('user_id', auth()->user()->id)
                                            ->where('org_id', $request->organisation_id)
                                            ->exists();
        // return response()->json(auth()->user()->id);
        if (!$userIsAnAdminInOrganisation) {
            return response()->json([
                'status' => 403,
                'message' => 'You do not have permission to initiate this payment'
            ], 403);
        }
        // $gateway_id = Gateway::where('code', 'flutterwave')->first()->id;
        $subscriptionPlan = SubscriptionPlan::find($request->plan_id);
        if(!$subscriptionPlan) {
            return response()->json([
                'status' => 404,
                'message' => 'Subscription Plan not found'
            ], 404);
        }

        $data = $validator->validated();
        $data['email'] = auth()->user()->email;
        $data['reference'] = Str::uuid();
        $data['plan_code'] = $subscriptionPlan->flutterwave_plan_code;
        $data['plan_id'] = $subscriptionPlan->id;
        $data['amount'] = $subscriptionPlan->price;
        $data['title'] = $subscriptionPlan->name;
        $data['organisation_id'] = $request->organisation_id;
        $data['title'] = $subscriptionPlan->name;

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
                'message' => 'An unexpected error occurred. Please try again later.'
                // 'message' => 'Payment Initialization Failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function handleFlutterwaveCallback($organisation_id, $id, Request $request)
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
            $user = Organisation::find($organisation_id)->first();
            if(!$user) {
                return response()->json([
                    'status' => 404,
                    'message' => 'User Not found'
                ], 404);
            }
            $user_id = $user->id;

            $userSubscription = new UserSubscription;
            $userSubscription->user_id = $user_id;
            $userSubscription->subscription_plan_id = $id;
            $userSubscription->org_id = $organisation_id;
            $userSubscription->save();

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
