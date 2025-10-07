<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
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
            'img_url' => [
                Rule::requiredIf(function () {
                    // 新しい画像がなく、かつ一時保存された画像もない場合にのみ必須
                    return !$this->has('temp_image_path') && !$this->user()->profile?->img_url;
                }),
                'nullable', 'image', 'mimes:jpeg,png'
            ],
            'name' => ['required', 'string', 'max:20'],
            'postcode' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address' => ['required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'img_url.image' => 'プロフィール画像は画像ファイルである必要があります',
            'img_url.mimes' => 'プロフィール画像はjpegまたはpng形式でアップロードしてください',
            'name.required' => 'ユーザー名を入力してください',
            'name.string' => 'ユーザー名を文字列で入力してください',
            'name.max' => 'ユーザー名は20文字以内で入力してください',
            'postcode.required' => '郵便番号を入力してください',
            'postcode.regex' => '郵便番号はハイフンありの8文字で入力してください',
            'address.required' => '住所を入力してください',
            'address.string' => '住所を文字列で入力してください',
            'address.max' => '住所は255文字以内で入力してください',
            'building.string' => '建物名を文字列で入力してください',
            'building.max' => '建物名は255文字以内で入力してください',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->hasFile('img_url') && $this->file('img_url')->isValid()) {
            $path = $this->file('img_url')->store('temp_profile_previews', 'public');
            session()->flash('image_preview_url', Storage::url($path));
            session()->flash('temp_image_path', $path);
        }

        parent::failedValidation($validator);
    }
}