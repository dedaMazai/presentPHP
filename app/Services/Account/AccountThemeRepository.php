<?php

namespace App\Services\Account;

use App\Models\Account\AccountDocument;
use App\Models\Account\AccountInfo;
use App\Models\Account\AccountTheme;
use App\Models\UkProject;
use App\Services\Crm\CrmClient;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use Illuminate\Validation\ValidationException;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class AccountThemeRepository
 *
 * @package App\Services\Account
 */
class AccountThemeRepository
{
    public function __construct(
        private DynamicsCrmClient $dynamicsCrmClient,
    ) {
    }

    private array $themes = [];

    /**
     * @throws \App\Services\DynamicsCrm\Exceptions\NotFoundException
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     */
    public function getAllByAccountNumber(string $accountNumber): array
    {
        $data = $this->dynamicsCrmClient->getAccountThemesByAccountNumber($accountNumber);
        $ukProjectId = AccountInfo::find($accountNumber)->uk_project_id;
        $ukMarketImage = UkProject::find($ukProjectId)->marketImage?->url;

        foreach ($data['serviceUkList'] as $theme) {
            if ($theme['groupingCode']['code'] == 5 && $theme['isDisplayedInLk'] == true) {
                $accountTheme = $this->makeAccountTheme($theme);
                $accountTheme->setMarketImageUrl($ukMarketImage);
                $this->themes[] = $accountTheme;
            }
        }

        $this->sortById();

        return array_reverse($this->themes);
    }

    private function makeAccountTheme(array $data): AccountTheme
    {
        return new AccountTheme(
            theme_id: $data['incidentClassificationCode']['code']??$data['name'],
            name: $data['heading'],
            description: $data['description']??null,
        );
    }

    private function sortThemes($id)
    {
        return usort($this->themes, function ($theme1, $theme2) use ($id) {
            if ($theme1->getThemeId() == $theme2->getThemeId()) {
                return 0;
            }
            return (
                $theme1->getThemeId() == $id
            ) ? 1 : 0;
        });
    }

    private function sortById()
    {
        $this->sortThemes(4);
        $this->sortThemes(8);
        $this->sortThemes(11);
        $this->sortThemes(12);
        $this->sortThemes(5);
        $this->sortThemes(1);
        $this->sortThemes(10);
    }
}
