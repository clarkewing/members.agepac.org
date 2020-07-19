<?php

namespace Tests\Feature;

use OptimistDigital\MenuBuilder\Http\Controllers\MenuController;
use OptimistDigital\MenuBuilder\Models\Menu;
use Tests\NovaTestRequests;
use Tests\TestCase;

class ManageMenusTest extends TestCase
{
    use NovaTestRequests;

    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling()->signInGod();
    }

    /** @test */
    public function testCanCreateNewMenu()
    {
        $this->storeResource('nova-menu', $data = [
            'name' => 'Foo navigation',
            'slug' => 'foo',
            'locale' => 'fr_FR',
        ]);

        $this->assertDatabaseHas('menus', $data);
        $this->assertEquals('Foo navigation', nova_get_menu('foo')['name']);
    }

    /** @test */
    public function testCanEditExistingMenu()
    {
        $menu = Menu::forceCreate([
            'name' => 'Foo navigation bar',
            'slug' => 'foo',
            'locale' => 'fr_FR',
        ]);

        $this->updateResource('nova-menu', $menu->id, $data = [
            'name' => 'Bar navigation',
            'slug' => 'bar',
            'locale' => 'fr_FR',
        ]);

        $this->assertDatabaseMissing('menus', $menu->toArray());
        $this->assertNull(nova_get_menu('foo'));

        $this->assertDatabaseHas('menus', ['id' => $menu->id] + $data);
        $this->assertEquals('Bar navigation', nova_get_menu('bar')['name']);
    }

    /** @test */
    public function testCanAddMenuItem()
    {
        $menu = Menu::forceCreate([
            'name' => 'Foo navigation bar',
            'slug' => 'foo',
            'locale' => 'fr_FR',
        ]);

        $this->assertEmpty(nova_get_menu('foo')['menuItems']);

        $this->postJson(
            action([MenuController::class, 'createMenuItem']),
            $data = [
                'menu_id' => $menu->id,
                'class' => 'OptimistDigital\MenuBuilder\Classes\MenuItemStaticURL',
                'enabled' => true,
                'name' => 'Foo Item',
                'value' => '/foo',
                'target' => '_self',
            ]
        )->assertSuccessful();

        $this->assertDatabaseHas('menu_items', $data);
        $this->assertCount(1, nova_get_menu('foo')['menuItems']);
    }

    // TODO: Add tests for menu Blade components (waiting for Laravel 8.0 TestView)
}
