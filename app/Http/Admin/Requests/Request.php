<?php

namespace App\Http\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AbstractRequest
 *
 * @package App\Http\Admin\Requests
 */
abstract class Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
}
