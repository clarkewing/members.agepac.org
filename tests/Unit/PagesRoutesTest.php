<?php

namespace Tests\Unit;

use App\Page;
use Tests\TestCase;

class PagesRoutesTest extends TestCase
{
    /** @test */
    public function testCanAccessPageByPath()
    {
        $this->signIn();

        Page::factory()->create(['path' => 'foo', 'title' => 'Foo title']);

        $this->get('/pages/foo')
            ->assertOk();

        Page::factory()->create(['path' => 'foo/bar-baz', 'title' => 'Bar baz title']);

        $this->get('/pages/foo/bar-baz')
            ->assertOk();
    }

    /** @test */
    public function testGeneratesNamedRoute()
    {
        $this->assertEquals(
            config('app.url').'/pages/foo',
            route('pages.show', Page::factory()->create(['path' => 'foo']))
        );

        $this->assertEquals(
            config('app.url').'/pages/foo/bar-baz',
            route('pages.show', Page::factory()->create(['path' => 'foo/bar-baz']))
        );
    }
}
