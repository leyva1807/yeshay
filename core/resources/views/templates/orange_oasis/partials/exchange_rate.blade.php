@php
    $currencies = App\Models\Currency::enabled()->availableForSell()->availableForBuy()->desc()->get();
@endphp
<div class="custom-widget mb-4">
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
                                        class="thumb">
                                </div>
                                <span class="currency-list__text">
                                    {{ __($currency->name) }} -
                                    {{ __($currency->cur_sym) }}
                                </span>
                            </div>
                            <div class="currency-list__content">
                                <span class="buy-sell">{{ gs('cur_sym') }}{{ showAmount($currency->sell_at, currencyFormat: false) }}</span>
                                <span class="buy-sell">{{ gs('cur_sym') }}{{ showAmount($currency->buy_at, currencyFormat: false) }}</span>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        @include($activeTemplate . 'partials.empty', ['message' => 'No exchange rate found'])
    @endif
</div>

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
                            slidesToScroll: 1,
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
