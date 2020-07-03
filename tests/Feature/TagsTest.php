<?php

namespace Tests\Feature;

use Spatie\Tags\Tag;
use Tests\TestCase;

class TagsTest extends TestCase
{
    /** @test */
    public function testGuestCannotListTags()
    {
        $this->withExceptionHandling();

        $this->getJson(route('api.tags.index'))
            ->assertUnauthorized();
    }

    /** @test */
    public function testUserCanListAllTags()
    {
        $this->signIn();

        $this->getJson(route('api.tags.index'))
            ->assertJson(Tag::all()->toArray());
    }

    /** @test */
    public function testUserCanListTagsOfGivenType()
    {
        Tag::create([
            'type' => 'foo',
            'name' => 'The tag we want',
        ]);
        Tag::create([
            'type' => 'bar',
            'name' => 'The tag we do not want',
        ]);

        $this->signIn();

        $results = $this->getJson(route('api.tags.index', ['type' => 'foo']))
            ->json();

        $this->assertCount(1, $results);
        $this->assertEquals('The tag we want', $results[0]['name']['fr']);
    }

    /** @test */
    public function testUserCanSearchByTagName()
    {
        Tag::create(['name' => 'Cool tag']);
        Tag::create(['name' => 'Sick tag']);

        $this->signIn();

        $this->getJson(route('api.tags.index', ['query' => 'foobarbaz']))
            ->assertJsonCount(0);

        $this->getJson(route('api.tags.index', ['query' => 'cool']))
            ->assertJsonCount(1);

        $this->getJson(route('api.tags.index', ['query' => 'tag']))
            ->assertJsonCount(2);
    }

    /** @test */
    public function testUserCanSearchByTagNameInType()
    {
        Tag::create([
            'type' => 'foo',
            'name' => 'Cool tag',
        ]);
        Tag::create([
            'type' => 'bar',
            'name' => 'Sick tag',
        ]);

        $this->signIn();

        $this->getJson(route('api.tags.index', [
            'type' => 'foo',
            'query' => 'cool',
        ]))->assertJsonCount(1);

        $this->getJson(route('api.tags.index', [
            'type' => 'bar',
            'query' => 'cool',
        ]))->assertJsonCount(0);
    }
}
