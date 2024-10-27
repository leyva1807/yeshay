<?php

namespace App\Http\Controllers\User;

use Exception;
use App\Lib\Intended;
use App\Models\Deposit;
use App\Models\Exchange;
use App\Constants\Status;
use App\Lib\FormProcessor;
use Illuminate\Http\Request;
use App\Lib\CurrencyExchanger;
use App\Models\GatewayCurrency;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\AdminNotification;
use App\Http\Controllers\Controller;

class ExchangeController extends Controller
{
    public function exchange(Request $request)
    {
        $currencyExchanger = new CurrencyExchanger();
        $message           = $currencyExchanger->currencyExchanger($request);

        if ($message['status'] == 'error') {
            return back()->withNotify($message['notify'])->withInput();
        }

        if (!auth()->user()) {
            session()->put('exchange_data', $currencyExchanger);
            Intended::identifyRoute();
            return to_route('user.login');
        }

        $currencyExchanger->createExchange();
        return to_route('user.exchange.preview');
    }

    public function preview()
    {
        if (!session()->has('EXCHANGE_TRACK')) {
            $notify[] = ['error', "Invalid session"];
            return to_route('home')->withNotify($notify);
        }
        $pageTitle = 'Exchange Preview';
        $exchange  = Exchange::where('exchange_id', session('EXCHANGE_TRACK'))->with('receivedCurrency.userDetailsData')->firstOrFail();
        return view('Template::user.exchange.preview', compact('pageTitle', 'exchange'));
    }

    public function confirm(Request $request)
    {
        if (!session()->has('EXCHANGE_TRACK')) {
            $notify[] = ['error', "Invalid session"];
            return to_route('home')->withNotify($notify);
        }
        $validation = [
            'wallet_id' => 'required'
        ];

        $exchange         = Exchange::where('exchange_id', session()->get('EXCHANGE_TRACK'))->firstOrFail();
        $userRequiredData = @$exchange->receivedCurrency->userDetailsData->form_data ?? [];

        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($userRequiredData);
        $validationRule = array_merge($validationRule, $validation);
        $request->validate($validationRule);

        $userData            = $formProcessor->processFormData($request, $userRequiredData);
        $exchange->user_data = $userData ?? null;
        $exchange->wallet_id = $request->wallet_id;
        $exchange->save();

        //=====automatic payment

        if ($exchange->sendCurrency->gateway_id != 0) {
            return $this->createDeposit($exchange);
        }
        return to_route('user.exchange.manual');
    }

    public function manual()
    {
        if (!session()->has('EXCHANGE_TRACK')) {
            $notify[] = ['error', "Something went the wrong with exchange processing"];
            return to_route('home')->withNotify($notify);
        }
        $exchange  = Exchange::where('exchange_id', session()->get('EXCHANGE_TRACK'))->firstOrFail();
        $pageTitle = "Transaction Proof";
        return view('Template::user.exchange.manual', compact('pageTitle', 'exchange'));
    }

    public function manualConfirm(Request $request)
    {
        if (!session()->has('EXCHANGE_TRACK')) {
            $notify[] = ['error', "Something went the wrong with exchange processing"];
            return to_route('home')->withNotify($notify);
        }

        $exchange              = Exchange::where('exchange_id', session()->get('EXCHANGE_TRACK'))->firstOrFail();
        $transactionProvedData = @$exchange->sendCurrency->transactionProvedData->form_data ?? [];

        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($transactionProvedData);
        $request->validate($validationRule);
        $provedData = $formProcessor->processFormData($request, $transactionProvedData);

        $exchange->transaction_proof_data = $provedData ?? null;
        $exchange->status                 = Status::EXCHANGE_PENDING;
        $exchange->save();

        $comment = 'send ' . getAmount($exchange->get_amount) . ' by ' . @$exchange->sendCurrency->name;

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $exchange->user_id;
        $adminNotification->title     = $comment;
        $adminNotification->click_url = urlPath('admin.exchange.details', $exchange->id);
        $adminNotification->save();

        session()->forget('EXCHANGE_TRACK');

        $notify[] = ['success', 'Admin will review your request'];
        return to_route('user.exchange.details', $exchange->exchange_id)->withNotify($notify);
    }

    public function list($scope = 'list')
    {
        try {
            $exchanges = Exchange::$scope()->where('user_id', auth()->id())->with(['sendCurrency', 'receivedCurrency'])->desc()->paginate(getPaginate());
            $pageTitle = formateScope($scope) . " Exchange";
        } catch (Exception $ex) {
            $notify[] = ['error', 'Invalid URL.'];
            return back()->withNotify($notify);
        }
        return view('Template::user.exchange.list', compact('pageTitle', 'exchanges'));
    }

    public function details($trx)
    {
        $exchange  = Exchange::where('user_id', auth()->id())->with('deposit', function ($deposit) {
            $deposit->where('status', Status::PAYMENT_INITIATE);
        })->where('exchange_id', $trx)->firstOrFail();
        $pageTitle = 'Exchange Details';
        return view('Template::user.exchange.details', compact('pageTitle', 'exchange'));
    }

    protected function validation($request)
    {
        $request->validate([
            'sending_amount'     => 'required|numeric|gt:0',
            'receiving_amount'   => 'required|numeric|gt:0',
            'sending_currency'   => 'required|integer',
            'receiving_currency' => 'required|integer|different:sending_currency',
        ]);
    }

    public function invoice($exchangeId, $type)
    {
        $types = ['print', 'download'];

        if (!in_array($type, $types)) {
            $notify[] = ['error', "Invalid URL."];
            return to_route('user.exchange.list', 'list')->withNotify($notify);
        }
        if ($type == 'print') {
            $pageTitle = "Print Exchange";
            $action    = 'stream';
        } else {
            $pageTitle = "Download Exchange";
            $action    = 'download';
        }

        $user     = auth()->user();
        $exchange = Exchange::where('status', '!=', Status::EXCHANGE_INITIAL)
            ->where('exchange_id', $exchangeId)
            ->where('user_id', $user->id)->firstOrFail();

        $pdf      = PDF::loadView('partials.pdf', compact('pageTitle', 'user', 'exchange'));
        $fileName = $exchange->exchange_id . '_' . time();

        return $pdf->$action($fileName . '.pdf');
    }

    public function complete($id)
    {
        $exchange = Exchange::where('user_id', auth()->id())->where('id', $id)->where('status', Status::EXCHANGE_INITIAL)->firstOrFail();

        session()->put('EXCHANGE_TRACK', $exchange->exchange_id);
        if (!$exchange->wallet_id) {
            return to_route('user.exchange.preview');
        }

        if ($exchange->sendCurrency->gateway_id && !$exchange->automatic_payment_status) {
            if (!$exchange->deposit) {
                return $this->createDeposit($exchange);
            }
            session()->put('Track', $exchange->exchange_id);
            return to_route('user.deposit.confirm');
        }

        if (!$exchange->sendCurrency->gateway_id && !$exchange->transaction_proof_data) {
            return to_route('user.exchange.manual');
        }
        return back();
    }

    private function createDeposit($exchange)
    {
        $curSymbol = $exchange->sendCurrency->cur_sym;
        $code      = $exchange->sendCurrency->gatewayCurrency->code;
        $gateway   = GatewayCurrency::where('method_code', $code)->where('currency', $curSymbol)->first();

        if (!$gateway) {
            $notify[] = ['error', "Something went the wrong with exchange processing"];
            return back()->withNotify($notify);
        }
        $amount = $exchange->sending_amount + $exchange->sending_charge;

        $deposit                  = new Deposit();
        $deposit->user_id         = auth()->id();
        $deposit->method_code     = $code;
        $deposit->method_currency = strtoupper($curSymbol);
        $deposit->amount          = $amount;
        $deposit->charge          = $exchange->sending_charge;
        $deposit->rate            = $exchange->buy_rate;
        $deposit->final_amount    = $amount;
        $deposit->btc_amount      = 0;
        $deposit->btc_wallet      = "";
        $deposit->trx             = $exchange->exchange_id;
        $deposit->try             = 0;
        $deposit->success_url     = urlPath('user.exchange.list');
        $deposit->failed_url      = urlPath('user.exchange.list');
        $deposit->status          = 0;
        $deposit->exchange_id     = $exchange->id;
        $deposit->save();

        session()->put('Track', $deposit->trx);
        return to_route('user.deposit.confirm');
    }
}
