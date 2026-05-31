<?php

namespace App\Support;

use Embed\Embed;

class EmbedHelper
{
    /**
     * Mirrors what VanOns\Laraberg\Helpers\EmbedHelper::create() did before
     * the package was removed. Pinned to embed/embed ^3.3 — the same major
     * laraberg used — so the oEmbed HTML for existing pages is unchanged.
     */
    public static function create(string $url)
    {
        return Embed::create($url);
    }
}
