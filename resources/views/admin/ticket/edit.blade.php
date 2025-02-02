@extends('admin.index')
@section('content')
<div class="row">
    <div class="col-12 chat-app">
        {{-- <div class="d-flex flex-row justify-content-between mb-3 chat-heading-container">
            <div class="d-flex flex-row chat-heading">
                <a class="d-flex" href="#">
                    <img alt="Profile Picture" src="{{ asset('img/User.png') }}"
                        class="img-thumbnail border-0 rounded-circle ml-0 mr-4 list-thumbnail align-self-center small">
                </a>
                <div class=" d-flex min-width-zero">
                    <div
                        class="card-body pl-0 align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero">
                        <div class="min-width-zero">
                            <a href="#">
                                <p class="list-item-heading mb-1 truncate ">Sarah Kortney</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- <div class="separator mb-5"></div> --}}

        <div class="scroll">
            <div class="scroll-content">
                @foreach ($ticket->messages as $message)
                    @if ($message->client_id == null)
                        <div class="card d-inline-block mb-3 float-left mr-2">
                            <div class="position-absolute pt-1 pr-2 r-0">
                                <span class="text-extra-small text-muted">
                                    {{ \Morilog\Jalali\Jalalian::forge($message->created_at)->format('%H:%M:%S') }}
                                </span>

                                <span class="text-extra-small text-muted">
                                    {{ \Morilog\Jalali\Jalalian::forge($message->created_at)->format('%Y/%m/%d') }}
                                </span>

                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-row pb-2">
                                    <a class="d-flex" href="#">
                                        <img alt="Profile Picture" src="{{ asset('img/user-support.png') }}"
                                            class="img-thumbnail border-0 rounded-circle mr-3 list-thumbnail align-self-center xsmall">
                                    </a>
                                    <div class=" d-flex flex-grow-1 min-width-zero">
                                        <div
                                            class="m-2 pl-0 align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero">
                                            <div class="min-width-zero">
                                                <p class="mb-0 truncate list-item-heading">{{$message->user->name ?? "-"}}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="chat-text-left">
                                    <p class="mb-0 text-semi-muted">
                                        {{$message->description}}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    @else
                        <div class="card d-inline-block mb-3 float-right  mr-2" style="background: #0096882b">
                            <div class="position-absolute pt-1 pr-2 r-0">
                                <span class="text-extra-small text-muted">
                                    {{ \Morilog\Jalali\Jalalian::forge($message->created_at)->format('%H:%M:%S') }}
                                </span>
                                <span class="text-extra-small text-muted">
                                    {{ \Morilog\Jalali\Jalalian::forge($message->created_at)->format('%Y/%m/%d') }}
                                </span>

                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-row pb-2">
                                    <a class="d-flex" href="#">
                                        <img alt="Profile Picture" src="{{ asset('img/User.png') }}"
                                            class="img-thumbnail border-0 rounded-circle mr-3 list-thumbnail align-self-center xsmall">
                                    </a>
                                    <div class=" d-flex flex-grow-1 min-width-zero">
                                        <div
                                            class="m-2 pl-0 align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero">
                                            <div class="min-width-zero">
                                                <p class="mb-0 truncate list-item-heading">{{$message->client->mobile}}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="chat-text-left">
                                    <p class="mb-0 text-semi-muted">
                                        {{$message->description}}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    @endif
                @endforeach
            </div>

        </div>
    </div>
</div>

<form action="{{ route('ticket.message.create', $ticket->id) }}" method="POST">
    @csrf

    <div class="chat-input-container d-flex justify-content-between align-items-center" style="padding: 0 150px 0 35px">
        <input class="form-control flex-grow-1" name="message" type="text" placeholder="پاسخ...">
        <div>
            {{-- <button type="button" class="btn btn-outline-primary icon-button large">
                <i class="simple-icon-paper-clip"></i>
            </button> --}}
            <button type="submit" class="btn btn-primary icon-button large d-flex align-items-center justify-content-center" style="font-size: 15px">
                <i class="simple-icon-arrow-right"></i>
            </button>
        </div>
    </div>
</form>

@endsection

