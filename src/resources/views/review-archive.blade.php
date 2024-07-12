@extends('layouts.app')

@section('title')
<title>{{ $restaurant->name }}の口コミ一覧 | Rese</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/review-archive.css') }}">
@endsection

@section('content')
<div class="reviews">
    <div class="inner reviews__inner">
        <h1 class="reviews__title">{{ $restaurant->name }}の口コミ一覧</h1>
        <div class="reviews-list__wrapper">
            @if($reviews->isEmpty())
            <p class="reviews-list__not-found">口コミがまだありません</p>
            @else
            <ul class="reviews-list">
            @foreach($reviews as $review)
            <li class="reviews-list__item">
                <div class="reviews-list__user">
                    <img class="reviews-list__user-icon" alt="user" src="{{ asset('img/icon-user.svg') }}">
                    <p class="reviews-list__user-name">{{ $review->getUser() }}</p>
                </div>
                <div class="reviews-list__stars-wrap">
                    @for($i = 1; $i <= 5; $i++)
                    @if($review->stars >= $i)
                    <span class="reviews-list__stars reviews-list__stars-on">★</span>
                    @else
                    <span class="reviews-list__stars reviews-list__stars-off">★</span>
                    @endif
                    @endfor
                </div>
                <p class="reviews-list__comment">{!! nl2br(e($review->comment)) !!}</p>
                @if($review->photo)
                <div class="reviews-list__photo-wrap">
                    <img src="{{ $review->photo }}" alt="" class="reviews-list__photo">
                </div>
                @endif
                @if($review->user_id === Auth::id())
                <div class="delete-form__wrap">
                    <form action="/remove-review" class="delete-form" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{ $review->id }}" class="delete-form__hidden">
                        <button class="delete-form__button">この口コミを削除する</button>
                    </form>
                </div>
                @elseif(Auth::guard('administrators')->check())
                <div class="delete-form__wrap">
                    <form action="/admin/remove-review" class="delete-form" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{ $review->id }}" class="delete-form__hidden">
                        <button class="delete-form__button">この口コミを削除する</button>
                    </form>
                </div>
                @endif
            </li>
            @endforeach
            </ul>
            @endif
        </div>

        <div class="back-button">
            <a href="/detail/{{ $restaurant->id }}" class="back-button__link">戻る</a>
        </div>
    </div>
</div>
@endsection

