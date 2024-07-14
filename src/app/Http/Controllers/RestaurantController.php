<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Restaurant;
use App\Models\Area;
use App\Models\Category;
use App\Models\Review;
use App\Http\Requests\RestaurantRequest;
use App\Http\Requests\CsvRequest;

class RestaurantController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::with('area')->get();
        $areas = Area::all();
        $categories = Category::all();

        $restaurants = $restaurants->shuffle();

        return view('index', compact('restaurants', 'areas', 'categories'));
    }

    public function search(Request $request)
    {
        $restaurants = Restaurant::with('area', 'category')
            ->AreaSearch($request->area_id)
            ->CategorySearch($request->category_id)
            ->KeywordSearch($request->keyword)
            ->get();
        $areas = Area::all();
        $categories = Category::all();

        switch($request->sort){
            case "random":
                $restaurants = $restaurants->shuffle();
                break;
            case "rating-high":
                $restaurants = $restaurants->sortByDesc(function ($restaurant) {
                    $reviewCount = $restaurant->getReviews()->count();
                    if($reviewCount > 0) {
                        //口コミが存在する場合は評価の平均値でソート
                        $reviewAverage = $restaurant->getReviews()->sum('stars') / $reviewCount;
                        return [$reviewAverage, $reviewCount];
                    } else {
                        //口コミが存在しない場合はソートの最後
                        return [PHP_INT_MIN, 0];
                    }
                });
                break;
            case "rating-low":
                $restaurants = $restaurants->sortBy(function ($restaurant) {
                    $reviewCount = $restaurant->getReviews()->count();
                    if($reviewCount > 0) {
                        //口コミが存在する場合は評価の平均値でソート
                        $reviewAverage = $restaurant->getReviews()->sum('stars') / $reviewCount;
                        return [$reviewAverage, $reviewCount];
                    } else {
                        //口コミが存在しない場合はソートの最後
                        return [PHP_FLOAT_MAX, 0];
                    }
                });
                break;
            default:
                break;
        }

        return view('index', compact('restaurants', 'areas', 'categories'));
    }

    public function detail($restaurant_id)
    {
        $restaurant = Restaurant::find($restaurant_id);
        $user_id = Auth::id();
        $myReview = null;

        if($user_id) {
            $myReview = Review::where('user_id', $user_id)
                        ->where('restaurant_id', $restaurant_id)
                        ->first();
        }

        return view('detail', compact('restaurant', 'myReview'));
    }

    public function add(RestaurantRequest $request)
    {
        try {
            $image = $request->file('photo_file');
            $path = $image->store('public/img/upload');
            $publicPath = str_replace('public/', '/storage/', $path);

            //店舗代表者情報を登録
            Restaurant::create([
                'owner_id' => $request['owner_id'],
                'name' => $request['name'],
                'area_id' => $request['area_id'],
                'category_id' => $request['category_id'],
                'photo' => $publicPath,
                'description' => $request['description'],
            ]);
            return redirect('/owner')->with('flashSuccess', '飲食店情報の登録が完了しました');
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            return redirect('/owner')->with('flashError', '飲食店情報登録時にエラーが発生しました: ' . $errorMessage);
        }
    }

    public function update(Request $request)
    {
        try {
            $data = [];

            if(!empty($request->input('name'))) {
                $data['name'] = $request->input('name');
            }

            if(!empty($request->input('area_id'))) {
                $data['area_id'] = $request->input('area_id');
            }

            if(!empty($request->input('category_id'))) {
                $data['category_id'] = $request->input('category_id');
            }

            if(!empty($request->input('description'))) {
                $data['description'] = $request->input('description');
            }

            if($request->file('photo_file')) {
                $image = $request->file('photo_file');
                $path = $image->store('public/img/upload');
                $publicPath = str_replace('public/', '/storage/', $path);
                $data['photo'] = $publicPath;
            }

            Restaurant::find($request->id)->update($data);

            return redirect('/owner')->with('flashSuccess', '飲食店情報の更新が完了しました');
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            return redirect('/owner')->with('flashError', '飲食店情報更新時にエラーが発生しました: ' . $errorMessage);
        }
    }

    public function uploadCsv(CsvRequest $request)
    {
        try{
            if ($request->hasFile('csv')) {
                if ($request->csv->getClientOriginalExtension() !== "csv") {
                    return redirect('/admin')->with('flashError', '不適切な拡張子です。');
                }

                //ファイルの保存
                $filename = $request->csv->getClientOriginalName();
                $request->csv->storeAs('public/csv', $filename);
            } else {
                return redirect('/admin')->with('flashError', 'CSVファイルの取得に失敗しました。');
            }

            //保存したCSVファイルの取得
            $csv = Storage::disk('local')->get("public/csv/{$filename}");
            $csv = str_replace(array("\r\n", "\r"), "\n", $csv);
            $uploadedData = collect(explode("\n", $csv));

            //CSVファイルのヘッダーの検証
            $header = collect(['name', 'area_name', 'category_name', 'description', 'photo_url','owner_id']);
            $uploadedHeader = collect(explode(",", $uploadedData->shift()));
            $uploadedHeader[0] = preg_replace('/^\xEF\xBB\xBF/', '', $uploadedHeader[0]);
            if ($header->diff($uploadedHeader)->isNotEmpty() || $uploadedHeader->diff($header)->isNotEmpty()) {
                return redirect('/admin')->with('flashError', 'CSVファイルのヘッダーが正しくありません');
            }

            $restaurants = $uploadedData->map(fn($oneRecord) => $header->combine(collect(explode(",", $oneRecord))));

            foreach ($restaurants as $restaurant) {
                $areaId = Area::getIdByName($restaurant['area_name']);
                //13:東京都、27:大阪府、40:福岡県
                if (!$areaId || !in_array($areaId, [13, 27, 40])) {
                    return redirect('/admin')->with('flashError', 'エリア名が正しくありません: ' . $restaurant['area_name']);
                }

                $categoryId = Category::getIdByName($restaurant['category_name']);
                if (!$categoryId) {
                    return redirect('/admin')->with('flashError', 'カテゴリ名が正しくありません: ' . $restaurant['area_name']);
                }

                $photoUrl = $restaurant['photo_url'];
                $extension = pathinfo($photoUrl, PATHINFO_EXTENSION);

                //拡張子のチェック
                if (!in_array(strtolower($extension), ['jpg', 'jpeg', 'png'])) {
                    return redirect('/admin')->with('flashError', 'jpg/jpeg/pngのいずれかの形式のファイルを指定してください: ' . $photoUrl);
                }

                // 画像を保存
                $photoContents = Http::get($photoUrl)->body();
                $photoName = Str::random(40) . '.' . $extension; // ランダムなファイル名を生成
                $photoPath = 'public/img/upload/' . $photoName;
                Storage::put($photoPath, $photoContents);
                $publicPhotoPath = str_replace('public/', '/storage/', $photoPath);

                Restaurant::create([
                    'name' => $restaurant['name'],
                    'area_id' => $areaId,
                    'category_id' => $categoryId,
                    'photo' => $publicPhotoPath,
                    'description' => $restaurant['description'],
                    'owner_id' => $restaurant['owner_id'],
                ]);
            }

            return redirect('/admin')->with('flashSuccess', 'CSVファイルのアップロードが完了しました');
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            return redirect('/admin')->with('flashError', 'CSVファイルのアップロード時にエラーが発生しました: ' . $errorMessage);
        }
    }
}
