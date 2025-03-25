<?php

namespace App\Http\Api\External\V1\Requests\Payment;

use App\Http\Api\External\V1\Requests\Request;
use Illuminate\Validation\Rule;

/**
 * Class PaymentCallbackRequest
 *
 * @property int|null $AMOUNT
 * @property string $CURRENCY
 * @property string $ORDER
 * @property string $DESC
 * @property string $TERMINAL
 * @property string $TRTYPE
 * @property string $MERCH_NAME
 * @property string $MERCHANT
 * @property string $EMAIL
 * @property string $TIMESTAMP
 * @property string $NONCE
 * @property string $RESULT
 * @property string $RC
 * @property string $AUTHCODE
 * @property string $RRN
 * @property string $INT_REF
 * @property string $P_SIGN
 * @property string $NAME
 * @property string $CARD
 * @package App\Http\Api\External\V1\Requests
 */
class PSBPaymentCallbackRequest extends Request
{
    public function rules(): array
    {
        return [
            'AMOUNT' => 'integer',
            'CURRENCY' => 'required|string',
            'ORDER' => 'required|string',
            'DESC' => 'required|string',
            'TERMINAL' => 'required|string',
            'TRTYPE' => 'required|string',
            'MERCH_NAME' => 'required|string',
            'MERCHANT' => 'required|string',
            'EMAIL' => 'required|string',
            'TIMESTAMP' => 'required|string',
            'NONCE' => 'required|string',
            'RESULT' => 'required|string',
            'RC' => 'required|string',
            'RCTEXT' => 'required|string',
            'AUTHCODE' => 'required|string',
            'RRN' => 'required|string',
            'INT_REF' => 'required|string',
            'P_SIGN' => 'required|string',
            'NAME' => 'required|string',
            'CARD' => 'required|string',
        ];
    }
}
