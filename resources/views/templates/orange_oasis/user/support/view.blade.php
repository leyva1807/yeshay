@extends($activeTemplate . 'layouts.' . $layout)
@section('content')
    <div class="container {{ $layout == 'frontend' ? 'pt-80 pb-80' : '' }}">
        <div class="row">
            <div class="col-lg-12">
                <div class="card custom--card">
                    <div class="p-3 d-flex justify-content-between flex-wrap border-bottom gap-2">
                        <h5 class="title">
                            @php echo $myTicket->statusBadge; @endphp
                            [@lang('Ticket')#{{ $myTicket->ticket }}] {{ $myTicket->subject }}
                        </h5>
                        @if ($myTicket->status != Status::TICKET_CLOSE && $myTicket->user)
                            <div>
                                @auth
                                    <a class="btn btn--base btn-sm" href="{{ route('ticket.index') }}">
                                        <i class="la la-reply"></i> @lang('My Tickets')
                                    </a>
                                @endauth
                                <button class="btn btn--danger close-button btn-sm confirmationBtn" type="button"
                                        data-question="@lang('Are you sure to close this ticket?')"
                                        data-action="{{ route('ticket.close', $myTicket->id) }}">
                                    <i class="fa fa-times-circle"></i> @lang('Close Ticket')
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="card-body">
                        <form action="{{ route('ticket.reply', $myTicket->id) }}" method="POST"
                              enctype="multipart/form-data" class="disableSubmission">
                            @csrf
                            <div class="row gy-3">
                                <div class="form--group col-sm-12">
                                    <label for="message" class="form-label required">@lang('Message')</label>
                                    <textarea name="message" class="form-control form--control" required>{{ old('message') }}</textarea>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-9">
                                    <button type="button" class="btn btn-dark btn-sm addAttachment my-2">
                                        <i class="fas fa-plus"></i> @lang('Add Attachment')
                                    </button>
                                    <p class="mb-2">
                                        <span class="text--info">
                                            @lang('Max 5 files can be uploaded | Maximum upload size is ' . convertToReadableSize(ini_get('upload_max_filesize')) . ' | Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx')
                                        </span>
                                    </p>
                                    <div class="row fileUploadsContainer">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn--base w-100 my-2" type="submit">
                                        <i class="la la-fw la-lg la-reply"></i> @lang('Reply')
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card custom--card mt-4">
                    <div class="card-body">
                        @foreach ($messages as $message)
                            @if ($message->admin_id == 0)
                                <div class="row border border--base border-radius-3 my-3 py-3 mx-2">
                                    <div class="col-md-3 border-end text-end">
                                        <h5 class="my-3">{{ $message->ticket->name }}</h5>
                                    </div>
                                    <div class="col-md-9">
                                        <p class="text-muted fw-bold my-3">
                                            @lang('Posted on') {{ $message->created_at->format('l, dS F Y @ H:i') }}</p>
                                        <p>{{ $message->message }}</p>
                                        @if ($message->attachments->count() > 0)
                                            <div class="mt-2">
                                                @foreach ($message->attachments as $k => $image)
                                                    <a href="{{ route('ticket.download', encrypt($image->id)) }}"
                                                       class="me-3 text--base"><i class="fa fa-file"></i>
                                                        @lang('Attachment') {{ ++$k }} </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="row border border--warning border-radius-3 my-3 py-3 mx-2"
                                     style="background-color: #ffd96729">
                                    <div class="col-md-3 border-end text-end">
                                        <h5 class="my-3">{{ $message->admin->name }}</h5>
                                        <p class="lead text-muted">@lang('Staff')</p>
                                    </div>
                                    <div class="col-md-9">
                                        <p class="text-muted fw-bold my-3">
                                            @lang('Posted on') {{ $message->created_at->format('l, dS F Y @ H:i') }}</p>
                                        <p>{{ $message->message }}</p>
                                        @if ($message->attachments->count() > 0)
                                            <div class="mt-2">
                                                @foreach ($message->attachments as $k => $image)
                                                    <a href="{{ route('ticket.download', encrypt($image->id)) }}"
                                                       class="me-3"><i class="fa fa-file"></i> @lang('Attachment')
                                                        {{ ++$k }} </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            var fileAdded = 0;
            $('.addAttachment').on('click', function() {
                fileAdded++;
                if (fileAdded == 5) {
                    $(this).attr('disabled', true)
                }
                $(".fileUploadsContainer").append(`
                <div class="col-lg-4 col-md-12 removeFileInput">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="file" name="attachments[]" class="form-control form--control" accept=".jpeg,.jpg,.png,.pdf,.doc,.docx" required>
                            <button type="button" class="input-group-text removeFile bg--danger border--danger"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                </div>
            `)
            });
            $(document).on('click', '.removeFile', function() {
                $('.addAttachment').removeAttr('disabled', true)
                fileAdded--;
                $(this).closest('.removeFileInput').remove();
            });
            $("#confirmationModal").find(".btn--primary").addClass("btn--base").removeClass("btn--primary");
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .close {
            cursor: pointer;
            background: transparent;
            border: none;
        }

        .modal p {
            margin: 0px;
        }

        .input-group-text {
            border-color: hsl(var(--base)) !important;
        }

        .input-group-text:focus {
            box-shadow: none !important;
        }

        .reply-bg {
            background-color: #ffd96729
        }

        .empty-message img {
            width: 120px;
            margin-bottom: 15px;
        }

        .removeFile {
            color: #ffffff;
        }

        .form-control:focus {
            box-shadow: none;
        }
    </style>
@endpush
