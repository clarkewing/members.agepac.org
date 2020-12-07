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

        create(Page::class, ['path' => 'foo', 'title' => 'Foo title']);

        $this->get('/pages/foo')
            ->assertOk();

        create(Page::class, ['path' => 'foo/bar-baz', 'title' => 'Bar baz title']);

        $this->get('/pages/foo/bar-baz')
            ->assertOk();
    }

    /** @test */
    public function testGeneratesNamedRoute()
    {
        $this->assertEquals(
            config('app.url').'/pages/foo',
            route('pages.show', create(Page::class, ['path' => 'foo']))
        );

        $this->assertEquals(
            config('app.url').'/pages/foo/bar-baz',
            route('pages.show', create(Page::class, ['path' => 'foo/bar-baz']))
        );
    }
}
