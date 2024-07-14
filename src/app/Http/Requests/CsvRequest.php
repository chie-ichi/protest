<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CsvRequest extends FormRequest
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
            'csv' => ['required', 'file', 'mimes:csv,txt', 'mimetypes:text/plain,text/csv', 'max:1024'],
        ];
    }

    public function messages()
    {
        return [
            'csv.required' => 'CSVファイルの指定は必須です。',
            'csv.file' => 'アップロードされたファイルが不正です。',
            'csv.mimes' => 'CSV形式のファイルを指定してください。',
            'csv.mimetypes' => 'CSV形式のファイルを指定してください。',
            'csv.max' => '1MB以下のファイルを指定してください。',
        ];
    }
}
