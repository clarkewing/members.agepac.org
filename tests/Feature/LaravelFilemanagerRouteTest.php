<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LaravelFilemanagerRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();
    }

    /** @test */
    public function testLaravelFilemanagerRouteIsNotRegistered()
    {
        $this->assertFalse(
            Route::has('unisharp.lfm.show'),
            'The unisharp/laravel-filemanager package must not be installed — it was the entry point for the security incident.'
        );
    }

    /** @test */
    public function testLaravelFilemanagerEndpointIsUnreachableForAuthenticatedUsers()
    {
        $this->signIn();

        $this->get('/laravel-filemanager?type=Images')
            ->assertNotFound();
    }
}
