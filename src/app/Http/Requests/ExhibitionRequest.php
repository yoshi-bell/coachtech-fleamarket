<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'img_url' => ['required', 'image', 'mimes:jpeg,png'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'condition_id' => ['required', 'integer', 'exists:conditions,id'],
            'price' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください',
            'name.string' => '商品名を文字列で入力してください',
            'name.max' => '商品名は255文字以内で入力してください',
            'description.required' => '商品説明を入力してください',
            'description.string' => '商品説明を文字列で入力してください',
            'description.max' => '商品説明は255文字以内で入力してください',
            'img_url.required' => '商品画像をアップロードしてください',
            'img_url.image' => '商品画像は画像ファイルである必要があります',
            'img_url.mimes' => '商品画像はjpegまたはpng形式でアップロードしてください',

            'category_id.required' => '商品のカテゴリーを選択してください',
            'category_id.integer' => '選択されたカテゴリーが不正です。',
            'category_id.exists' => '選択されたカテゴリーは無効です。',

            'condition_id.required' => '商品の状態を選択してください',
            'condition_id.integer' => '選択された商品の状態が不正です。',
            'condition_id.exists' => '選択された商品の状態は無効です。',

            'price.required' => '商品価格を入力してください',
            'price.integer' => '商品価格は数値で入力してください',
            'price.min' => '商品価格は0円以上で入力してください',
        ];
    }
}