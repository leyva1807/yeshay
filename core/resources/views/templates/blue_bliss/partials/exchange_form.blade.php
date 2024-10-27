<form class="exchange-form disableSubmission" method="POST" action="{{ route('exchange.start') }}" id="exchange-form">
    @csrf
    <div class="form-group sendData">
        <div class="input-wrapper">
            <input type="number" step="any" name="sending_amount" id="sending_amount" class="form--control"
                placeholder="@lang('You Send')" value="{{ old('sending_amount') }}" required>
            <select required class="select2 form-control form--control" data-type="select" name="sending_currency"
                id="send">
                <option value="" selected disabled>@lang('Select One')</option>
                @foreach ($sellCurrencies as $sellCurrency)
                    <option
                        data-image="{{ getImage(getFilePath('currency') . '/' . @$sellCurrency->image, getFileSize('currency')) }}"
                        data-min="{{ getAmount($sellCurrency->minimum_limit_for_buy) }}"
                        data-max="{{ getAmount($sellCurrency->maximum_limit_for_buy) }}"
                        data-buy="{{ getAmount($sellCurrency->buy_at) }}" data-currency="{{ @$sellCurrency->cur_sym }}"
                        value="{{ $sellCurrency->id }}" data-select-for="send" @selected(old('sending_currency') == $sellCurrency->id)>
                        {{ __($sellCurrency->name) }} - {{ __($sellCurrency->cur_sym) }}
                    </option>
                @endforeach
            </select>
        </div>
        <span class="d-none" id="currency-limit"></span>
    </div>
    <span class="exchange-form__icon">
        <i class="las la-exchange-alt"></i>
    </span>
    <div class="form-group receiveData ">
        <div class="input-wrapper">
            <input type="number" step="any" name="receiving_amount" class="form--control" id="receiving_amount"
                value="{{ old('receiving_amount') }}" placeholder="@lang('You Get')" required>
            <select class="select2 form-control form--control" name="receiving_currency" id="receive" required
                value.bind="selectedThing2">


                <option value="" selected disabled>@lang('Select One')</option>
                @foreach ($buyCurrencies as $buyCurrency)
                    <option
                        data-image="{{ getImage(getFilePath('currency') . '/' . @$buyCurrency->image, getFileSize('currency')) }}"
                        data-sell="{{ getAmount($buyCurrency->sell_at) }}"
                        data-currency="{{ @$buyCurrency->cur_sym }}"
                        data-min="{{ getAmount($buyCurrency->minimum_limit_for_sell) }}"
                        data-max="{{ getAmount($buyCurrency->maximum_limit_for_sell) }}"
                        data-reserve="{{ getAmount($buyCurrency->reserve) }}" value="{{ $buyCurrency->id }}"
                        data-select-for="received" @selected(old('receiving_currency') == $buyCurrency->id)>
                        {{ __($buyCurrency->name) }} - {{ __($buyCurrency->cur_sym) }}
                    </option>
                @endforeach
            </select>
        </div>
        <span class="d-none" id="currency-limit-received"></span>
    </div>
    <div class="exchange-btn">
        <button type="submit" class="btn--base btn">@lang('Exchange')</button>
    </div>
</form>

@push('style-lib')
    <link href="{{ asset('assets/global/css/select2.min.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            let sendId, sendMinAmount, sendMaxAmount, sendAmount, sendCurrency, sendCurrencyBuyRate;
            let receivedId, receivedAmount, receivedCurrency, receiveCurrencySellRate;

            //=============change select2 structure
            $('.select2').select2({
                templateResult: formatState
            });

            function formatState(state) {
                if (!state.id) return state.text;
                let selectType = $(state.element).data('select-for').toUpperCase();
                if (sendId && selectType == 'RECEIVED' && sendId == state.element.value) {
                    return false;
                }
                if (receivedId && selectType == 'SEND' && receivedId == state.element.value) {
                    return false;
                }
                return $('<img class="ms-1"   src="' + $(state.element).data('image') + '"/> <span class="ms-3">' +
                    state.text + '</span>');
            }

            @if (old('sending_currency'))
                sendAmount = "{{ old('sending_amount') }}";
                sendAmount = parseFloat(sendAmount);
                $("#sending_amount").val(sendAmount.toFixed("{{ gs('show_number_after_decimal') }}"));
                setTimeout(() => {
                    $('#send').trigger('change');
                });
            @endif

            @if (old('receiving_currency'))
                receivedAmount = "{{ old('receiving_amount') }}";
                receivedAmount = parseFloat(receivedAmount);
                $("#receiving_amount").val(receivedAmount.toFixed("{{ gs('show_number_after_decimal') }}"));
                setTimeout(() => {
                    $('#receive').trigger('change');
                });
            @endif

            $('[name=sending_currency]').on('change', function(e) {
                sendId = parseInt($(this).val());
                sendMinAmount = parseFloat($(this).find(':selected').data('min'));
                sendMaxAmount = parseFloat($(this).find(':selected').data('max'));
                sendCurrency = $(this).find(':selected').data('currency');
                sendCurrencyBuyRate = parseFloat($(this).find(':selected').data('buy'));

                $('#currency-limit').html(
                    `@lang('Limit') <span class="text--base">${sendMinAmount.toFixed(2)}</span> - <span class="text--base">${sendMaxAmount.toFixed(2)}</span> ${sendCurrency}`
                    );
                $('#currency-limit').removeClass('d-none').addClass("d-block mt-2");

                $("#sending_amount").siblings('.input-group-text').removeClass('d-none');
                $("#sending_amount").removeClass('rounded');
                $("#sending_amount").siblings('.input-group-text').text(sendCurrency);

                if (sendId) {
                    $(this).closest('.form-group').find('.select2-selection__rendered').html(
                        `<img src="${$(this).find(':selected').data('image')}" class="currency-image"/> ${$(this).find(':selected').text()}`
                    )
                    calculationReceivedAmount();
                }
            });

            $('[name=receiving_currency]').on('change', function(e) {
                receivedId = parseInt($(this).val());
                receiveCurrencySellRate = parseFloat($(this).find(':selected').data('sell'));
                receivedCurrency = $(this).find(':selected').data('currency');

                let minAmount = parseFloat($(this).find(':selected').data('min'));
                let maxAmount = parseFloat($(this).find(':selected').data('max'));
                let reserveAmount = parseFloat($(this).find(':selected').data('reserve'))

                $('#currency-limit-received').html(
                    `@lang('Limit')
                    <span class="text--base">${minAmount.toFixed(2)}</span> - <span class="text--base">${maxAmount.toFixed(2)}</span>
                    ${receivedCurrency} | Reserve <span class="text--base">${reserveAmount.toFixed(2)}</span> ${receivedCurrency}`
                );

                $('#currency-limit-received').removeClass('d-none').addClass("d-block mt-2");
                $("#receiving_amount").siblings('.input-group-text').removeClass('d-none');
                $("#receiving_amount").removeClass('rounded');
                $("#receiving_amount").siblings('.input-group-text').text(receivedCurrency);

                if (receivedId) {
                    $(this).closest('.form-group').find('.select2-selection__rendered').html(
                        `<img src="${$(this).find(':selected').data('image')}" class="currency-image"/> ${$(this).find(':selected').text()}`
                    )
                    calculationReceivedAmount();
                }
            });

            $('#sending_amount').on('input', function(e) {
                sendAmount = parseFloat(this.value);
                if (sendAmount < 0) {
                    sendAmount = 0;
                    notify('error', 'Negative amount is not allowed');
                    $(this).val('');
                    $('input[name="receiving_amount"]').val('');
                } else {
                    calculationReceivedAmount();
                }
            });

            $('#receiving_amount').on('input', function(e) {
                receivedAmount = parseFloat(this.value);
                if (receivedAmount < 0) {
                    notify('error', 'Negative amount is not allowed');
                    receivedAmount = 0;
                    $(this).val('');
                    $('input[name="sending_amount"]').val('');
                } else {
                    calculationSendAmount();
                }
            });

            const calculationReceivedAmount = () => {
                if (!sendId && !receivedId && !sendCurrencyBuyRate && !receiveCurrencySellRate) {
                    return false;
                }
                let amountReceived = (sendCurrencyBuyRate / receiveCurrencySellRate) * sendAmount;
                $("#receiving_amount").val(amountReceived.toFixed({{ gs('show_number_after_decimal') }}));
            }

            const calculationSendAmount = () => {
                if (!sendId && !receivedId && !sendCurrencyBuyRate && !receiveCurrencySellRate) {
                    return false;
                }
                let amountReceived = (receiveCurrencySellRate / sendCurrencyBuyRate) * receivedAmount;
                $("#sending_amount").val(amountReceived.toFixed({{ gs('show_number_after_decimal') }}));
            }
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .select2-container .select2-selection--single {
            height: 46px;
            width: 100%;
        }

        .select2-search--dropdown {
            display: block !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px;
        }

        .select2-container--default img {
            width: 28px;
            height: 28px;
            object-fit: contain;
        }

        .select2-results__option--selectable {
            display: flex;
        }

        .select2-container .selection {
            width: 220px;
            height: 48px;
            -moz-border-radius: 0;
            border-radius: 0;
            position: absolute;
            right: 4px;
            top: 50%;
            padding: 0 10px;
            transform: translateY(-50%);
            display: flex;
            align-items: center;
            border-radius: 2px;
            background: #E8E8E8;
            border: 0 !important;
        }

        @media (max-width:1199px) {
            .select2-container .selection {
                width: 170px;
            }
        }

        @media (max-width:991px) {
            .select2-container .selection {
                width: 296px;
            }
        }

        @media (max-width:767px) {
            .select2-container .selection {
                width: 235px;
            }
        }

        @media (max-width:575px) {
            .select2-container .selection {
                width: 215px;
            }
        }

        @media (max-width:424px) {
            .select2-container .selection {
                transform: unset;
                position: relative;
                width: 100%;
                right: 0;
            }
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            top: 80%;
        }

        .select2-container--default .select2-results__option--disabled {
            display: none;
        }

        .select2-dropdown {
            border: 1px solid #aaaaaa2e;
        }

        img.currency-image {
            width: 25px;
            height: 25px;
            margin-right: 8px;
        }

        .select2-container--default .select2-selection--single {
            border: 0;
            background-color: transparent;
        }

        .select2-results__option:empty {
            display: none !important;
        }


        .select2-container--default .select2-selection--single .select2-selection__arrow:after {
            top: 4px !important;
        }
    </style>
@endpush
