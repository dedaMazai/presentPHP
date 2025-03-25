<?php

namespace App\Http\Api\External\V1\Controllers\Sales;

use App\Http\Api\External\V1\Controllers\Controller;
use App\Http\Api\External\V1\Requests\Sales\CreateFileRequest;
use App\Http\Resources\Sales\File\FileResource;
use App\Services\Sales\File\FileService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FileController
 *
 * @package App\Http\Api\External\V1\Controllers\Sales
 */
class FileController extends Controller
{
    public function __construct(private FileService $fileService)
    {
    }

    public function show(string $fileId): Response
    {
        $file = $this->fileService->getFileInfo($fileId);

        return response()->json(new FileResource($file));
    }

    public function store(CreateFileRequest $request): Response
    {
        $this->fileService->storeFile($request, $this->getAuthUser()->crm_id);

        return $this->response();
    }
}
