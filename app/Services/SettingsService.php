<?php

namespace App\Services;

use App\Models\Settings;
use RuntimeException;

/**
 * Class SettingsService
 *
 * @package App\Services
 */
class SettingsService
{
    public function updateDocuments(string $offerUrl, string $consentUrl, string $confidant_url): void
    {
        $settings = $this->getSettings();
        $settings->offer_url = $offerUrl;
        $settings->consent_url = $consentUrl;
        $settings->confidant_url = $confidant_url;
        $settings->save();
    }

    public function updateBuilds(string $buildAndroidUrl, string $buildIosUrl): void
    {
        $settings = $this->getSettings();
        $settings->build_android_url = $buildAndroidUrl;
        $settings->build_ios_url = $buildIosUrl;
        $settings->save();
    }

    public function getClaimRootCategoryCrmId(): string
    {
        return $this->getSettings()->claim_root_category_crm_id ??
            throw new RuntimeException('CRM ID for root category not found.');
    }

    public function getClaimPassCarCrmServiceId(): string
    {
        return $this->getSettings()->claim_pass_car_crm_service_id ??
            throw new RuntimeException('CRM service ID for car pass claim not found.');
    }

    public function getClaimPassHumanCrmServiceId(): string
    {
        return $this->getSettings()->claim_pass_human_crm_service_id ??
            throw new RuntimeException('CRM service ID for human pass claim not found.');
    }

    private function getSettings(): Settings
    {
        /** @var Settings $settings */
        $settings = Settings::first();
        if ($settings === null) {
            throw new RuntimeException('Settings not found.');
        }

        return $settings;
    }
}
