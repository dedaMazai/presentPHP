<?php

namespace App\Services\Feedback\Dto;

/**
 * Class SaveFeedbackDto
 *
 * @package App\Services\Feedback\Dto
 */
class SaveFeedbackDto
{
    public function __construct(
        public string $message,
        public ?string $name,
        public ?string $email,
        public ?int $user_id,
        public ?string $app_version,
        public ?string $os_version,
        public ?string $phone,
    ) {
    }
}
