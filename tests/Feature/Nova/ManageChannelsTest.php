<?php

namespace Tests\Feature\Nova;

use App\Models\Channel;
use Illuminate\Support\Arr;
use Tests\NovaTestRequests;
use Tests\TestCase;

class ManageChannelsTest extends TestCase
{
    use NovaTestRequests;

    public function modeProvider()
    {
        return [
            'create' => ['store'],
            'edit' => ['update'],
        ];
    }

    /** @test */
    public function testUnauthorizedUsersCannotIndexChannels()
    {
        $this->signIn();

        $this->indexResource('channels')
            ->assertForbidden();
    }

    /** @test */
    public function testUnauthorizedUsersCannotViewAChannel()
    {
        $channel = Channel::factory()->create();

        $this->signIn();

        $this->showResource('channels', $channel->id)
            ->assertForbidden();
    }

    /** @test */
    public function testUnauthorizedUsersCannotCreateAChannel()
    {
        $this->signIn();

        $this->storeChannel(['name' => 'Fake Channel'])
            ->assertForbidden();

        $this->assertDatabaseMissing('channels', ['name' => 'Fake Channel']);
    }

    /** @test */
    public function testUnauthorizedUsersCannotEditAChannel()
    {
        $channel = Channel::factory()->create(['name' => 'Foobar']);

        $this->signIn();

        $this->updateChannel(['name' => 'Fake Channel'], $channel)
            ->assertForbidden();

        $this->assertEquals('Foobar', $channel->fresh()->name);
    }

    /** @test */
    public function testUnauthorizedUsersCannotDeleteAChannel()
    {
        $channel = Channel::factory()->create();

        $this->signIn();

        $this->deleteResource('channels', $channel->id)
            ->assertForbidden();

        $this->assertDatabaseHas('channels', ['id' => $channel->id]);
    }

    /** @test */
    public function testAuthorizedUsersCanIndexChannels()
    {
        $this->signInWithPermission('channels.manage');

        $this->indexResource('channels')
            ->assertOk();
    }

    /** @test */
    public function testAuthorizedUsersCanViewAChannel()
    {
        $channel = Channel::factory()->create();

        $this->signInWithPermission('channels.manage');

        $this->showResource('channels', $channel->id)
            ->assertOk();
    }

    /** @test */
    public function testAuthorizedUsersCanCreateAChannel()
    {
        $this->signInWithPermission('channels.manage');

        $this->storeChannel(['name' => 'Cool Channel'])
            ->assertCreated();

        $this->assertDatabaseHas('channels', ['name' => 'Cool Channel']);
    }

    /** @test */
    public function testAuthorizedUsersCanEditAChannel()
    {
        $channel = Channel::factory()->create();

        $this->signInWithPermission('channels.manage');

        $this->updateChannel(['name' => 'Updated Channel Name'], $channel)
            ->assertOk();

        $this->assertDatabaseHas('channels', ['id' => $channel->id, 'name' => 'Updated Channel Name']);
    }

    /** @test */
    public function testAuthorizedUsersCanDeleteAChannel()
    {
        $channel = Channel::factory()->create();

        $this->signInWithPermission('channels.manage');

        $this->deleteResource('channels', $channel->id)
            ->assertOk();

        $this->assertDatabaseMissing('channels', ['id' => $channel->id]);
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testNameIsRequired($verb)
    {
        $this->signInWithPermission('channels.manage');

        $this->{$verb . 'Channel'}(['name' => null])
            ->assertJsonValidationErrors('name');
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testNameCannotBeLongerThan50Characters($verb)
    {
        $this->signInWithPermission('channels.manage');

        $this->{$verb . 'Channel'}(['name' => str_repeat('*', 51)])
            ->assertJsonValidationErrors('name');
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testNameMustBeUnique($verb)
    {
        $this->signInWithPermission('channels.manage');

        Channel::factory()->create(['name' => 'Foo Channel']);

        $this->{$verb . 'Channel'}(['name' => 'Foo Channel'])
            ->assertJsonValidationErrors('name');
    }

    /** @test */
    public function testNameCanBeSameWhenUpdating()
    {
        $this->signInWithPermission('channels.manage');

        $channel = Channel::factory()->create(['name' => 'Foo Channel']);

        $this->updateChannel(['name' => 'Foo Channel'], $channel)
            ->assertJsonMissingValidationErrors('name');
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testSlugIsRequired($verb)
    {
        $this->signInWithPermission('channels.manage');

        $this->{$verb . 'Channel'}(['slug' => null])
            ->assertJsonValidationErrors('slug');
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testSlugCannotBeLongerThan50Characters($verb)
    {
        $this->signInWithPermission('channels.manage');

        $this->{$verb . 'Channel'}(['slug' => str_repeat('*', 51)])
            ->assertJsonValidationErrors('slug');
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testSlugMustBeUnique($verb)
    {
        $this->signInWithPermission('channels.manage');

        Channel::factory()->create(['slug' => 'foo']);

        $this->{$verb . 'Channel'}(['slug' => 'foo'])
            ->assertJsonValidationErrors('slug');
    }

    /** @test */
    public function testSlugCanBeSameWhenUpdating()
    {
        $this->signInWithPermission('channels.manage');

        $channel = Channel::factory()->create(['slug' => 'foo']);

        $this->updateChannel(['slug' => 'foo'], $channel)
            ->assertJsonMissingValidationErrors('slug');
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testDescriptionCanBeNull($verb)
    {
        $this->signInWithPermission('channels.manage');

        $this->{$verb . 'Channel'}(['description' => null])
            ->assertJsonMissingValidationErrors('description');
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testDescriptionCannotBeLongerThan255Characters($verb)
    {
        $this->signInWithPermission('channels.manage');

        $this->{$verb . 'Channel'}(['description' => str_repeat('*', 256)])
            ->assertJsonValidationErrors('description');
    }

    /** @test */
    public function testArchivedMustBeBoolean()
    {
        $this->signInWithPermission('channels.manage');

        $this->updateChannel(['archived' => 'foo'])
            ->assertJsonValidationErrors('archived');

        $this->updateChannel(['archived' => 123])
            ->assertJsonValidationErrors('archived');
    }

    /** @test */
    public function testAuthorizedUsersCanChangeChannelRestrictions()
    {
        $this->signInWithPermission('channels.manage');

        $channel = Channel::factory()->create();

        $this->assertFalse($channel->isRestricted('post'));

        $this->updateChannel(['posting_restricted' => true], $channel)
            ->assertSuccessful();

        $this->assertTrue($channel->isRestricted('post'));

        $this->updateChannel(['posting_restricted' => false], $channel)
            ->assertSuccessful();

        $this->assertFalse($channel->isRestricted('post'));
    }

    /** @test */
    public function testAChannelWithARestrictionShowsALinkToTheAssociatedPermission()
    {
        $this->signInWithPermission('channels.manage');

        $channel = Channel::factory()->create();

        $permission = $channel->createPermission('view');

        $fields = $this->showResource('channels', $channel->id)
            ->assertSuccessful()
            ->json('resource.fields');

        $permissionField = Arr::first($fields, function ($field) {
            return $field['name'] === 'View Permission';
        });

        $this->assertEquals(
            "<a class=\"no-underline font-bold dim text-primary\" href=\"/nova/resources/permissions/$permission->id\">$permission->name</a>",
            $permissionField['value']
        );
    }

    /**
     * Submits a request to create a channel.
     *
     * @param  array  $overrides
     * @return \Illuminate\Testing\TestResponse
     */
    public function storeChannel(array $overrides = [])
    {
        return $this->storeResource('channels', array_merge(
            Channel::factory()->make()->toArray(),
            $overrides
        ));
    }

    /**
     * Submits a request to update an existing channel.
     *
     * @param  array  $data
     * @param  \App\Models\Channel|null  $channel
     * @return \Illuminate\Testing\TestResponse
     */
    public function updateChannel(array $data = [], Channel $channel = null)
    {
        $channel = $channel ?? Channel::factory()->create();

        return $this->updateResource(
            'channels', $channel->id,
            array_merge($channel->toArray(), $data)
        );
    }
}
