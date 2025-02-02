@extends('admin.index')
@section('content')
    <div class="col-lg-12 col-md-12 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">همه تیکت ها</h5>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th scope="col">ردیف</th>
                        <th scope="col">عنوان</th>
                        <th scope="col">دسته بندی</th>
                        <th scope="col">اولویت</th>
                        <th scope="col">وضعیت</th>
                        <th scope="col">اپلیکیشن</th>
                        <th scope="col">کانال ورودی</th>
                        <th scope="col">عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($tickets as $product)
                        <tr>
                            <th scope="row">{{$loop->iteration}}</th>
                            <td>{{$product->title}}</td>
                            <td>
                                @if($product->appChannelCategory !== null)
                                    {{$product->appChannelCategory->title}}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($product->priority !== null)
                                    {{$product->priority->title}}
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                @if($product->ticketStatus->id == 1)
                                    <span class="badge bg-success" style="font-size: 100%;color: #fff">{{$product->ticketStatus->title}}</span>
                                @else
                                    <span class="badge bg-danger" style="font-size: 100%;color: #fff">{{$product->ticketStatus->title}}</span>
                                @endif
                            </td>

                            <td>{{$product->appChannel->app->title}}</td>
                            <td>{{$product->appChannel->channel->title}}</td>

                            <td><a href="{{ route('ticket.show', ['ticket' => $product->id]) }}">ورود به چت</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

