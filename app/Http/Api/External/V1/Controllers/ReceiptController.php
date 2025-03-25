<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Resources\ReceiptCollection;
use App\Services\Receipt\ReceiptRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function response;

/**
 * Class ReceiptController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class ReceiptController extends Controller
{
    public function __construct(private ReceiptRepository $repository)
    {
    }

    /**
     * @throws ValidationException
     */
    public function index(string $accountNumber, Request $request): Response
    {
        $this->validate($request, [
            'start_date' => 'date',
            'end_date' => 'date',
        ]);

        $receipts = $this->repository->getReceipts(
            $accountNumber,
            new Carbon($request->input('start_date', Carbon::now()->startOfYear()->toDateString())),
            new Carbon($request->input('end_date', Carbon::now()->endOfYear()->toDateString())),
        );

        return response()->json(new ReceiptCollection($receipts));
    }

    /**
     * @throws ValidationException
     */
    public function getPdf(string $accountNumber, Request $request): Response
    {
        $this->validate($request, [
            'uri' => 'required|string',
        ]);

        try {
            $receiptFile = $this->repository->getReceiptPdfByUri($request->input('uri'));
        } catch (Exception) {
            throw new NotFoundHttpException();
        }

        return response(base64_decode($receiptFile->getDocumentBody()))
            ->header('Content-Type', $receiptFile->getMimeType());
    }
}
