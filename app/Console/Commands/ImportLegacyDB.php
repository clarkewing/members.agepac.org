<?php

namespace App\Console\Commands;

use App\Events\PostCreated;
use App\Events\PostUpdated;
use App\Imports\CompaniesImport;
use App\Imports\CompanyCommentsImport;
use App\Imports\CoursesImport;
use App\Imports\ForumAttachmentsImport;
use App\Imports\ForumChannelsImport;
use App\Imports\ForumPollsImport;
use App\Imports\ForumPollVotesImport;
use App\Imports\ForumPostFavoritesImport;
use App\Imports\ForumPostsImport;
use App\Imports\ForumThreadsImport;
use App\Imports\ForumThreadSubscriptionsImport;
use App\Imports\OccupationsImport;
use App\Imports\OldCompaniesImport;
use App\Imports\ProfileInfoImport;
use App\Imports\SubscriptionsImport;
use App\Imports\UserFieldsImport;
use App\Imports\UsersImport;
use App\Models\Attachment;
use App\Models\Company;
use App\Models\Poll;
use App\Models\Post;
use App\Models\Profile;
use App\Traits\SuppressesEvents;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ImportLegacyDB extends Command
{
    use SuppressesEvents;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legacy:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports data from the legacy site DB CSV exports and download folder.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        Model::unguard();

        $this->section('Users', $this->importUsers());

        $this->section('Companies', $this->importCompanies());

        $this->section('Profiles', $this->importProfiles());

        $this->section('Forum', $this->importForum());

        Model::reguard();

        $this->output->success('Import successful');
    }

    /**
     * Import Users.
     *
     * @return \Closure
     */
    protected function importUsers(): \Closure
    {
        return function () {
            $this->line('Importing Users - Basic info');
            (new UsersImport)->withOutput($this->output)
                ->import($this->csvPath('agepacprzeforum_table_user'));

            $this->line('Importing Users - Additional fields');
            (new UserFieldsImport)->withOutput($this->output)
                ->import($this->csvPath('agepacprzeforum_table_userfield'));

            $this->line('Importing Users - Subscriptions');
            (new SubscriptionsImport)->withOutput($this->output)
                ->import($this->csvPath('agepacprzeforum_table_u_cotisation'));
        };
    }

    /**
     * Import Companies.
     *
     * @return \Closure
     */
    protected function importCompanies(): \Closure
    {
        return function () {
            Company::withoutSyncingToSearch(function () {
                $this->line('Importing Old Companies Implementation - Basic info');
                (new OldCompaniesImport)->withOutput($this->output)
                    ->import($this->csvPath('agepacprzeforum_table_c_airline'));

                $this->line('Importing Old Companies Implementation - Comments');
                (new CompanyCommentsImport)->withOutput($this->output)
                    ->import($this->csvPath('agepacprzeforum_table_c_comment'));

                $this->line('Importing New Companies Implementation');
                (new CompaniesImport)->withOutput($this->output)
                    ->import($this->csvPath('agepacprzeforum_table_fiche_compagnie'));
            });

            $this->line('Indexing Companies');
            $this->call('scout:import', [
                'searchable' => Company::class,
            ]);
        };
    }

    /**
     * Import Profiles.
     *
     * @return \Closure
     */
    protected function importProfiles(): \Closure
    {
        return function () {
            Profile::withoutSyncingToSearch(function () {
                $this->line('Importing Profiles - Bio and flight hours');
                (new ProfileInfoImport)->withOutput($this->output)
                    ->import($this->csvPath('agepacprzeforum_table_u_parcours'));

                $this->line('Importing Profiles - Courses');
                (new CoursesImport)->withOutput($this->output)
                    ->import($this->csvPath('agepacprzeforum_table_u_formation'));

                $this->line('Importing Profiles - Occupations');
                (new OccupationsImport)->withOutput($this->output)
                    ->import($this->csvPath('agepacprzeforum_table_u_emploi'));
            });

            $this->line('Indexing Profiles');
            $this->call('scout:import', [
                'searchable' => Profile::class,
            ]);
        };
    }

    /**
     * Import Forum.
     *
     * @return \Closure
     */
    protected function importForum(): \Closure
    {
        return function () {
            $this->line('Importing Forum - Channels');
            Schema::disableForeignKeyConstraints();
            (new ForumChannelsImport)->withOutput($this->output)
                ->import($this->csvPath('agepacprzeforum_table_forum'));
            Schema::enableForeignKeyConstraints();

            $this->line('Importing Forum - Threads');
            (new ForumThreadsImport)->withOutput($this->output)
                ->import($this->csvPath('agepacprzeforum_table_thread'));

            $this->line('Importing Forum - Thread Subsciptions');
            (new ForumThreadSubscriptionsImport)->withOutput($this->output)
                ->import($this->csvPath('agepacprzeforum_table_subscribethread'));

            $this->line('Importing Forum - Attachments');
            (new ForumAttachmentsImport)->withOutput($this->output)
                ->import($this->csvPath('agepacprzeforum_table_attachment'));

            Post::withoutSyncingToSearch(function () {
                $this->suppressingModelEvents(Post::class, [PostCreated::class, PostUpdated::class], function () {
                    $this->line('Importing Forum - Posts');
                    (new ForumPostsImport)->withOutput($this->output)
                        ->import($this->csvPath('agepacprzeforum_table_post'));

                    $this->line('Importing Forum - Embedding attachments');
                    $this->addUnembeddedAttachmentsToPosts();

                    $this->line('Importing Forum - Setting thread initiators');
                    $this->setThreadInitiators();
                });

                $this->line('Importing Forum - Running PostCreated events');
                Post::all()->each(function ($post) {
                    event(new PostCreated($post));
                });
            });

            $this->line('Importing Forum - Post Favorites');
            (new ForumPostFavoritesImport)->withOutput($this->output)
                ->import($this->csvPath('agepacprzeforum_table_post_thanks'));

            $this->line('Importing Forum - Polls');
            (new ForumPollsImport)->withOutput($this->output)
                ->import($this->csvPath('agepacprzeforum_table_poll'));

            Poll::where('title', '')->delete();

            $this->line('Importing Forum - Poll Votes');
            (new ForumPollVotesImport)->withOutput($this->output)
                ->import($this->csvPath('agepacprzeforum_table_pollvote'));

            $this->line('Indexing Forum');
            $this->call('scout:import', [
                'searchable' => Post::class,
            ]);
        };
    }

    /**
     * Define an import section.
     *
     * @param  string  $name
     * @param  callable  $callback
     * @return void
     */
    protected function section(string $name, callable $callback): void
    {
        $this->output->title("Importing $name");

        call_user_func($callback);

        $this->info("$name imported!");
    }

    /**
     * Adds attachments which weren't embedded to posts.
     *
     * @return void
     */
    protected function addUnembeddedAttachmentsToPosts(): void
    {
        Attachment::all()->each(function ($attachment) {
            if (! Str::contains($attachment->post->body, $attachment->id)) {
                $attachment->post->update([
                    'body' => $attachment->post->body . $attachment->html(),
                ]);
            }
        });
    }

    /**
     * Set `is_thread_initiator` on appropriate posts.
     *
     * @return void
     */
    protected function setThreadInitiators(): void
    {
        $firstPostsQuery = Post::withoutGlobalScopes()
            ->select('thread_id', DB::raw('MIN(`created_at`) AS first_post_created_at'))
            ->groupBy('thread_id');

        $firstPostsIds = Post::withoutGlobalScopes()
            ->select('id')->distinct() // Select prevents eager loading
            ->joinSub($firstPostsQuery, 'first_posts', function ($join) {
                $join->on('posts.thread_id', '=', 'first_posts.thread_id')
                    ->on('posts.created_at', '=', 'first_post_created_at');
            })
            ->pluck('id');

        Post::whereIn('id', $firstPostsIds)
            ->update(['is_thread_initiator' => true]);
    }

    /**
     * Retrieve the path of the given CSV.
     *
     * @param  string  $fileName
     * @return string
     */
    protected function csvPath(string $fileName): string
    {
        return storage_path("app/legacy-import/database/$fileName.csv");
    }
}
