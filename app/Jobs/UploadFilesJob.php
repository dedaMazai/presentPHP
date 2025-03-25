<?php

namespace App\Jobs;

use App\Models\Document\DocumentType;
use App\Services\Claim\Dto\ClaimImageDto;
use App\Services\Claim\Dto\SaveClaimAttachmentDto;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Throwable;

class UploadFilesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $attemptsToNotify = 10;
    private array|string|null $email;
    private int $waitTime = 90;
    private int $maxAttempts = 4;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private $img,
        private $user,
        private $claimId
    ) {
        $this->email = config('payments_notification.email');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        DynamicsCrmClient $dynamicsCrmClient
    ) {
        try {
            $uploadedFile = $this->getImageDtos();

            $attachDto = new SaveClaimAttachmentDto(
                file_name: $uploadedFile->fileName,
                file_body: base64_encode($uploadedFile->documentBody),
                mime_type: $uploadedFile->mimeType,
                document_type_code: $this->documentType['code'],
                document_type_name: $this->documentType['name'],
                claim_id: $this->claimId,
                crm_user_id: $this->user->crm_id
            );

            $dynamicsCrmClient->saveClaimAttachment($attachDto);
        } catch (Throwable $e) {
            $attempts = $this->attempts();
            $delay = pow(2, $attempts) * 40;

            if ($attempts > $this->maxAttempts) {
                logger()->error('Достигнуто максимальное количество попыток загрузки файла', [
                    'message' => $e->getMessage(),
                    'dto' => [
                        'file_name' => $attachDto->file_name,
                        'document_type_code' => $attachDto->document_type_code,
                        'document_type_name' => $attachDto->document_type_name,
                        'claim_id' => $attachDto->claim_id,
                        'crm_user_id' => $attachDto->crm_user_id,
                    ],
                ]);
                Storage::disk('local')->delete($this->img['path']);
                $this->fail();
            } else {
                $this->release($delay);
            }
        }

        Storage::disk('local')->delete($this->img['path']);
    }

    private function getImageDtos()
    {
        $types = ['image/jpg', 'image/jpeg', 'image/png', 'image/bmp', 'image/webp'];

        if (in_array($this->img['mimeType'], $types)) {
            $file = Storage::disk('local')->get($this->img['path']);
            $image = $this->fromBase64($file);
            $input['file'] = time().'.'.$this->img['extension'];
            $destinationPath = public_path();

            $imgFile = Image::make($image->getRealPath())->save($destinationPath.'/'.$input['file'], 20)
                ->encode('data-url', 20);
            $img = $imgFile->encoded;
            $img = file_get_contents($img);
            $this->documentType = [
                "code" => "524500",
                "name" => "Фото от Заявителя"
            ];
        } else {
            $img = Storage::disk('local')->get($this->img['path']);
            $this->documentType = [
                "code" => "524750",
                "name" => "Документ от заявителя"
            ];
        }

        $imageDtos = new ClaimImageDto(
            name: pathinfo($this->img['originalName'], PATHINFO_FILENAME),
            fileName: $this->img['originalName'],
            documentType: DocumentType::photoFromApplicant(),
            documentSubtype: null,
            isCustomerAvailable: true,
            documentBody: $img,
            mimeType: $this->img['mimeType'],
            sender: $this->user->crm_id,
        );

        return $imageDtos;
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
