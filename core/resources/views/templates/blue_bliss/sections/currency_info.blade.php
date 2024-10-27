@php
    $currencyInfoContent = getContent('currency_info.content', true);
    $currencies = App\Models\Currency::enabled()->availableForSell()->availableForBuy()->desc()->get();

    $reserveCurrencies = App\Models\Currency::enabled()
        ->availableForSell()
        ->availableForBuy()
        ->where('show_rate', Status::YES)
        ->where('reserve', '>', 0)
        ->asc('name')
        ->get();
@endphp
<section class="reserve-section padding-top padding-bottom">
    <div class="container">
        <div class="section-header">
            <h2 class="title">{{ __(@$currencyInfoContent->data_values->heading) }}</h2>
            <p>{{ __(@$currencyInfoContent->data_values->subheading) }}</p>
        </div>
        <div class="row gy-4">
            <div class="col-xl-6 col-lg-7">
                <div class="custom-widget">
                    <h6 class="custom-widget-title mb-3">@lang('Exchange Rates Now')</h6>
                    @if (!$currencies->isEmpty())
                        <div class="currency-wrapper">
                            <div class="currency-wrapper__header">
                                <p class="currency-wrapper__name">@lang('Currency')</p>
                                <div class="currency-wrapper__content">
                                    <span class="buy-sell">@lang('Buy At')</span>
                                    <span class="buy-sell">@lang('Sell At')</span>
                                </div>
                            </div>
                            <ul class="currency-list">
                                @foreach ($currencies as $currency)
                                    <li class="currency-list__item">
                                        <div class="currency-list__wrapper">
                                            <div class="currency-list__left">
                                                <div class="currency-list__thumb">
                                                    <img src="{{ getImage(getFilePath('currency') . '/' . $currency->image, getFileSize('currency')) }}"
                                                        class="thumb" alt="currency image">
                                                </div>
                                                <span class="currency-list__text">
                                                    {{ __($currency->name) }} -
                                                    {{ __($currency->cur_sym) }}
                                                </span>
                                            </div>
                                            <div class="currency-list__content">
                                                <span class="buy-sell">
                                                    {{ __(gs('cur_sym')) }}{{ showAmount($currency->sell_at, currencyFormat: false) }}
                                                </span>
                                                <span class="buy-sell">
                                                    {{ __(gs('cur_sym')) }}{{ showAmount($currency->buy_at, currencyFormat: false) }}
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        @include($activeTemplate . 'partials.empty', [
                            'message' => 'No exchange rate found',
                        ])
                    @endif
                </div>
            </div>
            <div class="col-xl-6 col-lg-5">
                <div class="custom-widget">
                    <h6 class="custom-widget-title mb-3">@lang('Our Reserves')</h6>
                    @if (!$reserveCurrencies->isEmpty())
                        <div class="currency-wrapper">
                            <div class="currency-wrapper__header">
                                <p class="currency-wrapper__name">@lang('Currency')</p>
                                <div class="currency-wrapper__content">
                                    <span class="buy-sell">@lang('Reserved')</span>
                                </div>
                            </div>
                            <ul class="currency-list">
                                @foreach ($reserveCurrencies as $currency)
                                    <li class="currency-list__item">
                                        <div class="currency-list__wrapper">
                                            <div class="currency-list__left">
                                                <div class="currency-list__thumb">
                                                    <img src="{{ getImage(getFilePath('currency') . '/' . @$currency->image, getFileSize('currency')) }}"
                                                        alt="currency-image" class="thumb">
                                                </div>
                                                <span class="currency-list__text">
                                                    {{ __($currency->name) }} - {{ __($currency->cur_sym) }}
                                                </span>
                                            </div>
                                            <div class="currency-list__content">
                                                <span class="buy-sell two">
                                                    {{ showAmount($currency->reserve, currencyFormat: false) }}
                                                    {{ __($currency->cur_sym) }}
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        @include($activeTemplate . 'partials.empty', [
                            'message' => 'No reserves found',
                        ])
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@push('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/slick.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/slick.min.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            'use strict'
            document.addEventListener("DOMContentLoaded", function() {
                const currencyLists = document.querySelectorAll(".currency-list");
                currencyLists.forEach(singleList => {
                    const items = singleList.querySelectorAll(".currency-list__item");
                    if (items.length > 5) {
                        $(singleList).slick({
                            autoplay: true,
                            dots: false,
                            infinite: true,
                            speed: 3000,
                            slidesToShow: 5,
                            arrows: false,
                            slidesToScroll: 4,
                            cssEase: "linear",
                            vertical: true,
                            autoplaySpeed: 0,
                            verticalSwiping: true,
                            swipeToSlide: true,
                            swipe: true,
                            focusOnHover: true,
                            pauseOnHover: true,
                        });
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
