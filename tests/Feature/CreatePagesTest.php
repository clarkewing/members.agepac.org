<?php

namespace Tests\Feature;

use App\Page;
use Tests\NovaTestCase;

class CreatePagesTest extends NovaTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->signInGod();
    }

    /** @test */
    public function testTitleIsRequired()
    {
        $this->createPage(['title' => null])
            ->assertJsonValidationErrors('title');
    }

    /** @test */
    public function testTitleCannotBeLongerThan255Characters()
    {
        $this->createPage(['title' => str_repeat('*', 256)])
            ->assertJsonValidationErrors('title');
    }

    /** @test */
    public function testPathIsRequired()
    {
        $this->createPage(['path' => null])
            ->assertJsonValidationErrors('path');
    }

    /** @test */
    public function testPathMustBeUnique()
    {
        create(Page::class, ['path' => 'foo/bar']);

        $this->createPage(['path' => 'foo/bar'])
            ->assertJsonValidationErrors('path');
    }

    /** @test */
    public function testPathCannotBeLongerThan255Characters()
    {
        $this->createPage(['path' => str_repeat('a', 256)])
            ->assertJsonValidationErrors('path');
    }

    /** @test */
    public function testPathCannotStartWithSlash()
    {
        $this->createPage(['path' => '/foo'])
            ->assertJsonValidationErrors('path');
    }

    /** @test */
    public function testPathCannotEndWithSlash()
    {
        $this->createPage(['path' => 'foo/'])
            ->assertJsonValidationErrors('path');
    }

    /** @test */
    public function testPathCannotHaveDoubleSlashes()
    {
        $this->createPage(['path' => 'foo//bar'])
            ->assertJsonValidationErrors('path');
    }

    /** @test */
    public function testRestrictedMustBeBoolean()
    {
        $this->createPage(['restricted' => 'foo'])
            ->assertJsonValidationErrors('restricted');
    }

    /** @test */
    public function testPublishedAtCanBeNull()
    {
        $this->createPage(['published_at' => null])
            ->assertJsonMissingValidationErrors('published_at');
    }

    /** @test */
    public function testPublishedAtMustBeDateTimeOfAppropriateFormat()
    {
        $this->createPage(['published_at' => 'foo'])
            ->assertJsonValidationErrors('published_at');

        $this->createPage(['published_at' => '22:00 14/06/2020'])
            ->assertJsonValidationErrors('published_at');

        $this->createPage(['published_at' => '2020-07-14 22:00:00'])
            ->assertJsonMissingValidationErrors('published_at');
    }

    /** @test */
    public function testBodyIsRequired()
    {
        $this->createPage(['body' => null])
            ->assertJsonValidationErrors('body');
    }

    /** @test */
    public function testBodyCannotBeLongerThan65535Characters()
    {
        $this->createPage(['body' => str_repeat('*', 65536)])
            ->assertJsonValidationErrors('body');
    }

    /** @test */
    public function testCanCreatePage()
    {
        $this->createPage($data = make(Page::class)->toArray())
            ->assertJsonMissingValidationErrors()
            ->assertCreated();

        $this->assertDatabaseHas('pages', $data);
    }

    /**
     * Submits a post request to create a page.
     *
     * @param  array  $overrides
     * @return \Illuminate\Testing\TestResponse
     */
    public function createPage(array $overrides = [])
    {
        return $this->storeResource('pages', array_merge(
            make(Page::class)->toArray(),
            $overrides
        ));
    }
}
