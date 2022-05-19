<?php

namespace App\View\Components;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class NewMembersCard extends Component
{
    public $newUsers;

    public function __construct(int $count = 4)
    {
        $this->newUsers = Cache::rememberForever(
            'new-users',
            fn () => User
                ::approved()
                ->latest('approved_at')
                ->take($count)
                ->get(),
        );
    }

    public function render(): View
    {
        return view('components.new-members-card');
    }
}
