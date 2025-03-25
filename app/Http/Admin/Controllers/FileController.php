<?php

namespace App\Http\Admin\Controllers;

use App\Models\Document;
use App\Models\Image;
use CFPropertyList\CFPropertyList;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use RuntimeException;
use ZanySoft\Zip\Zip;

/**
 * Class FileController
 *
 * @package App\Http\Admin\Controllers
 */
class FileController extends Controller
{
    private const IOS_FILE_NAME = 'pioneer.ipa';
    private const ANDROID_FILE_NAME = 'pioneer.apk';
    private const IMAGES_DIRECTORY = 'public/images';
    private const DOCUMENTS_DIRECTORY = 'public/documents';
    private const BUILDS_DIRECTORY = 'public/builds';

    public function uploadImage(Request $request): JsonResponse
    {
        $file = $request->file('image');
        $name = $file->hashName();
        $path = $file->storePubliclyAs(self::IMAGES_DIRECTORY, $name);

        $image = new Image();
        $image->name = $name;
        $image->path = $path;
        $image->url = Storage::url($path);
        $image->save();

        return response()->json([
            'id' => $image->id,
            'url' => $image->url,
        ]);
    }

    public function uploadDocument(Request $request): JsonResponse
    {
        $file = $request->file('file');

        $path = $file->storePubliclyAs(self::DOCUMENTS_DIRECTORY, $file->hashName());

        return response()->json([
            'url' => Storage::url($path),
        ]);
    }

    public function uploadAdminDocument(Request $request): JsonResponse
    {
        $file = $request->file('document');
        $name = $file->getClientOriginalName();

        $path = $file->storePubliclyAs(self::DOCUMENTS_DIRECTORY, $name);

        $document = new Document();
        $document->name = $name;
        $document->mime_type = $file->getMimeType();
        $document->size = $file->getSize();
        $document->path = $path;
        $document->url = Storage::url($path);
        $document->save();

        return response()->json([
            'id' => $document->id,
            'url' => $document->url,
        ]);
    }

    /**
     * @throws Exception
     */
    public function uploadBuild(Request $request): JsonResponse
    {
        $request->validate([
            'type' => [
                'required',
                Rule::in([
                    'android',
                    'ios',
                ]),
            ],
        ]);

        $file = $request->file('file');

        if ($request->input('type') == 'ios') {
            $path = $file->storePubliclyAs(
                self::BUILDS_DIRECTORY,
                self::IOS_FILE_NAME
            );

            //07.12.2022  Не грузились ios приложения
        } else {
            $path = $file->storePubliclyAs(
                self::BUILDS_DIRECTORY,
                self::ANDROID_FILE_NAME
            );
        }

        return response()->json([
            'url' => Storage::url($path),
        ]);
    }
}
