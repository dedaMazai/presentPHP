<?php

namespace App\Models\Sales;

use App\Models\Sales\Demand\Demand;

/**
 * Class StepMapper
 *
 * @package App\Models\Sales
 */
class StepMapper
{
    const STATUS_ACTIVE = 'active';
    const STATUS_DONE = 'done';
    const STATUS_BLOCKED = 'blocked';
    const STEP_TERMS = 'terms';
    const STEP_CONTRACT = 'contract';
    const STEP_PREPARE_SIGN = 'prepare_sign';
    const STEP_SIGN = 'sign';

    public static function getForDemand(Demand $demand): array
    {
        $dealSteps = explode('.', $demand->getDeal()->current_step);
        if (!isset($dealSteps[0])) {
            $dealSteps[0] = self::STEP_TERMS;
        }

        $steps = [];
        if ($dealSteps[0] == self::STEP_TERMS) {
            $statusTerms = self::STATUS_ACTIVE;
        } else {
            $statusTerms = self::STATUS_DONE;
        }
        $steps[self::STEP_TERMS] = [
            'name' => self::STEP_TERMS,
            'status' => $statusTerms,
            'steps' => [
                [
                    'name' => 'payment',
                    'status' => $statusTerms,
                ],
                [
                    'name' => 'owners',
                    'status' => $statusTerms,
                ],
            ],
        ];
        if ($demand->isFinishingAvailable()) {
            $steps[self::STEP_TERMS]['steps'][] = [
                'name' => 'finishing',
                'status' => $statusTerms,
            ];
        }

        $statusInfoContract = '';
        if ($dealSteps[0] == self::STEP_CONTRACT && $demand->getContract()) {
            $statusContract = self::STATUS_ACTIVE;
        } elseif ($dealSteps[0] == self::STEP_CONTRACT && !$demand->getContract()) {
            $statusContract = self::STATUS_BLOCKED;
            $statusInfoContract = 'preparing_documents';
        } elseif ($statusTerms == self::STATUS_DONE) {
            $statusContract = self::STATUS_DONE;
        } else {
            $statusContract = self::STATUS_BLOCKED;
        }
        $steps[self::STEP_CONTRACT] = [
            'name' => self::STEP_CONTRACT,
            'status' => $statusContract,
            'status_info' => $statusInfoContract,
        ];

        if ($dealSteps[0] == self::STEP_PREPARE_SIGN) {
            $statusPrepareSign = self::STATUS_ACTIVE;
            $statusPrepareSignUploadDocuments = self::STATUS_ACTIVE;
            $statusPrepareSignSetupSign = self::STATUS_ACTIVE;
            $statusPrepareSignPrepareBankAccounts = self::STATUS_BLOCKED;
        } else {
            $statusPrepareSign = self::STATUS_BLOCKED;
            $statusPrepareSignUploadDocuments = self::STATUS_BLOCKED;
            $statusPrepareSignSetupSign = self::STATUS_BLOCKED;
            $statusPrepareSignPrepareBankAccounts = self::STATUS_BLOCKED;
        }
        $steps[self::STEP_PREPARE_SIGN] = [
            'name' => self::STEP_PREPARE_SIGN,
            'status' => $statusPrepareSign,
            'steps' => [
                [
                    'type' => 'upload_documents',
                    'status' => $statusPrepareSignUploadDocuments,
                    'progress' => [
                        'current' => 0,
                        'total' => 0,
                    ],
                ],
                [
                    'type' => 'setup_sign',
                    'status' => $statusPrepareSignSetupSign,
                ],
                [
                    'type' => 'prepare_bank_accounts',
                    'status' => $statusPrepareSignPrepareBankAccounts,
                    'depend' => [
                        'upload_documents',
                    ],
                ],
            ],
        ];

        $steps[self::STEP_SIGN] = [
            'name' => self::STEP_SIGN,
            'status' => 'blocked',
            'depend' => [
                'setup_sign',
            ],
        ];

        return $steps;
    }

    public static function getActiveSteps(): array
    {
        return [
            self::STEP_TERMS,
            self::STEP_CONTRACT,
            self::STEP_PREPARE_SIGN,
            self::STEP_SIGN,
        ];
    }
}
