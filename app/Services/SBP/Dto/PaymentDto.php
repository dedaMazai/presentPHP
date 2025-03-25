<?php

namespace App\Services\SBP\Dto;

use App\Models\PaymentMethodType;
use PHPUnit\Runner\BaseTestRunner;

/**
 * Class CreatePaymentDto
 *
 * @package App\Services\Payment\Dto
 */
class PaymentDto
{
    /**
     * @param PaymentMethodType $type
     * @param int               $amount
     * @param string            $returnUrl
     * @param string            $failUrl
     * @param string            $firstName
     * @param string            $lastName
     * @param string            $middleName
     * @param string            $email
     */
    public function __construct(
        public $AMOUNT,
        public $CURRENCY,
        public $ORDER,
        public $DESC,
        public $TERMINAL,
        public $TRTYPE,
        public $MERCH_NAME,
        public $SBP_MERCHANT,
        public $EMAIL,
        public $TIMESTAMP,
        public $NONCE,
        public $P_SIGN,
        public $BACKREF,
        public $NOTIFY_URL,
        public $CARDHOLDER_NOTIFY,
        public $MERCHANT_NOTIFY,
        public $MERCHANT_NOTIFY_EMAIL,
        public $SBP_ID,
        public $SBP_ACCOUNT_NUMBER,
    ) {
    }
}
