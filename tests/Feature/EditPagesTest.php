<?php

namespace Tests\Feature;

use App\Page;
use Tests\NovaTestCase;

class EditPagesTest extends NovaTestCase
{
    /**
     * @var \App\Page
     */
    protected $page;

    public function setUp(): void
    {
        parent::setUp();

        $this->signInGod();

        $this->page = create(Page::class);
    }

    /** @test */
    public function testTitleIsRequired()
    {
        $this->updatePage(['title' => null])
            ->assertJsonValidationErrors('title');
    }

    /** @test */
    public function testTitleCannotBeLongerThan255Characters()
    {
        $this->updatePage(['title' => str_repeat('*', 256)])
            ->assertJsonValidationErrors('title');
    }

    /** @test */
    public function testPathIsRequiredIfSet()
    {
        $this->updatePage(['path' => null])
            ->assertJsonValidationErrors('path');
    }

    /** @test */
    public function testPathMustBeUnique()
    {
        create(Page::class, ['path' => 'foo/bar']);

        $this->updatePage(['path' => 'foo/bar'])
            ->assertJsonValidationErrors('path');
    }

    /** @test */
    public function testPathCannotBeLongerThan255Characters()
    {
        $this->updatePage(['path' => str_repeat('a', 256)])
            ->assertJsonValidationErrors('path');
    }

    /** @test */
    public function testPathCannotStartWithSlash()
    {
        $this->updatePage(['path' => '/foo'])
            ->assertJsonValidationErrors('path');
    }

    /** @test */
    public function testPathCannotEndWithSlash()
    {
        $this->updatePage(['path' => 'foo/'])
            ->assertJsonValidationErrors('path');
    }

    /** @test */
    public function testPathCannotHaveDoubleSlashes()
    {
        $this->updatePage(['path' => 'foo//bar'])
            ->assertJsonValidationErrors('path');
    }

    /** @test */
    public function testRestrictedMustBeBoolean()
    {
        $this->updatePage(['restricted' => 'foo'])
            ->assertJsonValidationErrors('restricted');
    }

    /** @test */
    public function testPublishedAtCanBeNull()
    {
        $this->updatePage(['published_at' => null])
            ->assertJsonMissingValidationErrors('published_at');
    }

    /** @test */
    public function testPublishedAtMustBeDateTimeOfAppropriateFormat()
    {
        $this->updatePage(['published_at' => 'foo'])
            ->assertJsonValidationErrors('published_at');

        $this->updatePage(['published_at' => '22:00 14/06/2020'])
            ->assertJsonValidationErrors('published_at');

        $this->updatePage(['published_at' => '2020-07-14 22:00:00'])
            ->assertJsonMissingValidationErrors('published_at');
    }

    /** @test */
    public function testBodyIsRequired()
    {
        $this->updatePage(['body' => null])
            ->assertJsonValidationErrors('body');
    }

    /** @test */
    public function testBodyCannotBeLongerThan65535Characters()
    {
        $this->updatePage(['body' => str_repeat('*', 65536)])
            ->assertJsonValidationErrors('body');
    }

    /** @test */
    public function testCanUpdatePage()
    {
        $this->updatePage($data = make(Page::class)->toArray())
            ->assertJsonMissingValidationErrors()
            ->assertOk();

        $this->assertDatabaseHas('pages', $data);
    }

    /**
     * Submits a post request to create a page.
     *
     * @param  array  $data
     * @return \Illuminate\Testing\TestResponse
     */
    public function updatePage(array $data = [])
    {
        return $this->updateResource('pages', $this->page->id,
            array_merge($this->page->toArray(), $data)
        );
    }
}
