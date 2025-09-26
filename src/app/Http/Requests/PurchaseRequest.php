<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class PurchaseRequest extends FormRequest
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
            'payment_method' => ['required', 'in:1,2'],
        ];
    }

    public function messages()
    {
        return [
            'payment_method.required' => '支払い方法を選択してください',
            'payment_method.in' => '有効な支払い方法を選択してください。',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            $item_id = $this->route('item')->id;

            // Check for address in session or profile
            if (!session('shipping_address_' . $item_id) && (!$user->profile || !$user->profile->address)) {
                $validator->errors()->add(
                    'shipping_address',
                    '配送先住所が登録されていません。プロフィールページから登録してください。'
                );
            }
        });
    }
}