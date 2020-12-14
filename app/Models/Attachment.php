<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Attachment extends Model
{
    use HasFactory;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'post_id',
        'path',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(function ($attachment): void {
            // Set the attachment's id.
            $attachment->id = Str::uuid();
        });

        static::deleting(function ($attachment): void {
            Storage::disk('public')->delete($attachment->path);
        });
    }

    /**
     * Get the post that owns the attachment.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the HTML to render the attachment.
     */
    public function html(): string
    {
        $trixType = 'file';

        $trixData = [
            'contentType' => Storage::disk('public')->mimeType($this->path),
            'filename' => basename($this->path),
            'filesize' => Storage::disk('public')->size($this->path),
            'id' => $this->id,
            'href' => '/storage/' . $this->path,
            'url' => '/storage/' . $this->path,
        ];

        if (Str::startsWith($trixData['contentType'], 'image')) {
            $trixType = 'preview';

            [$trixData['width'], $trixData['height']] = getimagesize(
                Storage::disk('public')->path($this->path)
            );
        }

        $html = '<figure data-trix-attachment="' . htmlentities(json_encode($trixData)) . '" class="attachment attachment--' . $trixType . '">'
                . '<a href="' . $trixData['href'] . '">';

        if ($trixType === 'preview') {
            $html .= '<img src="' . $trixData['url'] . '" width="' . $trixData['width'] . '" height="' . $trixData['height'] . '" alt="' . $trixData['filename'] . '">';
        }

        return $html
             . '<figcaption class="attachment__caption">'
             . '<span class="attachment__name">' . $trixData['filename'] . '</span>'
             . '<span class="attachment__size">' . $this->humanSize . '</span>'
             . '</figcaption>'
             . '</a>'
             . '</figure>';
    }

    /**
     * Get the attachment's human readable size.
     *
     * @return string
     */
    public function getHumanSizeAttribute(): string
    {
        $bytes = Storage::disk('public')->size($this->path);
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
