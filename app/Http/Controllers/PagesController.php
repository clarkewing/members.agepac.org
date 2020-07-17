<?php

namespace App\Http\Controllers;

use App\Page;
use VanOns\Laraberg\Helpers\EmbedHelper;

class PagesController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \App\Page  $page
     * @return \Illuminate\View\View
     */
    public function show(Page $page)
    {
        $this->authorize('view', $page);

        return view('page', [
            'title' => $page->title,
            'body' => $this->renderEmbeds($page->body),
        ]);
    }

    /**
     * Extracted from \VanOns\Laraberg\Helpers\EmbedHelper to replace $regex
     * Renders any embeds in the HTML.
     *
     * @param  string  $html
     * @return string - The HTML containing all embed code
     */
    protected function renderEmbeds($html)
    {
        // Match URL from raw Gutenberg embed content
        $regex = '/<!-- wp:core-embed\/.*?-->\s*?<figure class="wp-block-embed.*?".*?\s*?<div class="wp-block-embed__wrapper">\s*?(.*?)\s*?<\/div>(?:\s*?<figcaption>.*?<\/figcaption>)?\s*?<\/figure>/';

        return preg_replace_callback($regex, function ($matches) {
            $embed = EmbedHelper::create($matches[1]);
            $url = preg_replace('/\//', '\/', preg_quote($matches[1]));
            // Replace URL with OEmbed HTML
            return preg_replace("/>\s*?$url\s*?</", ">$embed->code<", $matches[0]);
        }, $html);
    }
}
