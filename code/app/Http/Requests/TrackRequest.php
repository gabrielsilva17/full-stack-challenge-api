<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class TrackRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'isrcs'   => 'required|array|min:1',
            'isrcs.*' => 'required|string|size:12',
        ];
    }
}
