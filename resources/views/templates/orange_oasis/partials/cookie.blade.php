@php
    $cookie = App\Models\Frontend::where('data_keys', 'cookie.data')->first();
@endphp
@if ($cookie->data_values->status == Status::ENABLE && !\Cookie::get('gdpr_cookie'))
    <div class="cookies-card text-center hide">
        <div class="cookies-card__icon bg--base">
            <i class="las la-cookie-bite"></i>
        </div>
        <p class="mt-4 cookies-card__content">
            {{ __($cookie->data_values->short_desc) }}
            <a href="{{ route('cookie.policy') }}" target="_blank" class="text--base">
                @lang('learn more')
            </a>
        </p>
        <div class="cookies-card__btn mt-4">
            <button type="button" class="btn btn--base w-100 policy cookie-btn">@lang('Allow')</button>
        </div>
    </div>
@endif

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.cookie-btn').on('click', function(e) {
                $.ajax({
                    url: `{{ route('cookie.accept') }}`,
                    type: "GET",
                    dataType: 'json',
                    cache: false,
                    success: function(response) {
                        if (response.success) {
                            notify('success', response.message);
                            $('.cookies-card').remove();
                        }
                    },
                });
            });
        })(jQuery);
    </script>
@endpush
