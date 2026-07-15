<?php

namespace App\Traits;

use Illuminate\Http\Request;
use File;
use phpDocumentor\Reflection\Types\Void_;

trait FileUploadTrait
{

    function uploadImage(Request $request, $inputName, $oldPath = NULL, $path = "/uploads")
    {

        if ($request->hasFile($inputName)) {

            $image = $request->{$inputName};
            $imageName = 'media_' . bin2hex(random_bytes(16)) . '.' . $image->extension();

            $image->move(public_path($path), $imageName);

            // delete previous file if exist
            if ($this->isSafeUploadedPath($oldPath) && File::exists(public_path($oldPath))) {
                File::delete(public_path($oldPath));
            }

            return $path . '/' . $imageName;
        }

        return NULL;
    }

    /**
     * Remove file
     */
    function removeImage(string $path) : void {
        if ($this->isSafeUploadedPath($path) && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }

    private function isSafeUploadedPath(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        $normalizedPath = str_replace('\\', '/', $path);

        return str_starts_with($normalizedPath, '/uploads/')
            && ! str_contains($normalizedPath, '..');
    }
}
