<?php

namespace Tests\Feature;

use App\Events\UserApproved;
use App\Models\User;
use App\View\Components\NewMembersCard;
use Cache;
use Illuminate\Auth\Events\Registered;
use Tests\TestCase;

class NewMembersCardTest extends TestCase
{
    protected $users;

    protected function setUp(): void
    {
        parent::setUp();

        $this->users = User::factory()
            ->count(4)
            ->sequence(fn ($sequence) => ['approved_at' => now()->subHours($sequence->count - $sequence->index)])
            ->create()
            ->sortByDesc('approved_at');
    }

    /** @test */
    public function testShowsLatestNewMembersInOrder()
    {
        $this->component(NewMembersCard::class)
            ->assertSeeTextInOrder(
                $this->users->pluck('name')->all()
            );
    }

    /** @test */
    public function testNewMembersAreCached()
    {
        $this->assertCacheMissing('new-users');

        $this->component(NewMembersCard::class);

        $this->assertCacheHas('new-users');
    }

    /** @test */
    public function testNewMembersAreFlushedWhenUserRegisters()
    {
        $this->initializeCache();

        event(new Registered($this->users->first()));

        $this->assertCacheMissing('new-users');
    }

    /** @test */
    public function testNewMembersAreFlushedWhenUserApproved()
    {
        $this->initializeCache();

        event(new UserApproved($this->users->first()));

        $this->assertCacheMissing('new-users');
    }

    /** @test */
    public function testNewMembersAreFlushedWhenUserDeleted()
    {
        $this->initializeCache();

        $this->users->first()->delete();

        $this->assertCacheMissing('new-users');
    }

    protected function assertCacheHas(string $key): void
    {
        $this->assertTrue(Cache::has($key), "Failed asserting key [$key] stored in cache.");
    }

    protected function assertCacheMissing(string $key): void
    {
        $this->assertFalse(Cache::has($key), "Failed asserting key [$key] missing from cache.");
    }

    protected function initializeCache(): void
    {
        $this->component(NewMembersCard::class);

        $this->assertCacheHas('new-users');
    }
}
