@extends('layouts.app')

@section('title')
<title>{{ $restaurant->name }} | Rese</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="detail">
    <div class="inner detail__inner">
        <div class="detail__contents">
            <div class="info">
                <div class="info__heading">
                    <a href="/" type="button" class="info__btn-back">&lt;</a>
                    <h1 class="info__title">{{ $restaurant->name }}</h1>
                </div>
                <div class="info__photo">
                    <img src="{{ asset($restaurant->photo) }}" alt="{{ $restaurant->name }}" class="info__photo-img">
                </div>
                <ul class="info__tag">
                    <li class="info__tag-item">{{ $restaurant->getArea()}}</li>
                    <li class="info__tag-item">{{ $restaurant->getCategory() }}</li>
                </ul>
                <p class="info__description">{!! nl2br(e($restaurant->description)) !!}</p>
                
                @auth
                @if(empty($my_review))
                <div class="review-form__wrap">
                    <form action="/review" class="review-form" method="get">
                        <input type="hidden" name="user_id" class="review-form__hidden" value="{{ Auth::user()->id }}">
                        <input type="hidden" name="restaurant_id" class="review-form__hidden" value="{{ $restaurant->id }}">
                        <div class="review-form__button-wrap">
                            <button class="review-form__button" type="submit">口コミを投稿する</button>
                        </div>
                    </form>
                </div>
                @endif
                @endauth

                @if($restaurant->getReviews())
                <div class="all-review">
                    <a href="/review-archive/{{ $restaurant->id }}" class="all-review__button">全ての口コミ情報</a>
                    </form>
                </div>
                @endif

                @auth
                @if(!empty($my_review))
                <div class="my-review">
                    <div class="my-review__form-wrap">
                        <div class="edit-review">
                            <form action="/edit-review" method="get">
                                <input type="hidden" name="id" class="remove-review__hidden" value="{{ $my_review->id }}">
                                <button class="edit-review__button" type="submit">口コミを編集</button>
                            </form>
                        </div>
                        <div class="remove-review">
                            <form action="/remove-review" method="post">
                                @csrf
                                <input type="hidden" name="id" class="remove-review__hidden" value="{{ $my_review->id }}">
                                <button class="remove-review__button" type="submit">口コミを削除</button>
                            </form>
                        </div>
                    </div>
                    <div class="my-review__contents">
                        <div class="my-review__stars-wrap">
                            @for($i = 1; $i <= 5; $i++)
                            @if($my_review->stars >= $i)
                            <span class="my-review__stars my-review__stars-on">★</span>
                            @else
                            <span class="my-review__stars my-review__stars-off">★</span>
                            @endif
                            @endfor
                        </div>
                        <p class="my-review__comment">{!! nl2br(e($my_review->comment)) !!}</p>
                        @if($my_review->photo)
                        <div class="my-review__photo-wrap">
                            <img src="{{ $my_review->photo }}" alt="" class="my-review__photo">
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                @endauth
            </div>
            <div class="reservation">
                <div class="reservation-form__wrap">
                    <form action="/reservation" method="post" class="reservation-form" novalidate>
                        @csrf
                        <div class="reservation-form__contents">
                            <h2 class="reservation__title">予約</h2>
                            <div class="reservation-form__item-wrap">
                                <div class="reservation-form__item">
                                    <input type="date" name="date" class="reservation-form__input" value="{{ old('date') }}" min="{{ now()->addDay()->format('Y-m-d') }}">
                                    <div class="reservation-form__error">
                                        @error('date')
                                        {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="reservation-form__item">
                                    <div class="reservation-form__select-wrap">
                                        <select name="time" class="reservation-form__select">
                                            @for($h = 9; $h <= 21; $h++)
                                                @for($m = 0; $m < 60; $m += 30)
                                                    @php
                                                        $time = sprintf('%02d:%02d', $h, $m);
                                                    @endphp
                                                    <option value="{{ $time }}" @if(old('time') == $time) selected @endif>{{ $time }}</option>
                                                @endfor
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="reservation-form__error">
                                        @error('time')
                                        {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="reservation-form__item">
                                    <div class="reservation-form__select-wrap">
                                        <select name="number" class="reservation-form__select">
                                            @for($i = 1; $i <= 10; $i++)
                                                <option value="{{ $i }}" @if(old('number') == $i) selected @endif>{{ $i . '人' }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="reservation-form__error">
                                        @error('number')
                                        {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <input type="hidden" name="restaurant_id" class="reservation-form__hidden" value="{{ $restaurant->id }}">
                                <input type="hidden" name="user_id" class="reservation-form__hidden" value="{{ Auth::id() }}">
                            </div>
                            <div class="reservation-summary">
                                <table class="reservation-summary__table">
                                    <tr class="reservation-summary__table-row">
                                        <th class="reservation-summary__table-heading">Shop</th>
                                        <td class="reservation-summary__table-data">{{ $restaurant->name }}</td>
                                    </tr>
                                    <tr class="reservation-summary__table-row">
                                        <th class="reservation-summary__table-heading">Date</th>
                                        <td class="reservation-summary__table-data" id="date"></td>
                                    </tr>
                                    <tr class="reservation-summary__table-row">
                                        <th class="reservation-summary__table-heading">Time</th>
                                        <td class="reservation-summary__table-data" id="time"></td>
                                    </tr>
                                    <tr class="reservation-summary__table-row">
                                        <th class="reservation-summary__table-heading">Number</th>
                                        <td class="reservation-summary__table-data" id="number"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <button class="reservation-form__button" type="submit">予約する</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

