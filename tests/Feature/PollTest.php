<?php

namespace Tests\Feature;

use App\Poll;
use App\PollOption;
use App\Thread;
use App\User;
use Illuminate\Support\Arr;
use Tests\TestCase;

class PollTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();
    }

    /** @test */
    public function testOnlyThreadCreatorCanAttachPollToThread()
    {
        $thread = create(Thread::class);

        $this->actingAs(create(User::class)) // Random user
            ->attachPollToThread($thread)
            ->assertForbidden();

        $this->actingAs($thread->creator)
            ->attachPollToThread($thread)
            ->assertCreated();
    }

    /** @test */
    public function testPollMustHaveAtLeastTwoOptions()
    {
        $thread = create(Thread::class);

        $this->signIn($thread->creator);

        $this->attachPollToThread($thread, ['options' => null])
            ->assertJsonValidationErrors('options');

        $this->attachPollToThread($thread, ['options' => [
            ['label' => 'Option 1'],
        ]])
            ->assertJsonValidationErrors('options');

        $this->attachPollToThread($thread, ['options' => [
            ['label' => 'Option 1'],
            ['label' => 'Option 2'],
        ]])
            ->assertJsonMissingValidationErrors('options');
    }

        $poll = $this->post(route('polls.store', ['channel' => $channel, 'thread' => $thread]), $pollArr)->original;
        $pollOption = $this->get(route('poll_options.index', ['channel' => $channel, 'thread' => $thread, 'poll' => $poll]))->original;
        $this->delete(route('poll_options.destroy', ['pollOption' => $pollOption[0]->id]))->assertStatus(403);
    }

    public function testOnlyPollCreatorCanEditPoll()
    {
        $john = create(User::class, ['username' => 'john.doe']);
        $jane = create(User::class, ['username' => 'jane.doe']);
        $thread = create(Thread::class, ['user_id' => $john->id]);
        $poll = make(Poll::class, ['thread_id' => $thread->id]);
        $poll->option_labels = ['Option 1', 'Option 2', 'Option 3'];
        $poll->option_colors = ['#ffffff', '#ffffff', '#ffffff'];
        $pollArr = json_decode(json_encode($poll), true);

        $channel = $thread->channel;

        $poll = $this->actingAs($john)->post(route('polls.store', ['channel' => $channel, 'thread' => $thread]), $pollArr)->original;

        $this->actingAs($john)->put(route('polls.update', ['poll' => $poll]), $pollArr)->assertStatus(200);
        $this->withExceptionHandling()->actingAs($jane)->put(route('polls.update', ['poll' => $poll]), $pollArr)->assertStatus(403);
    }

    public function testPollCreatorCanChooseIfUsersCanChangeTheirVote()
    {
        $creator = create(User::class);
        $user = create(User::class);
        $thread = create(Thread::class, ['user_id' => $creator->id]);
        $poll = make(Poll::class, ['thread_id' => $thread->id, 'votes_editable' => true]);

        $poll->option_labels = ['Option 1', 'Option 2'];
        $poll->option_colors = ['#ffffff', '#ffffff'];
        $pollArr = json_decode(json_encode($poll), true);

        $channel = $thread->channel;

        $poll = $this->actingAs($creator)->postJson(route('polls.store', ['channel' => $channel, 'thread' => $thread]), $pollArr)->original;

        $pollOption = $this->actingAs($user)->get(route('poll_options.index', ['channel' => $channel, 'thread' => $thread, 'poll' => $poll]))->original[0];

        $pollVote = $this->actingAs($user)->postJson(route('poll_votes.store', ['poll' => $poll, 'pollOption' => $pollOption]))->original;
        $this->withExceptionHandling()->actingAs($user)->deleteJson(route('poll_votes.destroy', ['pollVote' => $pollVote]))->assertStatus(200);

        $this->withExceptionHandling()->actingAs($creator)->putJson(route('polls.update', ['poll' => $poll]), ['votes_editable' => false])->assertStatus(200);
        $pollVote = $this->actingAs($user)->postJson(route('poll_votes.store', ['poll' => $poll, 'pollOption' => $pollOption]))->original;
        $this->withExceptionHandling()->actingAs($user)->deleteJson(route('poll_votes.destroy', ['pollVote' => $pollVote]))->assertStatus(403);
    }

    public function testPollCreatorCanSelectNumberOfVotesPerUser()
    {
        $creator = create(User::class);
        $user = create(User::class);
        $thread = create(Thread::class, ['user_id' => $creator->id]);
        $poll = make(Poll::class, ['thread_id' => $thread->id, 'max_votes' => 1, 'votes_editable' => true]);

        $poll->option_labels = ['Option 1', 'Option 2', 'Option 3'];
        $poll->option_colors = ['#ffffff', '#ffffff', '#ffffff'];
        $pollArr = json_decode(json_encode($poll), true);

        $channel = $thread->channel;

        $poll = $this->actingAs($creator)->postJson(route('polls.store', ['channel' => $channel, 'thread' => $thread]), $pollArr)->original;

        $pollOptions = $this->actingAs($user)->get(route('poll_options.index', ['channel' => $channel, 'thread' => $thread, 'poll' => $poll]))->original;

        $pollVote = $this->actingAs($user)->postJson(route('poll_votes.store', ['poll' => $poll, 'pollOption' => $pollOptions[0]]))->assertStatus(201)->original;
        $this->withExceptionHandling()->actingAs($user)->postJson(route('poll_votes.store', ['poll' => $poll, 'pollOption' => $pollOptions[1]]))->assertStatus(403);

        $this->withExceptionHandling()->actingAs($user)->deleteJson(route('poll_votes.destroy', ['pollVote' => $pollVote]))->assertStatus(200);
        $this->withExceptionHandling()->actingAs($creator)->putJson(route('polls.update', ['poll' => $poll]), ['max_votes' => 2])->assertStatus(200);

        $this->withExceptionHandling()->actingAs($user)->postJson(route('poll_votes.store', ['poll' => $poll, 'pollOption' => $pollOptions[0]]))->assertStatus(201);
        $this->withExceptionHandling()->actingAs($user)->postJson(route('poll_votes.store', ['poll' => $poll, 'pollOption' => $pollOptions[1]]))->assertStatus(201);
    }

    public function testVotePrivacy()
    {
        $creator = create(User::class);
        $user = create(User::class);
        $thread_none = create(Thread::class, ['user_id' => $creator->id]);
        $thread_author = create(Thread::class, ['user_id' => $creator->id]);
        $thread_all = create(Thread::class, ['user_id' => $creator->id]);
        $poll_none = make(Poll::class, ['thread_id' => $thread_none->id, 'votes_privacy' => 0]);
        $poll_author = make(Poll::class, ['thread_id' => $thread_author->id, 'votes_privacy' => 1]);
        $poll_all = make(Poll::class, ['thread_id' => $thread_all->id, 'votes_privacy' => 2]);

        $poll_none->option_labels = ['Option 1', 'Option 2'];
        $poll_author->option_labels = ['Option 1', 'Option 2'];
        $poll_all->option_labels = ['Option 1', 'Option 2'];
        $poll_none->option_colors = ['#ffffff', '#ffffff'];
        $poll_author->option_colors = ['#ffffff', '#ffffff'];
        $poll_all->option_colors = ['#ffffff', '#ffffff'];
        $pollArr_none = json_decode(json_encode($poll_none), true);
        $pollArr_author = json_decode(json_encode($poll_author), true);
        $pollArr_all = json_decode(json_encode($poll_all), true);

        $channel_none = $thread_none->channel;
        $channel_author = $thread_author->channel;
        $channel_all = $thread_all->channel;

        $poll_none = $this->actingAs($creator)->postJson(route('polls.store', ['channel' => $channel_none, 'thread' => $thread_none]), $pollArr_none)->original;
        $poll_author = $this->actingAs($creator)->postJson(route('polls.store', ['channel' => $channel_author, 'thread' => $thread_author]), $pollArr_author)->original;
        $poll_all = $this->actingAs($creator)->postJson(route('polls.store', ['channel' => $channel_all, 'thread' => $thread_all]), $pollArr_all)->original;

        $pollOption_none = $this->actingAs($user)->get(route('poll_options.index', ['channel' => $channel_none, 'thread' => $thread_none, 'poll' => $poll_none]))->original[0];
        $pollOption_author = $this->actingAs($user)->get(route('poll_options.index', ['channel' => $channel_author, 'thread' => $thread_author, 'poll' => $poll_author]))->original[0];
        $pollOption_all = $this->actingAs($user)->get(route('poll_options.index', ['channel' => $channel_all, 'thread' => $thread_all, 'poll' => $poll_all]))->original[0];

        $this->actingAs($user)->postJson(route('poll_votes.store', ['poll' => $poll_none, 'pollOption' => $pollOption_none]));
        $this->actingAs($user)->postJson(route('poll_votes.store', ['poll' => $poll_author, 'pollOption' => $pollOption_author]));
        $this->actingAs($user)->postJson(route('poll_votes.store', ['poll' => $poll_all, 'pollOption' => $pollOption_all]));

        $this->withExceptionHandling()->actingAs($user)->get(route('poll_votes.index', ['channel' => $channel_none, 'thread' => $thread_none, 'poll' => $poll_none]))->assertStatus(403);
        $this->withExceptionHandling()->actingAs($user)->get(route('poll.results', ['channel' => $channel_none, 'thread' => $thread_none, 'poll' => $poll_none]))->assertStatus(403);
        $this->withExceptionHandling()->actingAs($user)->get(route('poll_votes.index', ['channel' => $channel_author, 'thread' => $thread_author, 'poll' => $poll_author]))->assertStatus(403);
        $this->withExceptionHandling()->actingAs($user)->get(route('poll.results', ['channel' => $channel_author, 'thread' => $thread_author, 'poll' => $poll_author]))->assertStatus(403);
        $this->withExceptionHandling()->actingAs($user)->get(route('poll_votes.index', ['channel' => $channel_all, 'thread' => $thread_all, 'poll' => $poll_all]))->assertStatus(200);
        $this->withExceptionHandling()->actingAs($user)->get(route('poll.results', ['channel' => $channel_all, 'thread' => $thread_all, 'poll' => $poll_all]))->assertStatus(200);

        $this->withExceptionHandling()->actingAs($creator)->get(route('poll_votes.index', ['channel' => $channel_none, 'thread' => $thread_none, 'poll' => $poll_none]))->assertStatus(403);
        $this->withExceptionHandling()->actingAs($creator)->get(route('poll.results', ['channel' => $channel_none, 'thread' => $thread_none, 'poll' => $poll_none]))->assertStatus(403);
        $this->withExceptionHandling()->actingAs($creator)->get(route('poll_votes.index', ['channel' => $channel_author, 'thread' => $thread_author, 'poll' => $poll_author]))->assertStatus(200);
        $this->withExceptionHandling()->actingAs($creator)->get(route('poll.results', ['channel' => $channel_author, 'thread' => $thread_author, 'poll' => $poll_author]))->assertStatus(200);
        $this->withExceptionHandling()->actingAs($creator)->get(route('poll_votes.index', ['channel' => $channel_all, 'thread' => $thread_all, 'poll' => $poll_all]))->assertStatus(200);
        $this->withExceptionHandling()->actingAs($creator)->get(route('poll.results', ['channel' => $channel_all, 'thread' => $thread_all, 'poll' => $poll_all]))->assertStatus(200);
    }

    public function testLock()
    {
        $creator = create(User::class);
        $user = create(User::class);
        $thread_locked = create(Thread::class, ['user_id' => $creator->id]);
        $thread_locked_with_date = create(Thread::class, ['user_id' => $creator->id]);
        $poll_locked = make(Poll::class, ['thread_id' => $thread_locked->id]);

        $poll_locked->option_labels = ['Option 1', 'Option 2'];
        $poll_locked->option_colors = ['#ffffff', '#ffffff'];
        $pollArr_locked = json_decode(json_encode($poll_locked), true);

        $channel_locked = $thread_locked->channel;

        $poll_locked = $this->actingAs($creator)->postJson(route('polls.store', ['channel' => $channel_locked, 'thread' => $thread_locked]), $pollArr_locked)->original;

        $this->actingAs($creator)->postJson(route('polls.lock', ['poll' => $poll_locked]))->assertStatus(200);

        $pollOption_locked = $this->actingAs($user)->get(route('poll_options.index', ['channel' => $channel_locked, 'thread' => $thread_locked, 'poll' => $poll_locked]))->original[0];

        $this->withExceptionHandling()->actingAs($user)->postJson(route('poll_votes.store', ['poll' => $poll_locked, 'pollOption' => $pollOption_locked]))->assertStatus(403);
    }

    /**
     * Send post request to attach poll to thread.
     *
     * @param  \App\Thread  $thread
     * @param  array  $data
     * @return \Illuminate\Testing\TestResponse
     */
    protected function attachPollToThread(Thread $thread, array $data = [])
    {
        return $this->postJson(
            route('polls.store', ['channel' => $thread->channel, 'thread' => $thread]),
            array_merge(
                Arr::except(make(Poll::class)->toArray(), 'thread_id'),
                ['options' => make(PollOption::class, ['poll_id' => null], random_int(2, 10))->toArray()],
                $data,
            )
        );
    }
}
