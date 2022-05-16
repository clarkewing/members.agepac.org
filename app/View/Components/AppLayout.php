<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AppLayout extends Component
{
    /**
     * Whether the content should have vertical padding.
     *
     * @var bool
     */
    public bool $withContentPadding;

    /**
     * Create the component instance.
     *
     * @param $noPadding
     */
    public function __construct(bool $noPadding = false)
    {
        $this->withContentPadding = ! $noPadding;
    }

    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('layouts.app');
    }
}
