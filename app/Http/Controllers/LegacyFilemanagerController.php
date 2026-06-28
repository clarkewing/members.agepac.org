<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Serves files uploaded via the (now-removed) unisharp/laravel-filemanager
 * package so legacy links embedded in old Gutenberg page bodies — and any
 * external references such as bookmarked PDFs — keep working.
 *
 * Ported from UniSharp\LaravelFilemanager\Controllers\RedirectController
 * (v1.9.2). The original served files via this same URL pattern after an
 * auth check. The exploited vulnerability in laravel-filemanager was in
 * its upload/write paths, none of which are present here — this controller
 * is read-only and limited to two hard-coded folders under storage/.
 *
 * Two changes from the original, both security-related:
 *
 * - Path components come from the route parameters directly rather than
 *   being parsed back out of urldecode(request()->url()). The original's
 *   double-decode could resurrect %252e%252e-style traversal payloads.
 * - A realpath() guard rejects requests whose resolved path escapes the
 *   intended subtree, even if a parameter is the literal string '..'.
 */
class LegacyFilemanagerController extends Controller
{
    protected const BASE_DIRECTORY = 'storage';

    public function getFile(string $base_path, string $file_name): BinaryFileResponse
    {
        return $this->serve('files', $base_path, $file_name);
    }

    public function getImage(string $base_path, string $image_name): BinaryFileResponse
    {
        return $this->serve('photos', $base_path, $image_name);
    }

    protected function serve(string $folder, string $base_path, string $file_name): BinaryFileResponse
    {
        $expectedBase = realpath(base_path(self::BASE_DIRECTORY . "/{$folder}"));
        $resolved = realpath(base_path(self::BASE_DIRECTORY . "/{$folder}/{$base_path}/{$file_name}"));

        abort_unless(
            $expectedBase
                && $resolved
                && str_starts_with($resolved, $expectedBase . DIRECTORY_SEPARATOR)
                && File::isFile($resolved),
            404,
        );

        return response()->file($resolved);
    }
}
