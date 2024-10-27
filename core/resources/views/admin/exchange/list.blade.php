@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Exchange ID')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Received Method')</th>
                                    <th>@lang('Received Amount')</th>
                                    <th>@lang('Send Method')</th>
                                    <th>@lang('Send Amount')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($exchanges as $exchange)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $exchange->exchange_id }}</span>
                                            <br>
                                            <small class="text-muted">{{ showDateTime($exchange->created_at) }}</small>
                                        </td>
                                        <td>
                                            <span class="d-block">{{ __(@$exchange->user->fullname) }}</span>
                                            <span>
                                                <a class="text--primary"
                                                    href="{{ route('admin.users.detail', @$exchange->user_id) }}">
                                                    <span class="text--primary">@</span>{{ __(@$exchange->user->username) }}
                                                </a>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="d-block">{{ __(@$exchange->sendCurrency->name) }}</span>
                                            <span class="text--primary">{{ __(@$exchange->sendCurrency->cur_sym) }}</span>
                                        </td>
                                        <td>
                                            <span class="d-block">
                                                {{ showAmount($exchange->sending_amount, currencyFormat: false) }}
                                                {{ __(@$exchange->sendCurrency->cur_sym) }}
                                            </span>
                                            <span>
                                                {{ showAmount($exchange->sending_amount, currencyFormat: false) }}
                                            </span>
                                            +
                                            <span class="text--danger">
                                                {{ showAmount($exchange->sending_charge, currencyFormat: false) }}
                                            </span>
                                            =
                                            <span>
                                                {{ showAmount($exchange->sending_amount + $exchange->sending_charge, currencyFormat: false) }}
                                                {{ __(@$exchange->sendCurrency->cur_sym) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="d-block">{{ __(@$exchange->receivedCurrency->name) }}</span>
                                            <span
                                                class="text--primary">{{ __(@$exchange->receivedCurrency->cur_sym) }}</span>
                                        </td>
                                        <td>
                                            <span class="d-block">
                                                {{ showAmount($exchange->receiving_amount, currencyFormat: false) }}
                                                {{ __(@$exchange->receivedCurrency->cur_sym) }}
                                            </span>
                                            <span>
                                                {{ showAmount($exchange->receiving_amount, currencyFormat: false) }}
                                            </span>
                                            -
                                            <span class="text--danger">
                                                {{ showAmount($exchange->receiving_charge, currencyFormat: false) }}
                                            </span>
                                            =
                                            <span>
                                                {{ showAmount($exchange->receiving_amount - $exchange->receiving_charge, currencyFormat: false) }}
                                                {{ __(@$exchange->receivedCurrency->cur_sym) }}
                                            </span>
                                        </td>
                                        <td> @php echo $exchange->badgeData() @endphp </td>
                                        <td>
                                            <a href="{{ route('admin.exchange.details', $exchange->id) }}"
                                                class="btn btn-sm btn-outline--primary">
                                                <i class="las la-desktop"></i>@lang('Details')
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-muted text-center">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($exchanges->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($exchanges) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Exchange ID, username" dateSearch='yes' />
@endpush
