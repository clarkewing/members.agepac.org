<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AuthFormLayout extends Component
{
    public ?string $title;

    public function __construct(string $title = null)
    {
        $this->title = $title;
    }

    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\Contracts\View\Factory
     */
    public function render()
    {
        return view('layouts.auth-form');
    }
}
