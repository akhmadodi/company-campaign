<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Customer;
use App\Models\CustomerImage;
use App\Models\CustomerVoucher;
use App\Models\Voucher;
use App\Jobs\LockdownVoucher;
use Validator;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function customer_eligible_check(Request $request) {
        $input = $request->only(['campaign_id', 'email']);

        // vbasic validate
        $validator = Validator::make($input, [
            'campaign_id' => 'required',
            'email' => 'required|email'
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => $validator->errors()
            ], 422);
        }

        $campaign = Campaign::where([
            ['id', '=', $input['campaign_id']],
            ['ended_at', '>=', now()]
        ])->first();

        // validate if campaign is exist
        if (!$campaign) {
            return response()->json([
                'message' => 'Campaign not found.'
            ], 422);
        }

        $customer = Customer::where('email', $input['email'])->first();

        // validate if customer is exist
        if (!$customer) {
            return response()->json([
                'message' => 'Email not found.'
            ], 422);
        }

        $transactions = $customer ? $customer->purchaseTransactions
            ->whereBetween('transaction_at', [
                now()->subdays($campaign->in_last_days)->startOfDay(),
                now()->subday()->endOfDay()
                ]) : [];

        $count = count($transactions);
        $total_spent = $transactions->sum('total_spent');

        $min_purchase_transactions = $campaign->min_purchase_transactions;
        $total_transactions = $campaign->total_transactions;

        $campaign_vouchers = $campaign->vouchers->where('locked_by', null);
        $voucher = $campaign_vouchers ? $campaign_vouchers->first() : null;

        // validate if voucher has been locked for self
        $has_locked = $campaign->vouchers->where('locked_by', $customer->id)->first();
        if ($has_locked) {

            $voucher_is_exist = CustomerVoucher::where([
                ['customer_id', '=', $customer->id],
                ['campaign_id', '=', $campaign->id]
            ])->first();

            if ($voucher_is_exist) {
                return response()->json([
                    'message' => 'You already have a voucher.'
                ], 422);
            } else {
                return response()->json([
                    'message' => 'Please upload your photo.'
                ], 422);
            }

        } else {

            // validate if voucher is fully filled
            if (!$voucher) {
                return response()->json([
                    'message' => 'The campaign has closed.'
                ], 422);
            }

        }

        if ($count >= $min_purchase_transactions && $total_spent >= $total_transactions) {

            $has_redeem = CustomerVoucher::where([
                'campaign_id' => $campaign->id,
                'customer_id' => $customer->id
            ])->first();

            // check if customer has been redeem
            if ($has_redeem) {
                return response()->json([
                    'message' => 'You already have a voucher.'
                ], 422);
            }

            // locked voucher
            $voucher->update([
                'locked_by' => $customer->id
            ]);

            // If the photo upload exceeds 10 minutes, remove the lockdown
            // and this voucher will become available to the next customer to grab.
            LockdownVoucher::dispatch($voucher)
                ->delay(now()->addMinutes(10));

            return response()->json([
                'message' => 'Congrats! You qualified. Please upload your photo.'
            ]);

        } else {
            return response()->json([
                'message' => 'Sorry, you don\'t qualify'
            ]);
        }
    }

    public function photo_submission(Request $request) {
        // $input = $request->only(['email']);
        $input = $request->all();

        // vbasic validate
        $validator = Validator::make($input, [
            'email' => 'required|email',
            'photo' => 'required|mimes:jpeg,jpg,png'
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => $validator->errors()
            ], 422);
        }

        $customer = Customer::where('email', $input['email'])->first();

        // validate if customer is not exist
        if (!$customer) {
            return response()->json([
                'message' => 'Email not found.'
            ], 422);
        }

        $voucher = Voucher::where('locked_by', $customer->id)->first();

        // validate if voucher is not exist
        if (!$voucher) {
            return response()->json([
                'message' => 'Time is up, your voucher is available to other customers.'
            ], 422);
        }

        $voucher_already = CustomerVoucher::where([
            ['customer_id', '=', $customer->id],
            ['campaign_id', '=', $voucher->campaign->id],
            ['voucher_id', '=', $voucher->id]
        ])->first();

        if ($voucher_already) {
            return response()->json([
                'message' => 'You already have a voucher.'
            ], 422);
        } else {

            $image = $request->file('photo');
            $name = time() . '.' . $image->extension();
            $path = public_path() . '/upload/photos/';
            $image->move($path, $name);

            $save_image = CustomerImage::create([
                'customer_id' => $customer->id,
                'campaign_id' => $voucher->campaign->id,
                'path' => $path
            ]);

            if ($save_image) {
                CustomerVoucher::create([
                    'customer_id' => $customer->id,
                    'voucher_id' => $voucher->id,
                    'campaign_id' => $voucher->campaign->id
                ]);
            }

            return response()->json([
                'message' => 'Your voucher code is ' . $voucher->code,
            ]);

        }
    }
}
