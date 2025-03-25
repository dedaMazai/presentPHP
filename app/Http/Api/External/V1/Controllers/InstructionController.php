<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Resources\Instruction\ArticleInstuctionResource;
use App\Http\Resources\InstructionCollection;
use App\Http\Resources\InstructionResource;
use App\Models\Instruction\Instruction;
use App\Services\Instruction\InstructionService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class InstructionController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class InstructionController extends Controller
{
    public function __construct(
        private InstructionService $instructionService
    ) {
    }

    public function index(): Response
    {
        return response()->json(new InstructionCollection(Instruction::all()));
    }

    public function getArticleInstruction(string $id): Response
    {
        return response()->json(
            ['instruction' => new ArticleInstuctionResource($this->instructionService->getArticleInstruction($id))]
        );
    }
}
