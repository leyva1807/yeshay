@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="container">
        <div class="row gy-4">
            <div class="col-12">
                <div class="card custom--card">
                    <div class="card-body">
                        <h6 class="text-center"> @lang('Exchange ID: ') <span
                                class="text-muted">#{{ $exchange->exchange_id }}</span></h6>
                        <p class="mt-1 fw-bold text-center text--warning">
                            @lang('Send')
                            {{ showAmount($exchange->sending_amount + $exchange->sending_charge, currencyFormat: false) }}
                            {{ __(ucfirst(@$exchange->sendCurrency->cur_sym)) }} @lang('via')
                            {{ __(@$exchange->sendCurrency->name) }} @lang('to get')
                            {{ showAmount($exchange->receiving_amount - $exchange->receiving_charge, currencyFormat: false) }}
                            {{ __(ucfirst(@$exchange->receivedCurrency->cur_sym)) }} @lang('via')
                            {{ __(@$exchange->receivedCurrency->name) }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card custom--card">
                    <div class="card-header">
                        <h5 class="card-title">@lang('Sending Details')</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush p-3">
                            <li class="list-group-item d-flex justify-content-between flex-wrap border-dotted">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="svg__icon">
                                        <img src="{{ getImage($activeTemplateTrue . 'images/svg/method_icon.php') }}?color={{ gs('base_color') }}"
                                            alt="icon-image">
                                    </span>
                                    <small class="fw-bold">@lang('Method')</small>
                                </div>
                                <span class="d-flex align-items-center">
                                    <div class="thumb me-2">
                                        <img class="currency__image"
                                            src="{{ getImage(getFilePath('currency') . '/' . @$exchange->sendCurrency->image, getFileSize('currency')) }}"
                                            alt="currency image">
                                    </div>
                                    <span class="fw-bold">{{ __(@$exchange->sendCurrency->name) }}</span>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between flex-wrap border-dotted">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="svg__icon">
                                        <img src="{{ getImage($activeTemplateTrue . 'images/svg/currency_icon.php') }}?color={{ gs('base_color') }}"
                                            alt="icon-image">
                                    </span>
                                    <small class="fw-bold">@lang('Currency')</small>
                                </div>
                                <span class="fw-bold">{{ __(ucfirst(@$exchange->sendCurrency->cur_sym)) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between flex-wrap border-dotted">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="svg__icon">
                                        <img src="{{ getImage($activeTemplateTrue . 'images/svg/amount_icon.php') }}?color={{ gs('base_color') }}"
                                            alt="icon-image">
                                    </span>
                                    <small class="fw-bold">@lang('Amount')</small>
                                </div>
                                <span class="fw-bold">
                                    {{ showAmount(@$exchange->sending_amount, currencyFormat: false) }}
                                    {{ __(ucfirst(@$exchange->sendCurrency->cur_sym)) }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between flex-wrap border-dotted">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="svg__icon">
                                        <img src="{{ getImage($activeTemplateTrue . 'images/svg/charge_icon.php') }}?color={{ gs('base_color') }}"
                                            alt="icon-image">
                                    </span>
                                    <small class="fw-bold">@lang('Charge')</small>
                                </div>
                                <span class="fw-bold text--danger">
                                    {{ showAmount(@$exchange->sending_charge, currencyFormat: false) }}
                                    {{ __(ucfirst(@$exchange->sendCurrency->cur_sym)) }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between flex-wrap border-dotted">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="svg__icon">
                                        <img src="{{ getImage($activeTemplateTrue . 'images/svg/total_icon.php') }}?color={{ gs('base_color') }}"
                                            alt="icon-image">
                                    </span>
                                    <small class="fw-bold">@lang('Total Sending Amount Including Charge')</small>
                                </div>
                                <span class="fw-bold">
                                    {{ showAmount($exchange->sending_amount + $exchange->sending_charge, currencyFormat: false) }}
                                    {{ __(ucfirst(@$exchange->sendCurrency->cur_sym)) }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card custom--card">
                    <div class="card-header">
                        <h5 class="card-title">@lang('Receiving Details')</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush p-3">
                            <li class="list-group-item d-flex justify-content-between flex-wrap border-dotted">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="svg__icon">
                                        <img src="{{ getImage($activeTemplateTrue . 'images/svg/method_icon.php') }}?color={{ gs('base_color') }}"
                                            alt="icon-image">
                                    </span>
                                    <small class="fw-bold">@lang('Method')</small>
                                </div>
                                <span class="d-flex align-items-center">
                                    <div class="thumb me-2">
                                        <img class="currency__image"
                                            src="{{ getImage(getFilePath('currency') . '/' . @$exchange->receivedCurrency->image, getFileSize('currency')) }}"
                                            alt="currency image">
                                    </div>
                                    <span class="fw-bold">{{ __(@$exchange->receivedCurrency->name) }}</span>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between flex-wrap border-dotted">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="svg__icon">
                                        <img src="{{ getImage($activeTemplateTrue . 'images/svg/currency_icon.php') }}?color={{ gs('base_color') }}"
                                            alt="icon-image">
                                    </span>
                                    <small class="fw-bold">@lang('Currency')</small>
                                </div>
                                <span class="fw-bold">
                                    {{ __(ucfirst(@$exchange->receivedCurrency->cur_sym)) }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between flex-wrap border-dotted">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="svg__icon">
                                        <img src="{{ getImage($activeTemplateTrue . 'images/svg/amount_icon.php') }}?color={{ gs('base_color') }}"
                                            alt="icon-image">
                                    </span>
                                    <small class="fw-bold">@lang('Amount')</small>
                                </div>
                                <span class="fw-bold">
                                    {{ showAmount(@$exchange->receiving_amount, currencyFormat: false) }}
                                    {{ __(ucfirst(@$exchange->receivedCurrency->cur_sym)) }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between flex-wrap border-dotted">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="svg__icon">
                                        <img src="{{ getImage($activeTemplateTrue . 'images/svg/charge_icon.php') }}?color={{ gs('base_color') }}"
                                            alt="icon-image">
                                    </span>
                                    <small class="fw-bold">@lang('Charge')</small>
                                </div>
                                <span class="fw-bold text--danger">
                                    {{ showAmount(@$exchange->receiving_charge, currencyFormat: false) }}
                                    {{ __(ucfirst(@$exchange->receivedCurrency->cur_sym)) }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between flex-wrap border-dotted">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="svg__icon">
                                        <img src="{{ getImage($activeTemplateTrue . 'images/svg/total_icon.php') }}?color={{ gs('base_color') }}"
                                            alt="icon-image">
                                    </span>
                                    <small class="fw-bold">@lang('Receivable Amount After Charge')</small>
                                </div>
                                <span class="fw-bold">
                                    {{ showAmount($exchange->receiving_amount - $exchange->receiving_charge, currencyFormat: false) }}
                                    {{ __(ucfirst(@$exchange->receivedCurrency->cur_sym)) }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card custom--card">
                    <div class="card-body">
                        <form method="post" action="{{ route('user.exchange.confirm') }}" enctype="multipart/form-data"
                            class="disableSubmission">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">
                                    @lang('Your')
                                    {{ __(@$exchange->receivedCurrency->name) }}
                                    @lang('Wallet Number/ID')
                                </label>
                                <input type="text" class="form-control form--control" name="wallet_id" required>
                            </div>
                            <x-viser-form identifier="id"
                                identifierValue="{{ @$exchange->receivedCurrency->userDetailsData->id }}" />
                            <button class="btn btn--base w-100 confirmationBtn" type="submit">
                                @lang('Confirm Exchange')
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
