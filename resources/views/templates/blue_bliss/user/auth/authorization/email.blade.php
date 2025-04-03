@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="padding-top padding-bottom section-bg">
        <div class="d-flex justify-content-center">
            <div class="verification-code-wrapper bg-white">
                <div class="verification-area">
                    <h5 class="text-center border-bottom">@lang('Verify Email Address')</h5>
                    <form action="{{ route('user.verify.email') }}" method="POST" class="submit-form disableSubmission">
                        @csrf
                        <p class="verification-text mt-3">
                            @lang('A 6 digit verification code sent to your email address'): {{ showEmailAddress(auth()->user()->email) }}
                        </p>
                        @include($activeTemplate . 'partials.verification_code')
                        <div class="mb-3">
                            <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                        </div>
                        <p class="m-0">
                            @lang('If you don\'t get any code'),
                            <span class="countdown-wrapper">
                                @lang('try again after')
                                <span id="countdown" class="fw-bold">--</span>
                                @lang('seconds')
                            </span>
                            <a href="{{ route('user.send.verify.code', 'email') }}" class="try-again-link d-none">
                                @lang('Try again')
                            </a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        var distance = Number("{{ @$user->ver_code_send_at->addMinutes(2)->timestamp - time() }}");
        var x = setInterval(function() {
            distance--;
            document.getElementById("countdown").innerHTML = distance;
            if (distance <= 0) {
                clearInterval(x);
                document.querySelector('.countdown-wrapper').classList.add('d-none');
                document.querySelector('.try-again-link').classList.remove('d-none');
            }
        }, 1000);
    </script>
@endpush
