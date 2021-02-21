<?php

namespace Tests\Unit;

use App\Models\Attachment;
use App\Models\Channel;
use App\Models\Company;
use App\Models\Poll;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class NamedRouteTest extends TestCase
{
    /* @test */
    public function testHome()
    {
        $this->assertRoutePathIs('/home', 'home');
    }

    /* @test */
    public function testThreadIndex()
    {
        $this->assertRoutePathIs('/threads', 'threads.index');
    }

    /* @test */
    public function testThreadIndexWithChannel()
    {
        $channel = Channel::factory()->make();

        $this->assertRoutePathIs("/threads/{$channel->slug}", 'threads.index', $channel);
    }

    /* @test */
    public function testThreadCreate()
    {
        $this->assertRoutePathIs('/threads/create', 'threads.create');
    }

    /* @test */
    public function testThreadStore()
    {
        $this->assertRoutePathIs('/threads', 'threads.store');
    }

    /* @test */
    public function testThreadSearch()
    {
        $this->assertRoutePathIs('/threads/search', 'threads.search');
    }

    /* @test */
    public function testThreadShow()
    {
        $thread = Thread::factory()->create(); // Create required to generate slug

        $this->assertRoutePathIs(
            "/threads/{$thread->channel->slug}/{$thread->slug}",
            'threads.show', [$thread->channel, $thread]
        );
    }

    /* @test */
    public function testThreadDestroy()
    {
        $thread = Thread::factory()->create();

        $this->assertRoutePathIs(
            "/threads/{$thread->channel->slug}/{$thread->slug}",
            'threads.destroy', [$thread->channel, $thread]
        );
    }

    /* @test */
    public function testPostsIndex()
    {
        $thread = Thread::factory()->create();

        $this->assertRoutePathIs(
            "/threads/{$thread->channel->slug}/{$thread->slug}/posts",
            'posts.index', [$thread->channel, $thread]
        );
    }

    /* @test */
    public function testPostsStore()
    {
        $thread = Thread::factory()->create();

        $this->assertRoutePathIs(
            "/threads/{$thread->channel->slug}/{$thread->slug}/posts",
            'posts.store', [$thread->channel, $thread]
        );
    }

    /* @test */
    public function testPostsUpdate()
    {
        $post = Post::factory()->create();

        $this->assertRoutePathIs(
            "/posts/{$post->id}",
            'posts.update', $post
        );
    }

    /* @test */
    public function testPostsDestroy()
    {
        $post = Post::factory()->create();

        $this->assertRoutePathIs(
            "/posts/{$post->id}",
            'posts.destroy', $post
        );
    }

    /* @test */
    public function testPostsMarkBest()
    {
        $post = Post::factory()->create();

        $this->assertRoutePathIs(
            "/posts/{$post->id}/best",
            'posts.mark_best', $post
        );
    }

    /* @test */
    public function testPostsUnmarkBest()
    {
        $posts = Post::factory()->create();

        $this->assertRoutePathIs(
            "/posts/{$posts->id}/best",
            'posts.unmark_best', $posts
        );
    }

    /* @test */
    public function testPostsFavorite()
    {
        $post = Post::factory()->create();

        $this->assertRoutePathIs(
            "/posts/{$post->id}/favorites",
            'posts.favorite', $post
        );
    }

    /* @test */
    public function testPostsUnfavorite()
    {
        $post = Post::factory()->create();

        $this->assertRoutePathIs(
            "/posts/{$post->id}/favorites",
            'posts.unfavorite', $post
        );
    }

    /* @test */
    public function testAttachmentsStore()
    {
        $this->assertRoutePathIs(
            '/attachments',
            'attachments.store'
        );
    }

    /* @test */
    public function testAttachmentsDestroy()
    {
        $attachment = Attachment::factory()->create();

        $this->assertRoutePathIs(
            "/attachments/{$attachment->id}",
            'attachments.destroy', $attachment
        );
    }

    /* @test */
    public function testPollsShow()
    {
        $poll = Poll::factory()->create();

        $this->assertRoutePathIs(
            "/threads/{$poll->thread->channel->slug}/{$poll->thread->slug}",
            'threads.show', [$poll->thread->channel, $poll->thread]
        );
    }

    /* @test */
    public function testPollsCreate()
    {
        $poll = Poll::factory()->create();

        $this->assertRoutePathIs(
            "/threads/{$poll->thread->channel->slug}/{$poll->thread->slug}/poll/create",
            'polls.create', [$poll->thread->channel, $poll->thread]
        );
    }

    /* @test */
    public function testPollsStore()
    {
        $poll = Poll::factory()->create();

        $this->assertRoutePathIs(
            "/threads/{$poll->thread->channel->slug}/{$poll->thread->slug}/poll",
            'polls.store', [$poll->thread->channel, $poll->thread]
        );
    }

    /* @test */
    public function testPollsUpdate()
    {
        $poll = Poll::factory()->create();

        $this->assertRoutePathIs(
            "/threads/{$poll->thread->channel->slug}/{$poll->thread->slug}/poll",
            'polls.update', [$poll->thread->channel, $poll->thread]
        );
    }

    /* @test */
    public function testPollsDestroy()
    {
        $poll = Poll::factory()->create();

        $this->assertRoutePathIs(
            "/threads/{$poll->thread->channel->slug}/{$poll->thread->slug}/poll",
            'polls.destroy', [$poll->thread->channel, $poll->thread]
        );
    }

    /* @test */
    public function testPollVotesUpdate()
    {
        $poll = Poll::factory()->create();

        $this->assertRoutePathIs(
            "/threads/{$poll->thread->channel->slug}/{$poll->thread->slug}/poll/vote",
            'poll_votes.update', [$poll->thread->channel, $poll->thread]
        );
    }

    /* @test */
    public function testPollResultsShow()
    {
        $poll = Poll::factory()->create();

        $this->assertRoutePathIs(
            "/threads/{$poll->thread->channel->slug}/{$poll->thread->slug}/poll/results",
            'poll_results.show', [$poll->thread->channel, $poll->thread]
        );
    }

    /* @test */
    public function testProfilesIndex()
    {
        $this->assertRoutePathIs(
            '/profiles',
            'profiles.index'
        );
    }

    /* @test */
    public function testProfilesShow()
    {
        $user = User::factory()->make();

        $this->assertRoutePathIs(
            '/profiles/' . $user->username,
            'profiles.show', $user
        );
    }

    /* @test */
    public function testProfilesUpdate()
    {
        $user = User::factory()->make();

        $this->assertRoutePathIs(
            '/profiles/' . $user->username,
            'profiles.update', $user
        );
    }

    /* @test */
    public function testCompaniesIndex()
    {
        $this->assertRoutePathIs(
            '/companies',
            'companies.index'
        );
    }

    /* @test */
    public function testCompaniesStore()
    {
        $this->assertRoutePathIs(
            '/companies',
            'companies.store'
        );
    }

    /* @test */
    public function testCompaniesShow()
    {
        $company = Company::factory()->create();

        $this->assertRoutePathIs(
            '/companies/' . $company->slug,
            'companies.show', $company
        );
    }

    /* @test */
    public function testCompaniesUpdate()
    {
        $company = Company::factory()->create();

        $this->assertRoutePathIs(
            '/companies/' . $company->slug,
            'companies.update', $company
        );
    }

    /** @test */
    public function testOccupationsStore()
    {
        $this->assertRoutePathIs(
            '/occupations',
            'occupations.store'
        );
    }

    /** @test */
    public function testOccupationsUpdate()
    {
        $this->assertRoutePathIs(
            '/occupations/1',
            'occupations.update', 1
        );
    }

    /** @test */
    public function testOccupationsDestroy()
    {
        $this->assertRoutePathIs(
            '/occupations/1',
            'occupations.destroy', 1
        );
    }

    /** @test */
    public function testCoursesStore()
    {
        $this->assertRoutePathIs(
            '/courses',
            'courses.store'
        );
    }

    /** @test */
    public function testCoursesUpdate()
    {
        $this->assertRoutePathIs(
            '/courses/1',
            'courses.update', 1
        );
    }

    /** @test */
    public function testCoursesDestroy()
    {
        $this->assertRoutePathIs(
            '/courses/1',
            'courses.destroy', 1
        );
    }

    /* @test */
    public function testNotificationsIndex()
    {
        $this->assertRoutePathIs('/notifications', 'notifications.index');
    }

    /* @test */
    public function testNotificationsDestroy()
    {
        $notificationId = Str::orderedUuid();

        $this->assertRoutePathIs(
            "/notifications/{$notificationId}",
            'notifications.destroy', $notificationId
        );
    }

    /* @test */
    public function testApiUsersIndex()
    {
        $this->assertRoutePathIs('/api/users', 'api.users.index');
    }

    /* @test */
    public function testApiUsersAvatarStore()
    {
        $user = User::factory()->make();

        $this->assertRoutePathIs(
            '/api/users/' . $user->username . '/avatar',
            'api.users.avatar.store', $user
        );
    }

    /* @test */
    public function testApiTagsIndex()
    {
        $this->assertRoutePathIs('/api/tags', 'api.tags.index');

        $this->assertRoutePathIs('/api/tags/foobar', 'api.tags.index', ['type' => 'foobar']);
    }

    /** @test */
    public function testAccountEdit()
    {
        $this->assertRoutePathIs(
            '/account/info',
            'account.edit'
        );
    }

    /** @test */
    public function testAccountUpdate()
    {
        $this->assertRoutePathIs(
            '/account/info',
            'account.update'
        );
    }

    /** @test */
    public function testAccountSubscriptionStore()
    {
        $this->assertRoutePathIs(
            '/account/subscription',
            'subscription.store'
        );
    }

    /** @test */
    public function testAccountSubscriptionEdit()
    {
        $this->assertRoutePathIs(
            '/account/subscription',
            'subscription.edit'
        );
    }

    /** @test */
    public function testAccountSubscriptionUpdate()
    {
        $this->assertRoutePathIs(
            '/account/subscription',
            'subscription.update'
        );
    }

    /** @test */
    public function testAccountSubscriptionInvoicesShow()
    {
        $this->assertRoutePathIs(
            '/account/subscription/invoice/thisIsTheInvoiceId',
            'subscription.invoices.show', 'thisIsTheInvoiceId'
        );
    }

    /** @test */
    public function testAccountSubscriptionPaymentMethodsCreate()
    {
        $this->assertRoutePathIs(
            '/account/subscription/payment-methods/create',
            'subscription.payment-methods.create'
        );
    }

    /** @test */
    public function testAccountSubscriptionPaymentMethodsStore()
    {
        $this->assertRoutePathIs(
            '/account/subscription/payment-methods',
            'subscription.payment-methods.store'
        );
    }

    /** @test */
    public function testAccountSubscriptionPaymentMethodsUpdate()
    {
        $paymentMethodId = 'pm_foobar';

        $this->assertRoutePathIs(
            "/account/subscription/payment-methods/{$paymentMethodId}",
            'subscription.payment-methods.update', $paymentMethodId
        );
    }

    /** @test */
    public function testAccountSubscriptionPaymentMethodsDestroy()
    {
        $paymentMethodId = 'pm_foobar';

        $this->assertRoutePathIs(
            "/account/subscription/payment-methods/{$paymentMethodId}",
            'subscription.payment-methods.destroy', $paymentMethodId
        );
    }

    public function assertRoutePathIs(string $expectedPath, string $routeName, $routeParameters = null)
    {
        $this->assertEquals(
            config('app.url') . $expectedPath,
            route($routeName, $routeParameters)
        );
    }
}
