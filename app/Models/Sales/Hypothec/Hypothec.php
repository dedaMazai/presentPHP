<?php

namespace App\Models\Sales\Hypothec;

use App\Models\Sales\MortgageApproval\MortgageApproval;

/**
 * Class Hypothec
 *
 * @package App\Models\Sales\Hypothec
 */
class Hypothec
{
    public function __construct(
        private ?bool $isMortgageOnlineAvailable,
        private ?string $typeHypothecDemand,
        private ?bool $isManualApprove,
        private ?bool $isDigitalApprove,
        private ?array $approvalList,
        private ?MortgageApproval $confirmBankHypothec,
        private ?bool $isBorrowersAvailible,
        private ?array $borrowers,
        private ?string $mortageOnlineInfo,
        private ?string $infoBorrower,
        private ?string $escrowBankName,
        private ?string $bankNameApproveManual,
    ) {
    }

    /**
     * @return bool|null
     */
    public function getIsMortgageOnlineAvailable(): ?bool
    {
        return $this->isMortgageOnlineAvailable;
    }

    /**
     * @return string|null
     */
    public function getTypeHypothecDemand(): ?string
    {
        return $this->typeHypothecDemand;
    }

    /**
     * @return bool|null
     */
    public function getIsManualApprove(): ?bool
    {
        return $this->isManualApprove;
    }

    /**
     * @return bool|null
     */
    public function getIsDigitalApprove(): ?bool
    {
        return $this->isDigitalApprove;
    }

    /**
     * @return array|null
     */
    public function getApprovalList(): ?array
    {
        return $this->approvalList;
    }

    /**
     * @return MortgageApproval|null
     */
    public function getConfirmBankHypothec(): ?MortgageApproval
    {
        return $this->confirmBankHypothec;
    }

    /**
     * @return bool|null
     */
    public function getIsBorrowersAvailible(): ?bool
    {
        return $this->isBorrowersAvailible;
    }

    /**
     * @return array|null
     */
    public function getBorrowers(): ?array
    {
        return $this->borrowers;
    }

    /**
     * @return string|null
     */
    public function getMortageOnlineInfo(): ?string
    {
        return $this->mortageOnlineInfo;
    }

    /**
     * @return string|null
     */
    public function getEscrowBankName(): ?string
    {
        return $this->escrowBankName;
    }

    /**
     * @return string|null
     */
    public function getInfoBorrower(): ?string
    {
        return $this->infoBorrower;
    }

    /**
     * @return string|null
     */
    public function getBankNameApproveManual(): ?string
    {
        return $this->bankNameApproveManual;
    }
}
