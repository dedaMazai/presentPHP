<?php

namespace App\Services\Instruction;

use App\Http\Api\External\V1\Requests\Sales\ManagerContactsRequest;
use App\Models\Building\Building;
use App\Models\Document;
use App\Models\User\User;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Sales\Property\PropertyRepository;

/**
 * Class InstructionService
 *
 * @package App\Services\Instruction
 */
class InstructionService
{
    public function __construct(
        private PropertyRepository $propertyRepository,
    ) {
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function getArticleInstruction(string $id): ?Document
    {
        $property = $this->propertyRepository->getById($id);
        $propertyZid = $property->getZid();

        if ($propertyZid != null) {
            $document_id = Building::where('build_id', '=', $propertyZid)->first()?->document_id;

            if ($document_id != null) {
                $instruction = Document::find($document_id);
            } else {
                $instruction = null;
            }
        } else {
            $instruction = null;
        }

        return $instruction;
    }
}
