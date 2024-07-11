<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

            $existing_review = Review::where('user_id', $user_id)
                        ->where('restaurant_id', $restaurant_id)
                        ->first();

            if($existing_review){
                return redirect("/detail/{$restaurant_id}")->with('flashError', 'レビュー済みのため投稿できませんでした');
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
            return redirect("/detail/{$restaurant_id}")->with('flashSuccess', 'レビューを投稿しました');
        } catch (\Throwable $th) {
            return redirect("/detail/{$restaurant_id}")->with('flashError', 'レビューの投稿に失敗しました: ' . $th->getMessage());
        }
    }
}
