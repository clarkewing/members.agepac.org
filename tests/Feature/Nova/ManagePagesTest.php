<?php

namespace Tests\Feature\Nova;

use App\Page;
use Illuminate\Support\Arr;
use Tests\NovaTestRequests;
use Tests\TestCase;

class ManagePagesTest extends TestCase
{
    use NovaTestRequests;

    public function permissionProvider()
    {
        return [
            'create' => ['create'],
            'edit' => ['edit'],
            'delete' => ['delete'],
            'viewDeleted' => ['viewDeleted'],
            'restore' => ['restore'],
            'forceDelete' => ['forceDelete'],
        ];
    }

    public function modeProvider()
    {
        return [
            'create' => ['store', 'create'],
            'edit' => ['update', 'edit'],
        ];
    }

    /** @test */
    public function testUnauthorizedUsersCannotIndexPages()
    {
        $this->signIn();

        $this->indexResource('pages')
            ->assertForbidden();
    }

    /** @test */
    public function testUnauthorizedUsersCannotViewAPage()
    {
        $page = create(Page::class);

        $this->signIn();

        $this->showResource('pages', $page->id)
            ->assertForbidden();
    }

    /** @test */
    public function testUnauthorizedUsersCannotCreateAPage()
    {
        foreach (Arr::except($this->permissionProvider(), 'create') as [$permission]) {
            $this->signInWithPermission("pages.$permission");

            $this->storePage(['title' => 'Forbidden Page'])
                ->assertForbidden();

            $this->assertDatabaseMissing('pages', ['title' => 'Forbidden Page']);
        }
    }

    /** @test */
    public function testUnauthorizedUsersCannotEditAPage()
    {
        foreach (Arr::except($this->permissionProvider(), 'edit') as [$permission]) {
            $page = create(Page::class, ['title' => 'Foo Page']);

            $this->signInWithPermission("pages.$permission");

            $this->updatePage(['title' => 'Fake Page'], $page)
                ->assertForbidden();

            $this->assertEquals('Foo Page', $page->fresh()->title);
        }
    }

    /** @test */
    public function testUnauthorizedUsersCannotDeleteAPage()
    {
        foreach (Arr::except($this->permissionProvider(), 'delete') as [$permission]) {
            $page = create(Page::class, ['title' => 'Foo Page']);

            $this->signInWithPermission("pages.$permission");

            $this->deleteResource('pages', $page->id);
            // Nova doesn't return 403 on unauthorized delete request, so we don't check the status.
            // Beware: with a random user, it would return a 403 because of the viewAll authorization.
            // But with any permission, it returns a 200.

            $this->assertDatabaseHas('pages', ['id' => $page->id, 'deleted_at' => null]);
        }
    }

    /** @test */
    public function testUnauthorizedUsersCannotViewDeletedPages()
    {
        foreach (Arr::except($this->permissionProvider(), 'viewDeleted') as [$permission]) {
            $page = tap(create(Page::class))->delete();

            $this->assertSoftDeleted('pages', ['id' => $page->id]);

            $this->signInWithPermission("pages.$permission");

            $this->showResource('pages', $page->id)
                ->assertForbidden();
        }
    }

    /** @test */
    public function testUnauthorizedUsersCannotRestoreAPage()
    {
        foreach (Arr::except($this->permissionProvider(), 'restore') as [$permission]) {
            $page = tap(create(Page::class))->delete();

            $this->signInWithPermission("pages.$permission");

            $this->restoreResource('pages', $page->id);
            // Nova doesn't return 403 on unauthorized restore request, so we don't check the status.
            // Beware: with a random user, it would return a 403 because of the viewAll authorization.
            // But with any permission, it returns a 200.

            $this->assertSoftDeleted('pages', ['id' => $page->id]);
        }
    }

    /** @test */
    public function testUnauthorizedUsersCannotForceDeleteAPage()
    {
        foreach (Arr::except($this->permissionProvider(), 'forceDelete') as [$permission]) {
            $page = create(Page::class);

            $this->signInWithPermission("pages.$permission");

            $this->forceDeleteResource('pages', $page->id);
            // Nova doesn't return 403 on unauthorized force delete request, so we don't check the status.
            // Beware: with a random user, it would return a 403 because of the viewAll authorization.
            // But with any permission, it returns a 200.

            $this->assertDatabaseHas('pages', ['id' => $page->id]);
        }
    }

    /** @test */
    public function testAuthorizedUsersCanCreateAPage()
    {
        $this->signInWithPermission('pages.create');

        $this->storePage($data = make(Page::class)->toArray())
            ->assertJsonMissingValidationErrors()
            ->assertCreated();

        $this->assertDatabaseHas('pages', $data);
    }

    /** @test */
    public function testAuthorizedUsersCanUpdateAPage()
    {
        $this->signInWithPermission('pages.edit');

        $this->updatePage($data = make(Page::class)->toArray())
            ->assertJsonMissingValidationErrors()
            ->assertOk();

        $this->assertDatabaseHas('pages', $data);
    }

    /** @test */
    public function testAuthorizedUsersCanSoftDeleteAPage()
    {
        $this->signInWithPermission('pages.delete');

        $page = create(Page::class);

        $this->deleteResource('pages', $page->id)
            ->assertOk();

        $this->assertSoftDeleted('pages', ['id' => $page->id]);
    }

    /** @test */
    public function testAuthorizedUsersCanViewADeletedPage()
    {
        $this->signInWithPermission('pages.viewDeleted');

        $page = tap(create(Page::class))->delete();

        $this->showResource('pages', $page->id)
            ->assertOk();
    }

    /** @test */
    public function testAuthorizedUsersCanRestoreAPage()
    {
        $this->signInWithPermission('pages.restore');

        $page = tap(create(Page::class))->delete();

        $this->assertSoftDeleted('pages', ['id' => $page->id]);

        $this->restoreResource('pages', $page->id)
            ->assertOk();

        $this->assertDatabaseHas('pages', ['id' => $page->id, 'deleted_at' => null]);
    }

    /** @test */
    public function testAuthorizedUsersCanForceDeleteAPage()
    {
        $this->signInWithPermission('pages.forceDelete');

        $page = create(Page::class);

        $this->forceDeleteResource('pages', $page->id)
            ->assertOk();

        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testTitleIsRequired($verb, $permission)
    {
        $this->signInWithPermission("pages.$permission");

        $this->{$verb . 'Page'}(['title' => null])
            ->assertJsonValidationErrors('title');
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testTitleCannotBeLongerThan255Characters($verb, $permission)
    {
        $this->signInWithPermission("pages.$permission");

        $this->{$verb . 'Page'}(['title' => str_repeat('*', 256)])
            ->assertJsonValidationErrors('title');
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testPathIsRequired($verb, $permission)
    {
        $this->signInWithPermission("pages.$permission");

        $this->{$verb . 'Page'}(['path' => null])
            ->assertJsonValidationErrors('path');
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testPathMustBeUnique($verb, $permission)
    {
        $this->signInWithPermission("pages.$permission");

        create(Page::class, ['path' => 'foo/bar']);

        $this->{$verb . 'Page'}(['path' => 'foo/bar'])
            ->assertJsonValidationErrors('path');
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testPathCannotBeLongerThan255Characters($verb, $permission)
    {
        $this->signInWithPermission("pages.$permission");

        $this->{$verb . 'Page'}(['path' => str_repeat('a', 256)])
            ->assertJsonValidationErrors('path');
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testPathCannotStartWithSlash($verb, $permission)
    {
        $this->signInWithPermission("pages.$permission");

        $this->{$verb . 'Page'}(['path' => '/foo'])
            ->assertJsonValidationErrors('path');
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testPathCannotEndWithSlash($verb, $permission)
    {
        $this->signInWithPermission("pages.$permission");

        $this->{$verb . 'Page'}(['path' => 'foo/'])
            ->assertJsonValidationErrors('path');
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testPathCannotHaveDoubleSlashes($verb, $permission)
    {
        $this->signInWithPermission("pages.$permission");

        $this->{$verb . 'Page'}(['path' => 'foo//bar'])
            ->assertJsonValidationErrors('path');
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testRestrictedMustBeBoolean($verb, $permission)
    {
        $this->signInWithPermission("pages.$permission");

        $this->{$verb . 'Page'}(['restricted' => 'foo'])
            ->assertJsonValidationErrors('restricted');
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testPublishedAtCanBeNull($verb, $permission)
    {
        $this->signInWithPermission("pages.$permission");

        $this->{$verb . 'Page'}(['published_at' => null])
            ->assertJsonMissingValidationErrors('published_at');
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testPublishedAtMustBeDateTimeOfAppropriateFormat($verb, $permission)
    {
        $this->signInWithPermission("pages.$permission");

        $this->{$verb . 'Page'}(['published_at' => 'foo'])
            ->assertJsonValidationErrors('published_at');

        $this->{$verb . 'Page'}(['published_at' => '22:00 14/06/2020'])
            ->assertJsonValidationErrors('published_at');

        $this->{$verb . 'Page'}(['published_at' => '2020-07-14 22:00:00'])
            ->assertJsonMissingValidationErrors('published_at');
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testBodyIsRequired($verb, $permission)
    {
        $this->signInWithPermission("pages.$permission");

        $this->{$verb . 'Page'}(['body' => null])
            ->assertJsonValidationErrors('body');
    }

    /**
     * @test
     * @dataProvider modeProvider
     */
    public function testBodyCannotBeLongerThan65535Characters($verb, $permission)
    {
        $this->signInWithPermission("pages.$permission");

        $this->{$verb . 'Page'}(['body' => str_repeat('*', 65536)])
            ->assertJsonValidationErrors('body');
    }

    /**
     * Send a POST request to create a page.
     *
     * @param  array  $overrides
     * @return \Illuminate\Testing\TestResponse
     */
    public function storePage(array $overrides = [])
    {
        return $this->storeResource('pages', array_merge(
            make(Page::class)->toArray(),
            $overrides
        ));
    }

    /**
     * Send a PUT request to update an existing page.
     *
     * @param  array  $data
     * @param  \App\Page|null  $page
     * @return \Illuminate\Testing\TestResponse
     */
    public function updatePage(array $data = [], Page $page = null)
    {
        $page = $page ?? create(Page::class);

        return $this->updateResource(
            'pages', $page->id,
            array_merge($page->toArray(), $data)
        );
    }
}
