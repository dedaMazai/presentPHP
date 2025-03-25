<?php

namespace App\Models\User;

use App\Models\Document\Document;
use App\Models\Sales\Customer\Customer;

/**
 * Class UserSignInfo
 *
 * @package App\Models\User
 */
class UserSignInfo
{
    public function __construct(
        private string $fullName,
        private $reissueGuideUrl,
        private ?Customer $signInfo,
        private ?Document $signStatementDocument,
    ) {
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * @return string|null
     */
    public function getReissueGuideUrl(): ?string
    {
        return $this->reissueGuideUrl;
    }

    /**
     * @return Customer|null
     */
    public function getSignInfo(): ?Customer
    {
        return $this->signInfo;
    }

    /**
     * @return Document|null
     */
    public function getSignStatementDocument(): ?Document
    {
        return $this->signStatementDocument;
    }
}
