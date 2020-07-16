<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use OptimistDigital\MenuBuilder\Models\Menu;
use OptimistDigital\MenuBuilder\Models\MenuItem;

class AddStarterMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $mainNavbar = Menu::forceCreate([
            'name' => 'Main navigation bar',
            'slug' => 'main',
            'locale' => 'fr_FR',
        ]);

        MenuItem::forceCreate([
            'menu_id' => $mainNavbar->id,
            'name' => 'Réseau',
            'class' => 'OptimistDigital\MenuBuilder\Classes\MenuItemText',
            'parameters' => null,
            'order' => 1,
        ]);

        MenuItem::forceCreate([
            'menu_id' => $mainNavbar->id,
            'name' => 'Annuaire des EPL',
            'class' => 'App\Nova\MenuBuilder\MenuRoute',
            'value' => 'profiles.index',
            'parameters' => null,
            'parent_id' => 1,
            'order' => 1,
        ]);

        MenuItem::forceCreate([
            'menu_id' => $mainNavbar->id,
            'name' => 'Annuaire des Compagnies',
            'class' => 'App\Nova\MenuBuilder\MenuRoute',
            'value' => 'companies.index',
            'parameters' => null,
            'parent_id' => 1,
            'order' => 2,
        ]);

        MenuItem::forceCreate([
            'menu_id' => $mainNavbar->id,
            'name' => 'Forum',
            'class' => 'App\Nova\MenuBuilder\MenuRoute',
            'value' => 'threads.index',
            'parameters' => null,
            'order' => 2,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Menu::truncate();
        MenuItem::truncate();

        Schema::enableForeignKeyConstraints();
    }
}
