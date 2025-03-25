<?php

namespace App\Http\Api\External\V1\Requests\Auth;

use App\Http\Api\External\V1\Requests\Request;
use Illuminate\Validation\Rule;

/**
 * Class EmailCheckRequest
 *
 * @package App\Http\Api\External\V1\Requests\Auth
 */
class EmailCheckRequest extends Request
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|max:255',
        ];
    }
}
