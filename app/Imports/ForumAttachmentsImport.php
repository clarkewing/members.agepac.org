<?php

namespace App\Imports;

use App\Models\Attachment;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;

class ForumAttachmentsImport extends LegacyDBImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $fileLocation = Arr::first(
            Storage::disk('legacy-import')->allFiles('download'),
            function ($filename) use ($row) {
                return Str::endsWith($filename, "/{$row['filedataid']}.attach");
            }
        );

        if (is_null($fileLocation)) {
            return null;
        }

        return new Attachment([
            'post_id' => $row['contentid'],
            'path' => Storage::disk('public')->putFileAs(
                'attachments/' . $row['attachmentid'],
                Storage::disk('legacy-import')->path($fileLocation),
                $row['filename']
            ),
            'created_at' => Carbon::createFromTimestamp($row['dateline']),
            'updated_at' => Carbon::createFromTimestamp($row['dateline']),
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'attachmentid' => ['required', 'integer'],
            'contenttypeid' => ['required', 'integer', Rule::in([1])], // Ensure related model is Post
            'contentid' => ['required', 'integer'],
            'dateline' => ['required', 'date_format:U'],
            'filedataid' => ['required', 'integer'],
            'filename' => ['required', 'string'],
        ];
    }
}
