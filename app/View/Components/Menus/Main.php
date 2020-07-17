<?php

namespace App\View\Components\Menus;

use Illuminate\View\Component;
use Spatie\Menu\Item;
use Spatie\Menu\Laravel\Html;
use Spatie\Menu\Laravel\Link;
use Spatie\Menu\Laravel\Menu;

class Main extends Component
{
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        $nav = Menu::new();

        if (is_null(nova_get_menu('main'))) {
            return '';
        }

        nova_get_menu('main')['menuItems']
            ->each(function ($menuItem) use ($nav) {
                $this->addItem($nav, $menuItem);
            });

        return $nav
            ->addClass('navbar-nav')
            ->addItemParentClass('nav-item')
            ->each(function (Link $link) {
                $link->addClass('nav-link');
            })
            ->render();
    }

    /**
     * Add an item to the menu.
     *
     * @param  $menu
     * @param  $menuItem
     * @param  bool  $isSubmenu
     */
    protected function addItem(&$menu, $menuItem, bool $isSubmenu = false): void
    {
        if (! $menuItem['enabled']) {
            return; // Don't add disabled items.
        }

        if ($this->isSubmenuHeader($menuItem)) {
            $this->addSubmenu($menu, $menuItem);

            return;
        }

        $menu->add($this->renderItem($menuItem, $isSubmenu));
    }

    /**
     * Determine if the passed menu item is a submenu header.
     *
     * @param  $menuItem
     * @return bool
     */
    protected function isSubmenuHeader($menuItem): bool
    {
        return ! $menuItem['children']->isEmpty();
    }

    /**
     * Add a submenu to the menu.
     *
     * @param  $menu
     * @param  $menuItem
     */
    protected function addSubmenu($menu, $menuItem): void
    {
        $menu->submenu(
            $this->renderSubmenuHeader($menuItem),
            $this->renderSubmenuMenu($menuItem['children'])
        );
    }

    /**
     * Render the header for the submenu.
     *
     * @param  $menuItem
     * @return \Spatie\Menu\Laravel\Link
     */
    protected function renderSubmenuHeader($menuItem): Link
    {
        $navItem = $this->renderItem($menuItem);

        return ($navItem instanceof Link ? $navItem : Link::to('#', $menuItem['name']))
            ->addClass('nav-link dropdown-toggle')
            ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']);
    }

    /**
     * Render a menu item.
     *
     * @param  $menuItem
     * @param  bool  $isSubmenu
     * @return \Spatie\Menu\Item
     */
    protected function renderItem($menuItem, bool $isSubmenu = false): Item
    {
        switch ($menuItem['type']) {
            case 'text':
                $navItem = $isSubmenu
                    ? Html::raw('<h6 class="dropdown-header">' . $menuItem['name'] . '</h6>')
                    : Html::raw('<span class="navbar-text">' . $menuItem['name'] . '</span>');
                break;

            case 'separator':
                $navItem = $isSubmenu ? '' : Html::raw('<div class="dropdown-divider"></div>');
                break;

            case 'route':
                $navItem = Link::toRoute($menuItem['value']['route'], $menuItem['name'], $menuItem['value']['params']);
                break;

            case 'page':
                $navItem = Link::toRoute('pages.show', $menuItem['name'], ['page' => $menuItem['value']]);
                break;

            case 'static-url':
            default:
                $navItem = Link::toUrl($menuItem['value'], $menuItem['name'], $menuItem['parameters'] ?? []);
                break;
        }

        if ($navItem instanceof Link && ! is_null($menuItem['target'])) {
            $navItem->setAttribute('target', $menuItem['target']);
        }

        return $navItem;
    }

    /**
     * Render the submenu content.
     *
     * @param  $menuItems
     * @return \Spatie\Menu\Laravel\Menu
     */
    protected function renderSubmenuMenu($menuItems): Menu
    {
        $subNav = Menu::new();

        $menuItems->each(function ($menuItem) use ($subNav) {
            $this->addItem($subNav, $menuItem, true);
        });

        return $subNav
            ->addParentClass('dropdown')
            ->setWrapperTag('div')
            ->addClass('dropdown-menu')
            ->withoutParentTag()
            ->addItemClass('dropdown-item');
    }
}
