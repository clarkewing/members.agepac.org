<?php

namespace Tests\Feature\Nova;

use App\Models\MentorshipTag;
use Illuminate\Support\Arr;
use Tests\NovaTestRequests;
use Tests\TestCase;

class ManageMentorshipTagsTest extends TestCase
{
    use NovaTestRequests;

    public function permissionProvider()
    {
        return [
            'edit' => ['edit'],
            'delete' => ['delete'],
        ];
    }

    public function modeProvider()
    {
        return [
            'create' => ['store'],
            'edit' => ['update'],
        ];
    }

    /** @test */
    public function testUnauthorizedUsersCannotIndexMentorshipTags()
    {
        $this->signIn();

        $this->indexResource('mentorship-tags')
            ->assertForbidden();
    }

    /** @test */
    public function testUnauthorizedUsersCannotViewAMentorshipTag()
    {
        $mentorshipTag = $this->createMentorshipTag();

        $this->signIn();

        $this->showResource('mentorship-tags', $mentorshipTag->id)
            ->assertForbidden();
    }

    /** @test */
    public function testUnauthorizedUsersCannotCreateAMentorshipTag()
    {
        $this->signIn();

        $this->storeMentorshipTag(['name' => 'Fake tag'])
            ->assertForbidden();

        $this->assertDatabaseMissingTag(['name' => 'Fake tag']);
    }

    /** @test */
    public function testUnauthorizedUsersCannotEditAMentorshipTag()
    {
        $mentorshipTag = $this->createMentorshipTag(['name' => 'Foo tag']);

        $this->signIn();

        $this->updateMentorshipTag(['name' => 'Fake tag'], $mentorshipTag)
            ->assertForbidden();

        $this->assertEquals('Foo tag', $mentorshipTag->fresh()->getTranslation('name', 'fr'));
    }

    /** @test */
    public function testUnauthorizedUsersCannotDeleteAMentorshipTag()
    {
        $mentorshipTag = $this->createMentorshipTag();

        $this->signIn();

        $this->deleteResource('mentorship-tags', $mentorshipTag->id)
            ->assertForbidden();

        $this->assertDatabaseHasTag(['id' => $mentorshipTag->id]);
    }

    /**
     * @test
     * @dataProvider permissionProvider
     */
    public function testAuthorizedUsersCanIndexMentorshipTags($permission)
    {
        $this->signInWithPermission('mentorship_tags.' . $permission);

        $this->indexResource('mentorship-tags')
            ->assertOk();
    }

    /**
     * @test
     * @dataProvider permissionProvider
     */
    public function testAuthorizedUsersCanViewAMentorshipTag($permission)
    {
        $mentorshipTag = $this->createMentorshipTag();

        $this->signInWithPermission('mentorship_tags.' . $permission);

        $this->showResource('mentorship-tags', $mentorshipTag->id)
            ->assertOk();
    }

    /**
     * @test
     * @dataProvider permissionProvider
     */
    public function testAuthorizedUsersCanCreateAMentorshipTag($permission)
    {
        $this->signInWithPermission('mentorship_tags.' . $permission);

        $this->storeMentorshipTag(['name' => 'Sexy tag'])
            ->assertCreated();

        $this->assertDatabaseHasTag(['name' => 'Sexy tag']);
    }

    /** @test */
    public function testAuthorizedUsersCanEditAMentorshipTag()
    {
        $mentorshipTag = $this->createMentorshipTag();

        $this->signInWithPermission('mentorship_tags.edit');

        $this->updateMentorshipTag(['name' => 'Banana tag'], $mentorshipTag)
            ->assertOk();

        $this->assertDatabaseHasTag(['id' => $mentorshipTag->id, 'name' => 'Banana tag']);
    }

    /** @test */
    public function testAuthorizedUsersCanDeleteAMentorshipTag()
    {
        $mentorshipTag = $this->createMentorshipTag();

        $this->signInWithPermission('mentorship_tags.delete');

        $this->deleteResource('mentorship-tags', $mentorshipTag->id)
            ->assertOk();

        $this->assertDatabaseMissingTag(['id' => $mentorshipTag->id]);
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testNameIsRequired($verb)
    {
        $this->signInWithPermission('mentorship_tags.edit');

        $this->{$verb . 'mentorshipTag'}(['name' => null])
            ->assertJsonValidationErrors('name');
    }

    /**
     * Submits a request to create a mentorship_tags.
     *
     * @param  array  $overrides
     * @return \Illuminate\Testing\TestResponse
     */
    public function storeMentorshipTag(array $overrides = [])
    {
        return $this->storeResource('mentorship-tags', array_merge(
            ['name' => 'Foo tag'],
            $overrides
        ));
    }

    /**
     * Create a mentorship tag.
     *
     * @param  array  $overrides
     * @return mixed
     */
    protected function createMentorshipTag(array $overrides = [])
    {
        return MentorshipTag::create(
            array_merge(['name' => 'Foo Tag'], $overrides)
        );
    }

    /**
     * Submits a request to update an existing mentorship_tags.
     *
     * @param  array  $data
     * @param  \App\Models\MentorshipTag|null  $mentorshipTag
     * @return \Illuminate\Testing\TestResponse
     */
    protected function updateMentorshipTag(array $data = [], MentorshipTag $mentorshipTag = null)
    {
        $mentorshipTag = $mentorshipTag ?? $this->createMentorshipTag();

        return $this->updateResource(
            'mentorship-tags', $mentorshipTag->id,
            array_merge($mentorshipTag->toArray(), $data)
        );
    }

    /**
     * @param  array  $values
     * @return \Tests\Feature\Nova\ManageMentorshipTagsTest
     */
    protected function assertDatabaseHasTag(array $values): self
    {
        return $this->assertDatabaseHas('tags', $this->dbValues($values));
    }

    /**
     * @param  array  $values
     * @return \Tests\Feature\Nova\ManageMentorshipTagsTest
     */
    protected function assertDatabaseMissingTag(array $values): self
    {
        return $this->assertDatabaseMissing('tags', $this->dbValues($values));
    }

    protected function dbValues($values)
    {
        $newValues = ['type' => 'mentorship'];

        if (isset($values['name'])) {
            $newValues['name->fr'] = Arr::pull($values, 'name');
        }

        if (isset($values['slug'])) {
            $newValues['slug->fr'] = Arr::pull($values, 'slug');
        }

        return $newValues;
    }
}
