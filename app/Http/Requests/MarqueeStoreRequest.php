<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarqueeStoreRequest extends FormRequest
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
            'display'     => ['required'],
            'forever'     => ['required', 'in:1,2'],
            'title'       => ['required', 'max:20'],
            'description' => ['required', 'max:500'],
            'start_at'    => ['bail', 'required', 'date_format:Y-m-d H:i:s', 'after_or_equal:' . date("Y-m-d H:i:s", strtotime('-2 minute'))],
            'end_at'      => ['bail', 'required_if:forever,==,2', 'date_format:Y-m-d H:i:s', 'after:start_at']
        ];
    }
}
