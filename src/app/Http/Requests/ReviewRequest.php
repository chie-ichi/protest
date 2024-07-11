<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'stars' => ['required'],
            'comment' => ['required','max:400'],
            'photo_file' => ['image','mimes:jpeg,png,jpg','max:512'],
        ];
    }

    public function messages()
    {
        return [
            'stars.required' => '評価は必須です。',
            'comment.required' => '口コミは必須です。',
            'comment.max' => '口コミは400文字以内で入力してください。',
            'photo_file.image' => '画像ファイルを指定してください。',
            'photo_file.mimes' => 'JPEG、PNG、JPGのいずれかの形式のファイルを指定してください。',
            'photo_file.max' => '500KB以下のファイルを指定してください。',
        ];
    }
}
