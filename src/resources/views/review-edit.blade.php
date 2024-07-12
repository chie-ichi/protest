@extends('layouts.app')

@section('title')
<title>Edit Review | Rese</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/review-edit.css') }}">
@endsection

@section('content')
<div class="review">
    <div class="inner review__inner">
        <div class="review__contents-wrap">
            <div class="review__form">
                <form action="/update-review" class="review-form" method="post" enctype="multipart/form-data" >
                    @csrf
                    <div class="review__form-upper">
                        <div class="review__head">
                            <h1 class="review__title">このお店の評価を編集しますか？</h1>
                            <div class="restaurant-card">
                                <div class="restaurant-card__photo">
                                    <img src="{{ $restaurant->photo }}" alt="{{ $restaurant->name }}">
                                </div>
                                <div class="restaurant-card__contents">
                                    <p class="restaurant-card__name">{{ $restaurant->name }}</p>
                                    <div class="restaurant-card__tag">
                                        <p class="restaurant-card__area">{{ $restaurant->getArea() }}</p>
                                        <p class="restaurant-card__category">{{ $restaurant->getCategory() }}</p>
                                    </div>
                                    <div class="restaurant-card__info">
                                        <a href="/detail/{{ $restaurant->id }}" class="restaurant-card__link">詳しくみる</a>
                                        @php
                                            $user_id = Auth::id();
                                        @endphp
                                        <div class="restaurant-card__favorite">
                                            @if($restaurant->isFavorite($user_id))
                                            <img src="{{ asset('img/icon-favorite-on.svg') }}" alt="お気に入りON" class="restaurant-card__favorite-icon">
                                            @else
                                            <img src="{{ asset('img/icon-favorite-off.svg') }}" alt="お気に入りOFF" class="restaurant-card__favorite-icon">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="review__body">
                            <input type="hidden" name="id" class="review-form__hidden" value="{{ $review->id }}">
                            <input type="hidden" name="user_id" class="review-form__hidden" value="{{ $review->user_id }}">
                            <input type="hidden" name="restaurant_id" class="review-form__hidden" value="{{ $restaurant->id }}">
                            <div class="review-form__item">
                                <h2 class="review-form__item-title">体験を評価してください</h2>
                                <div class="review-form__stars">
                                    <input id="star05" type="radio" name="stars" value="5"  class="review-form__stars-radio" @if($review->stars == '5') checked @endif><label for="star05"  class="review-form__stars-label">★</label>
                                    <input id="star04" type="radio" name="stars" value="4" class="review-form__stars-radio" @if($review->stars == '4') checked @endif><label for="star04" class="review-form__stars-label">★</label>
                                    <input id="star03" type="radio" name="stars" value="3" class="review-form__stars-radio" @if($review->stars == '3') checked @endif><label for="star03" class="review-form__stars-label">★</label>
                                    <input id="star02" type="radio" name="stars" value="2" class="review-form__stars-radio" @if($review->stars == '2') checked @endif><label for="star02" class="review-form__stars-label">★</label>
                                    <input id="star01" type="radio" name="stars" value="1" class="review-form__stars-radio" @if($review->stars == '1') checked @endif><label for="star01" class="review-form__stars-label">★</label>
                                </div>
                                <div class="review-form__error">
                                    @error('stars')
                                    {{ $message }}
                                    @enderror
                                </div>
                            </div>
                            <div class="review-form__item">
                                <h2 class="review-form__item-title">口コミを投稿</h2>
                                <textarea name="comment" class="review-form__textarea" placeholder="カジュアルな夜のお出かけにおすすめのスポット" id="textarea-count-target" maxlength="400">{{ $review->comment }}</textarea>
                                <div class="review-form__error">
                                    @error('comment')
                                    {{ $message }}
                                    @enderror
                                </div>
                                <p class="review-form__textarea-count"><span id="textarea-count-result">0</span>/400（最高文字数）</p>
                            </div>
                            <div class="review-form__item">
                                <h2 class="review-form__item-title">画像の追加</h2>
                                <div class="upload-area" id="uploadArea">
                                    <div class="upload-area__txt-wrap">
                                        <p class="upload-area__txt">クリックして写真を編集</p>
                                        <p class="upload-area__txt--small">またはドラッグアンドドロップ</p>
                                        @if($review->photo)
                                        <img src="{{ asset($review->photo) }}" alt="{{ $restaurant->name }}" class="upload-area__preview photo-current">
                                        @endif
                                        <img id="preview" src="" alt="プレビュー画像" style="display: none;" class="upload-area__preview photo-preview">
                                    </div>
                                    <input type="file" id="fileInput" name="photo_file" class="review-form__file">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="review-form__button-wrap">
                        <button class="review-form__button" type="submit">口コミを更新</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

