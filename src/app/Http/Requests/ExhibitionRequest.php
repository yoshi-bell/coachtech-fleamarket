<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
            'img_url' => [Rule::requiredIf(empty($this->input('temp_image_path'))), 'image', 'mimes:jpeg,png'],
            'category_ids'   => ['required', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
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

            'category_ids.required' => '商品のカテゴリーを1つ以上選択してください',
            'category_ids.array' => 'カテゴリーの形式が不正です。',
            'category_ids.*.integer' => '選択されたカテゴリーが不正です。',
            'category_ids.*.exists' => '選択されたカテゴリーは無効です。',

            'condition_id.required' => '商品の状態を選択してください',
            'condition_id.integer' => '選択された商品の状態が不正です。',
            'condition_id.exists' => '選択された商品の状態は無効です。',

            'price.required' => '商品価格を入力してください',
            'price.integer' => '商品価格は数値で入力してください',
            'price.min' => '商品価格は0円以上で入力してください',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // バリデーション失敗時に画像がアップロードされていれば、一時保存する
        if ($this->hasFile('img_url') && $this->file('img_url')->isValid()) {
            $path = $this->file('img_url')->store('temp_previews', 'public');
            // 次のリクエストのために、画像のURLとサーバー上の一時パスの両方をセッションに保存
            session()->flash('image_preview_url', Storage::url($path));
            session()->flash('temp_image_path', $path);
        }

        // 親クラスのバリデーション失敗処理を呼び出す
        parent::failedValidation($validator);
    }
}
