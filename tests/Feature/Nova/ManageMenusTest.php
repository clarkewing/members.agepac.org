<?php

namespace Tests\Feature\Nova;

use Illuminate\Support\Str;
use OptimistDigital\MenuBuilder\Http\Controllers\MenuController;
use OptimistDigital\MenuBuilder\Models\Menu;
use OptimistDigital\MenuBuilder\Models\MenuItem;
use Tests\NovaTestRequests;
use Tests\TestCase;

class ManageMenusTest extends TestCase
{
    use NovaTestRequests;

    /** @test */
    public function testUnauthorizedUsersCannotIndexMenus()
    {
        $this->signIn();

        $this->indexResource('nova-menu')
            ->assertForbidden();
    }

    /** @test */
    public function testUnauthorizedUsersCannotViewAMenu()
    {
        $menu = $this->createMenu();

        $this->signIn();

        $this->showResource('nova-menu', $menu->id)
            ->assertForbidden();
    }

    /** @test */
    public function testCreatingAMenuIsForbidden()
    {
        $this->signInWithPermission('menus.manage');

        $this->storeMenu(['name' => 'Fake menu'])
            ->assertForbidden();

        $this->assertDatabaseMissing('menus', ['name' => 'Fake menu']);
    }

    /** @test */
    public function testEditingAMenuIsForbidden()
    {
        $menu = $this->createMenu(['name' => 'Foo menu']);

        $this->signInWithPermission('menus.manage');

        $this->updateMenu(['name' => 'Fake menu'], $menu)
            ->assertForbidden();

        $this->assertEquals('Foo menu', $menu->fresh()->name);
    }

    /** @test */
    public function testDeletingAMenuIsForbidden()
    {
        $menu = $this->createMenu();

        $this->signInWithPermission('menus.manage');

        $this->deleteResource('nova-menu', $menu->id);
        // Nova doesn't return 403 on unauthorized delete request, so we don't check the status.
        // Beware: with a random user, it would return a 403 because of the viewAll authorization.
        // But with any permission, it returns a 200.

        $this->assertDatabaseHas('menus', ['id' => $menu->id]);
    }

    /** @test */
    public function testUnauthorizedUsersCannotAddMenuItems()
    {
        $this->signIn();

        $menuItemsCount = MenuItem::count();

        $this->storeMenuItem()
            ->assertForbidden();

        $this->assertDatabaseCount('menu_items', $menuItemsCount);
    }

    /** @test */
    public function testUnauthorizedUsersCannotEditMenuItems()
    {
        $this->signIn();

        $menuItem = $this->createMenuItem(['name' => 'Foo link']);

        $this->updateMenuItem($menuItem, ['name' => 'Fake link'])
            ->assertForbidden();

        $this->assertEquals('Foo link', $menuItem->fresh()->name);
    }

    /** @test */
    public function testUnauthorizedUsersCannotDeleteMenuItems()
    {
        $this->signIn();

        $menuItem = $this->createMenuItem();

        $this->deleteMenuItem($menuItem)
            ->assertForbidden();

        $this->assertDatabaseHas('menu_items', ['id' => $menuItem->id]);
    }

    /** @test */
    public function testAuthorizedUsersCanAddMenuItems()
    {
        $this->signInWithPermission('menus.manage');

        $menuItemsCount = MenuItem::count();

        $this->storeMenuItem($data = $this->makeMenuItemData())
            ->assertSuccessful();

        $this->assertDatabaseHas('menu_items', $data);
        $this->assertDatabaseCount('menu_items', $menuItemsCount + 1);
    }

    /** @test */
    public function testAuthorizedUsersCanEditMenuItems()
    {
        $this->signInWithPermission('menus.manage');

        $menuItem = $this->createMenuItem();

        $this->updateMenuItem($menuItem, ['name' => 'Foo link'])
            ->assertSuccessful();

        $this->assertEquals('Foo link', $menuItem->fresh()->name);
    }

    /** @test */
    public function testAuthorizedUsersCanDeleteMenuItems()
    {
        $this->signInWithPermission('menus.manage');

        $menuItem = $this->createMenuItem();

        $this->deleteMenuItem($menuItem)
            ->assertSuccessful();

        $this->assertDatabaseMissing('menu_items', ['id' => $menuItem->id]);
    }

    // TODO: Add tests for menu Blade components (waiting for Laravel 8.0 TestView)

    /**
     * Send a POST request to store a new menu.
     *
     * @param  array  $overrides
     * @return \Illuminate\Testing\TestResponse
     */
    protected function storeMenu(array $overrides = []): \Illuminate\Testing\TestResponse
    {
        return $this->storeResource('nova-menu', $this->makeMenuData($overrides));
    }

    /**
     * Send a PUT request to update an existing menu.
     *
     * @param  array  $data
     * @param  \OptimistDigital\MenuBuilder\Models\Menu|null  $menu
     * @return \Illuminate\Testing\TestResponse
     */
    protected function updateMenu(array $data = [], Menu $menu = null): \Illuminate\Testing\TestResponse
    {
        $menu = $menu ?? $this->createMenu();

        return $this->updateResource(
            'nova-menu',
            $menu->id,
            array_merge($menu->toArray(), $data)
        );
    }

    /**
     * Send a POST request to store a new menu item.
     *
     * @param  array  $overrides
     * @return \Illuminate\Testing\TestResponse
     */
    protected function storeMenuItem(array $overrides = []): \Illuminate\Testing\TestResponse
    {
        return $this->postJson(
            action([MenuController::class, 'createMenuItem']),
            array_merge($this->makeMenuItemData(), $overrides)
        );
    }

    /**
     * Send a POST (yes, POST) request to update an existing menu item.
     *
     * @param  \OptimistDigital\MenuBuilder\Models\MenuItem  $menuItem
     * @param  array  $data
     * @return \Illuminate\Testing\TestResponse
     */
    protected function updateMenuItem(MenuItem $menuItem, array $data): \Illuminate\Testing\TestResponse
    {
        return $this->postJson(
            action([MenuController::class, 'updateMenuItem'], ['menuItem' => $menuItem]),
            array_merge($menuItem->toArray(), $data)
        );
    }

    /**
     * Send a DELETE request to delete an existing menu item.
     *
     * @param  \OptimistDigital\MenuBuilder\Models\MenuItem  $menuItem
     * @return \Illuminate\Testing\TestResponse
     */
    protected function deleteMenuItem(MenuItem $menuItem): \Illuminate\Testing\TestResponse
    {
        return $this->deleteJson(
            action([MenuController::class, 'deleteMenuItem'], ['menuItem' => $menuItem])
        );
    }

    /**
     * Return an array of menu attributes.
     *
     * @param  array  $overrides
     * @return array
     */
    protected function makeMenuData(array $overrides = []): array
    {
        $faker = \Faker\Factory::create();

        return array_merge([
            'name' => $name = $faker->sentence(),
            'slug' => Str::slug($name),
            'locale' => 'fr_FR',
        ], $overrides);
    }

    /**
     * Create a new menu instance.
     *
     * @param  array  $overrides
     * @return \OptimistDigital\MenuBuilder\Models\Menu
     */
    protected function createMenu(array $overrides = []): Menu
    {
        return Menu::forceCreate($this->makeMenuData($overrides));
    }

    /**
     * Return an array of menu item attributes.
     *
     * @param  array  $overrides
     * @return array
     */
    protected function makeMenuItemData(array $overrides = []): array
    {
        $faker = \Faker\Factory::create();

        return array_merge([
            'menu_id' => $this->createMenu()->id,
            'class' => 'OptimistDigital\MenuBuilder\Classes\MenuItemStaticURL',
            'enabled' => true,
            'name' => $faker->text(30),
            'value' => $faker->url(),
            'target' => '_self',
        ], $overrides);
    }

    /**
     * Create a new menu item instance.
     *
     * @param  array  $overrides
     * @return \OptimistDigital\MenuBuilder\Models\MenuItem
     */
    protected function createMenuItem(array $overrides = []): MenuItem
    {
        return MenuItem::forceCreate(
            $this->makeMenuItemData(array_merge(['order' => 1], $overrides))
        );
    }
}
