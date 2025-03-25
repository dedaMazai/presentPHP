<?php

namespace App\Services\Sales\File;

use App\Models\Sales\SalesDocument;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

/**
 * Class FileService
 *
 * @package App\Services\Sales\File
 */
class FileService
{
    public function __construct(private DynamicsCrmClient $dynamicsCrmClient)
    {
    }

    public function getFileInfo(string $contractId)
    {
        $document = $this->dynamicsCrmClient->getDocumentById($contractId);

        return $this->makeDocument($document);
    }

    public function storeFile($request, $userId)
    {
        $annotations = [];
        $html = '';
        $namePrefix = "Документ; ";

        foreach ($request->get('files') as $file) {
            if ($file['mime_type'] == 'image/jpeg' || $file['mime_type'] == 'image/png') {
                $image = $this->fromBase64(base64_decode($file['body']));
                $input['file'] = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path();

                $imgFile = Image::make($image->getRealPath())->resize(1080, 1920)
                    ->save($destinationPath.'/'.$input['file'], 70)
                    ->encode('data-url', 70);
                $img = $imgFile->encoded;
                $img = preg_replace('#^data:image/[^;]+;base64,#', '', $img);

                $html .= '<img src="data:' . $file['mime_type'] . ';base64,' .
                    $img . '" alt="' . $file['name'] . '">';

                $html .= '<style>
                                img {
                                    width: auto%;
                                    height: 100%;
                                }
                          </style>';
            } else {
                $name = $namePrefix . $file['name'];

                $annotations[] = [
                    'Name' => $name,
                    'FileName' => $name,
                    'DocumentType' => [
                        'code' => strval($file['document_type']),
                    ],
                    'IsCustomerAvailable' => true,
                    'DocumentBody' => $file['body'],
                    'MimeType' => $file['mime_type'],
                ];
            }
        }

        if ($html != '') {
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->render();

            $name = $namePrefix . $userId . Str::random(10);

            $annotations[] = [
                'Name' => $name . '.pdf',
                'FileName' => $name . '.pdf',
                'DocumentType' => [
                    'code' => strval($request->get('files')[0]['document_type']),
                ],
                'IsCustomerAvailable' => true,
                'DocumentBody' => base64_encode($dompdf->output()),
                'MimeType' => 'application/pdf',
            ];
        }

        $body = [
            'ObjectId' => $request->object_id,
            'ObjectTypeCode' => $request->object_type,
            'Annotations' => $annotations
        ];

        $this->dynamicsCrmClient->uploadSaleFile($body, $userId);
    }

    private function makeDocument(array $data): SalesDocument
    {
        return new SalesDocument(
            id: $data['id'],
            name: $data['fileName'],
            document: $data['documentBody'],
            mimeType: $data['mimeType'],
            type: $data['documentType']['code'],
            processingStatus: $data['documentProcessingStatus']['code'],
            status: $data['status']['code'],
            url: $data['url'],
        );
    }

    public static function fromBase64(string $base64File): UploadedFile
    {
        // save it to temporary dir first.
        $tmpFilePath = sys_get_temp_dir() . '/' . Str::uuid()->toString();
        file_put_contents($tmpFilePath, $base64File);

// this just to help us get file info.
        $tmpFile = new File($tmpFilePath);

        $file = new UploadedFile(
            $tmpFile->getPathname(),
            $tmpFile->getFilename(),
            $tmpFile->getMimeType(),
            0,
            true // Mark it as test, since the file isn't from real HTTP POST.
        );

        return $file;
    }
}
