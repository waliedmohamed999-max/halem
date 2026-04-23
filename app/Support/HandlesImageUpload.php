<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HandlesImageUpload
{
    protected function storeImage(?UploadedFile $file, string $folder, ?string $oldPath = null): ?string
    {
        if (! $file) {
            return $oldPath;
        }

        $path = $file->store($folder, 'public');

        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return $path;
    }
}
