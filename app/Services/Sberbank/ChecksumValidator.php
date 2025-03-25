<?php

namespace App\Services\Sberbank;

use App\Services\Payment\Dto\ValidatePaymentChecksumDto;
use RuntimeException;

/**
 * Class ChecksumValidator
 *
 * @package App\Services\Sberbank
 */
class ChecksumValidator
{
    public function __construct(
        private string $sberbankCertsPath,
        private string $salesSellerId,
    ) {
    }

    public function validate(ValidatePaymentChecksumDto $dto): bool
    {
        $certFileName = $dto->transactionLog?->account_service_seller_id ?? $this->salesSellerId;
        $certFilePath = $this->sberbankCertsPath . '/' . $certFileName . '.cer';
        if (file_exists($certFilePath)) {
            $cert = file_get_contents($certFilePath);

            $publicKey = openssl_pkey_get_details(openssl_get_publickey($cert))['key'] ?? null;
            if (!$publicKey) {
                throw new RuntimeException('Cert for seller is not valid.');
            }
        } else {
            throw new RuntimeException('Cert for seller is not exist.');
        }

        $binarySignature = hex2bin(strtolower($dto->checksum));
        $isVerify = openssl_verify(
            $this->getCheckString($dto->checkParams),
            $binarySignature,
            $publicKey,
            OPENSSL_ALGO_SHA512,
        );

        return $isVerify == 1;
    }

    private function getCheckString(array $payload): string
    {
        unset($payload['checksum']);
        unset($payload['sign_alias']);

        ksort($payload);

        $checkString = '';
        foreach ($payload as $key => $value) {
            $checkString .= $key . ';' . $value . ';';
        }

        return $checkString;
    }
}
