<?php

namespace App\Http\Api\External\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AbstractRequest
 *
 * @package App\Http\Api\External\V1\Requests
 */
abstract class Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
}
