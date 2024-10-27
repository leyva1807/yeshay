<?php

namespace App\Http\Controllers\Api;

use App\Models\Currency;
use App\Constants\Status;
use App\Lib\FormProcessor;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use App\Models\AdminNotification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class WithdrawController extends Controller
{
    public function withdrawMethod()
    {
        $currencies = Currency::enabled()->where('available_for_buy', Status::YES)
            ->with('userDetailsData')
            ->get();

        $notify[] = 'Withdrawals Currency';
        return response()->json([
            'remark' => 'withdraw_currency',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'currencies' => $currencies
            ]
        ]);
    }

    public function withdrawStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currency' => 'required',
            'send_amount' => 'required|numeric|gte:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark' => 'validation_error',
                'status' => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $currency = Currency::enabled()->where('available_for_buy', Status::YES)->where('id', $request->currency)->first();

        if (!$currency) {
            $notify[] =  'Withdraw currency not found.';
            return response()->json([
                'remark' => 'validation_error',
                'status' => 'error',
                'message' => ['error' => $notify],
            ]);
        }

        $formData = @$currency->userDetailsData->form_data ?? null;
        $formProcessor = new FormProcessor();
        if ($formData) {
            $validationRule = $formProcessor->valueValidation($formData);
            $validator = Validator::make($request->all(), $validationRule);
            if ($validator->fails()) {
                return response()->json([
                    'remark' => 'validation_error',
                    'status' => 'error',
                    'message' => ['error' => $validator->errors()->all()],
                ]);
            }
        }
        $formValue = $formProcessor->processFormData($request, $formData);

        $user = auth()->user();
        if ($user->ts) {
            if (!$request->authenticator_code) {
                $notify[] = 'Google authentication is required';
                return response()->json([
                    'remark' => 'validation_error',
                    'status' => 'error',
                    'message' => ['error' => $notify],
                ]);
            }
            $response = verifyG2fa($user, $request->authenticator_code);
            if (!$response) {
                $notify[] = 'Wrong verification code';
                return response()->json([
                    'remark' => 'validation_error',
                    'status' => 'error',
                    'message' => ['error' => $notify],
                ]);
            }
        }

        if ($request->send_amount < ($currency->minimum_limit_for_sell * $currency->sell_at)) {
            $notify[] =  'Your requested amount is smaller than minimum amount';
            return response()->json([
                'remark' => 'validation_error',
                'status' => 'error',
                'message' => ['error' => $notify],
            ]);
        }

        if ($request->send_amount > ($currency->maximum_limit_for_sell * $currency->sell_at)) {
            $notify[] = 'Your requested amount is larger than maximum amount';
            return response()->json([
                'remark' => 'validation_error',
                'status' => 'error',
                'message' => ['error' => $notify],
            ]);
        }

        if ($request->send_amount > $user->balance) {
            $notify[] = 'Insufficient balance for withdrawal';
            return response()->json([
                'remark' => 'validation_error',
                'status' => 'error',
                'message' => ['error' => $notify],
            ]);
        }

        $getAmount = $request->send_amount / $currency->sell_at;
        $charge    = $currency->fixed_charge_for_sell + ($getAmount * $currency->percent_charge_for_sell / 100);
        $afterCharge = $getAmount - $charge;

        if ($afterCharge <= 0) {
            $notify[] = 'Withdraw amount must be sufficient for charges';
            return response()->json([
                'remark' => 'validation_error',
                'status' => 'error',
                'message' => ['error' => $notify],
            ]);
        }

        $withdraw                       = new Withdrawal();
        $withdraw->method_id            = $currency->id;
        $withdraw->user_id              = $user->id;
        $withdraw->amount               = $request->send_amount;
        $withdraw->currency             = gs('cur_text');
        $withdraw->rate                 = $currency->sell_at;
        $withdraw->charge               = $charge;
        $withdraw->final_amount         = $getAmount;
        $withdraw->after_charge         = $afterCharge;
        $withdraw->trx                  = getTrx();
        $withdraw->status               = Status::WITHDRAW_PENDING;
        $withdraw->withdraw_information = $formValue;
        $withdraw->save();

        $user->balance -= $withdraw->amount;
        $user->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'New withdraw request from ' . $user->username;
        $adminNotification->click_url = urlPath('admin.withdraw.data.details', $withdraw->id);
        $adminNotification->save();

        notify($user, 'WITHDRAW_REQUEST', [
            'method_name'          => $withdraw->method->name,
            'method_currency'      => $withdraw->method->cur_sym,
            'method_amount'        => showAmount($withdraw->final_amount, currencyFormat: false),
            'amount'               => showAmount($withdraw->amount, currencyFormat: false),
            'charge'               => showAmount($withdraw->charge, currencyFormat: false),
            'rate'                 => showAmount($withdraw->rate, currencyFormat: false),
            'trx'                  => $withdraw->trx,
            'post_balance'         => showAmount($user->balance, currencyFormat: false),
            'balance_after_charge' => showAmount($withdraw->after_charge, currencyFormat: false),
        ]);

        $notify[] = 'Withdraw request sent successfully';
        return response()->json([
            'remark' => 'withdraw_confirmed',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'withdraw' => $withdraw
            ]
        ]);
    }

    public function withdrawLog(Request $request)
    {
        $withdraws = Withdrawal::where('user_id', auth()->id());
        if ($request->search) {
            $withdraws = $withdraws->where('trx', $request->search);
        }
        $withdraws = $withdraws->where('status', '!=', Status::PAYMENT_INITIATE)->with('method')->orderBy('id', 'desc')->paginate(getPaginate());
        $notify[] = 'Withdrawals';
        return response()->json([
            'remark' => 'withdrawals',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'withdrawals' => $withdraws
            ]
        ]);
    }
}
