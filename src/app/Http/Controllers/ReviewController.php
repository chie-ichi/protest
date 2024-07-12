<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;
use App\Models\Restaurant;
use App\Http\Requests\ReviewRequest;

class ReviewController extends Controller
{
    public function review(Request $request)
    {
        $user_id = $request->user_id;
        $restaurant = Restaurant::find($request->restaurant_id);
        return view('review', compact('user_id', 'restaurant'));
    }

    public function addReview(ReviewRequest $request)
    {
        try {
            $restaurant_id = $request->restaurant_id;
            $user_id = $request->user_id;
            $public_path = null;
            $image = $request->file('photo_file');

            $review = Review::where('user_id', $user_id)
                        ->where('restaurant_id', $restaurant_id)
                        ->first();

            if($review){
                return redirect("/detail/{$restaurant_id}")->with('flashError', '口コミ投稿済みのため処理できませんでした');
            }

            if($image) {
                $path = $image->store('public/img/upload');
                $public_path = str_replace('public/', '/storage/', $path);
            }

            Review::create([
                'user_id' => $user_id,
                'restaurant_id' => $restaurant_id,
                'stars' => $request->stars,
                'comment' => $request->comment,
                'photo' => $public_path,
            ]);
            return redirect("/detail/{$restaurant_id}")->with('flashSuccess', '口コミを投稿しました');
        } catch (\Throwable $th) {
            return redirect("/detail/{$restaurant_id}")->with('flashError', '口コミの投稿に失敗しました: ' . $th->getMessage());
        }
    }

    public function reviewArchive($restaurant_id)
    {
        $restaurant = Restaurant::find($restaurant_id);
        $reviews = Review::where('restaurant_id', $restaurant_id)
            ->get();

        return view('review-archive', compact('restaurant', 'reviews'));
    }

    public function removeReview(Request $request){
        $review = Review::find($request->id);
        $user_id = $review->user_id;

        if($user_id != Auth::id()) {
            return redirect()->back()->with('flashError', '他のユーザーの口コミは削除できません');
        }

        try {
            $review->delete();
            return redirect()->back()->with('flashSuccess', '口コミを削除しました');
        } catch (\Throwable $th) {
            return redirect()->back()->with('flashError', '口コミの削除に失敗しました: ' . $th->getMessage());
        }
    }

    public function editReview(Request $request)
    {
        $review = Review::find($request->id);
        $restaurant_id = $review->restaurant_id;
        $restaurant = Restaurant::find($restaurant_id);

        return view('review-edit', compact('restaurant', 'review'));
    }

    public function updateReview(ReviewRequest $request)
    {
        try {
            $user_id = $request->user_id;
            $restaurant_id = $request->restaurant_id;
            if($user_id != Auth::id()) {
                return redirect("/detail/{$restaurant_id}")->with('flashError', '他のユーザーの口コミは更新できません');
            }

            $public_path = null;
            $image = $request->file('photo_file');

            if($image) {
                $path = $image->store('public/img/upload');
                $public_path = str_replace('public/', '/storage/', $path);
            }

            $data = [];

            if(!empty($request->stars)) {
                $data['stars'] = $request->stars;
            }

            if(!empty($request->comment)) {
                $data['comment'] = $request->comment;
            }

            if($request->photo_file) {
                $image = $request->photo_file;
                $path = $image->store('public/img/upload');
                $public_path = str_replace('public/', '/storage/', $path);
                $data['photo'] = $public_path;
            }

            Review::find($request->id)->update($data);

            return redirect("/detail/{$restaurant_id}")->with('flashSuccess', '口コミを更新しました');
        } catch (\Throwable $th) {
            return redirect("/detail/{$restaurant_id}")->with('flashError', '口コミの更新に失敗しました: ' . $th->getMessage());
        }
    }

    public function removeReviewAdmin(Request $request){
        try {
            Review::find($request->id)->delete();
            return redirect()->back()->with('flashSuccess', '口コミを削除しました');
        } catch (\Throwable $th) {
            return redirect()->back()->with('flashError', '口コミの削除に失敗しました: ' . $th->getMessage());
        }
    }
}
