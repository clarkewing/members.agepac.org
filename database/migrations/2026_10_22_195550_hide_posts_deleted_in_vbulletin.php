<?php

use App\Models\Post;
use App\Models\Thread;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class HidePostsDeletedInVbulletin extends Migration
{
    /**
     * Threads that moderators had deleted on the vBulletin forum.
     */
    public const THREAD_IDS = [
        597, 617, 620, 636, 661, 712, 731, 798, 876, 996,
        997, 1021, 2339, 2478, 2492, 2650,
    ];

    /**
     * Posts that moderators had deleted on the vBulletin forum.
     */
    public const POST_IDS = [
        3071, 3449, 3993, 4559, 4779, 5006, 5020, 5663, 5796, 5939,
        6140, 6375, 6404, 6427, 6454, 6455, 6548, 6666, 6862, 6887,
        7150, 7586, 7601, 7745, 7804, 7903, 7966, 8017, 8107, 8128,
        8131, 8132, 8134, 8136, 8146, 8153, 8363, 8658, 8892, 8894,
        8895, 8913, 9056, 9136, 9140, 9180, 9191, 9213, 9292, 9385,
        9404, 9443, 9553, 9557, 9567, 9670, 9859, 9863, 10153, 10183,
        10313, 10319, 10356, 10382, 10386, 10388, 10486, 10500, 10546, 10570,
        10750, 10793, 10936, 10984, 10989, 11015, 11138, 11224, 11225, 11305,
        11355, 11393, 11396, 11429, 11430, 11436, 11510, 11566, 11585, 11871,
        11931, 11941, 11942, 11999, 12111, 12164, 12207, 12243, 12256, 12316,
        12437, 12486, 12569, 12672, 12673, 12819, 12861, 13011, 13077, 13097,
        13124, 13166, 13198, 13306, 13336, 13464, 13653, 13723, 13724, 13726,
        13874, 13887, 13897, 14008, 14083, 14123, 14144, 14195, 14196, 14264,
        14326, 14455, 14533, 14627, 14684, 14743, 15040, 15146, 15188, 15191,
        15196, 15202, 15284, 15285, 15286, 15312, 15313, 15360, 15367, 15371,
        15405, 15411, 15422, 15427, 15526, 15661, 15767, 15824, 15826, 16004,
        16010, 16068, 16071, 16082, 16147, 16152, 16203, 16209, 16237, 16281,
        16424, 16607, 16610, 16752, 16779, 16806, 16834, 16861, 16912, 16929,
        16954, 16986, 17049, 17051, 17141, 17155, 17173, 17211, 17265, 17269,
        17308, 17326, 17391, 17482, 17540, 17587, 17635, 17715, 17776, 17882,
        18027, 18050, 18218, 18281, 18636, 18663, 18995, 19329, 19380, 19384,
        19425, 19517, 19626, 19711, 19827, 19829, 19880, 19947, 20010,
    ];

    /**
     * The December 2020 legacy:import brought these threads and posts across
     * as visible. Soft-delete them, keeping their attachments and favorites
     * so a moderator can restore any of them intact.
     */
    public function up(): void
    {
        // Databases without the imported forum (fresh installs, the test suite)
        // have nothing to hide, and touching the models here would boot them
        // without a signed-in user, so they would stop recording activity.
        if (
            DB::table('threads')->whereIn('id', self::THREAD_IDS)->doesntExist()
            && DB::table('posts')->whereIn('id', self::POST_IDS)->doesntExist()
        ) {
            return;
        }

        // Without touching, so these old threads don't show up as having new posts.
        Model::withoutTouching(function () {
            Thread::whereIn('id', self::THREAD_IDS)->get()->each(function (Thread $thread) {
                $thread->posts()->get()->each(function (Post $post) {
                    $this->hidePost($post);
                });

                // Its posts are already hidden, so the thread's own events only
                // take back the creator's reputation.
                $thread->activity()->delete();
                $thread->delete();
            });

            Post::whereIn('id', self::POST_IDS)->get()->each(function (Post $post) {
                $this->hidePost($post);
            });
        });
    }

    public function down(): void
    {
        // No-op: moderators can restore individual posts from the forum.
    }

    /**
     * Do what deleting a post normally does, except deleting its attachments
     * and favorites: those would be lost for good if the post were restored.
     */
    protected function hidePost(Post $post): void
    {
        // Activity only cleans up after itself when a user is signed in, which
        // is never the case during a migration. The favorites' activity has to
        // go too: the profile feed can't render a favorite of a hidden post.
        $post->favorites->each(function ($favorite) {
            $favorite->activity()->delete();
        });
        $post->activity()->delete();

        $post->unsearchable();

        if ($post->isBest()) {
            $post->thread->unmarkBestPost();
        }

        Model::withoutEvents(function () use ($post) {
            $post->delete();
        });

        if (! $post->is_thread_initiator) {
            $post->owner->loseReputation('reply_posted');
        }
    }
}
