<?php

namespace App\Models\Pass;

use Carbon\Carbon;

/**
 * Class Pass
 *
 * @package App\Models\Meter
 */
class Pass
{
    /**
     * @param string $id
     * @param string $name
     * @param PassStatus|null $status
     * @param Carbon|null $arrivalDate
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @param Carbon|null $creationDate
     * @param string|null $comment
     * @param PassAssignment|null $assignment
     * @param PassCarType|null $carType
     */
    public function __construct(
        private string $id,
        private string $name,
        private ?PassStatus $status,
        private ?Carbon $arrivalDate,
        private ?Carbon $startDate,
        private ?Carbon $endDate,
        private ?Carbon $creationDate,
        private ?string $comment,
        private ?PassAssignment $assignment,
        private ?PassCarType $carType
    ) {
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return PassStatus|null
     */
    public function getStatus(): ?PassStatus
    {
        return $this->status;
    }

    /**
     * @return Carbon|null
     */
    public function getArrivalDate(): ?Carbon
    {
        return $this->arrivalDate;
    }

    /**
     * @return Carbon|null
     */
    public function getStartDate(): ?Carbon
    {
        return $this->startDate;
    }

    /**
     * @return Carbon|null
     */
    public function getEndDate(): ?Carbon
    {
        return $this->endDate;
    }

    /**
     * @return Carbon|null
     */
    public function getCreationDate(): ?Carbon
    {
        return $this->creationDate;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @return PassAssignment|null
     */
    public function getAssignment(): ?PassAssignment
    {
        return $this->assignment;
    }

    /**
     * @return PassCarType|null
     */
    public function getCarType(): ?PassCarType
    {
        return $this->carType;
    }
}
