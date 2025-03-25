<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Auth\VerificationCode\VerificationCodeManager;
use App\Http\Api\External\V1\Requests\SendVerificationCodeRequest;
use App\Http\Middleware\VerificationCode\LimitVerificationCodeSending;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VerificationCodeController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class VerificationCodeController extends Controller
{
    public function __construct(
        private VerificationCodeManager $verificationCodeManager,
    ) {
        $this->middleware(
            LimitVerificationCodeSending::class . ':phone,case'
        )->only('send');
    }

    /**
     * @param SendVerificationCodeRequest $request
     *
     * @return Response
     */
    public function send(SendVerificationCodeRequest $request): Response
    {
        $this->verificationCodeManager->send(
            $request->input('case'),
            $request->input('phone')
        );

        return $this->empty();
    }
}
