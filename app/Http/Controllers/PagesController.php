<?php

namespace App\Http\Controllers;

use App\Models\Page;
use ClarkeWing\Handoff\Actions\GenerateHandoffUrl;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    /**
     * Redirect the page to its new home.
     *
     * Pages are now authored and served by the new app: public pages
     * redirect to the public site. Restricted pages live on the members
     * app, which has no active login yet — so members authenticate here
     * and are handed off with their session, while the new app stays the
     * authority on approval and publication.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function show(Request $request, Page $page)
    {
        if (! $page->restricted) {
            return redirect()->away(config('app.public_site_url')."/pages/{$page->path}");
        }

        if (is_null($user = $request->user())) {
            throw new AuthenticationException;
        }

        return redirect()->away(resolve(GenerateHandoffUrl::class)->generate(
            user: $user,
            toPath: "/pages/{$page->path}",
        ));
    }
}
