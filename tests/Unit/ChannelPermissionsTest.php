<?php

namespace Tests\Unit;

use App\Channel;
use App\ChannelPermissions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ChannelPermissionsTest extends TestCase
{
    /**
     * @var \App\Channel
     */
    public $channel;

    public function setUp(): void
    {
        parent::setUp();

        $this->channel = create(Channel::class);
    }

    /** @test */
    public function testExceptionThrownIfPermissionsArrayIsNotSet()
    {
        $this->expectExceptionMessage('Permissions array missing on model.');

        $channelWithoutPermissionsArray = new class extends Model {
            use ChannelPermissions;
        };
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function testNoExceptionThrownIfPermissionsArrayIsSet()
    {
        $channelWithPermissionsArray = new class extends Model {
            use ChannelPermissions;

            protected static $permissions = ['view'];
        };
    }

    /** @test */
    public function testKnowsIfAnyRestrictionsExist()
    {
        $this->assertFalse($this->channel->isRestricted());

        $this->channel->createPermission('post');

        $this->assertTrue($this->channel->isRestricted());

        $this->assertFalse($this->channel->isRestricted('view'));
        $this->assertTrue($this->channel->isRestricted('post'));
    }

    /** @test */
    public function testCanCreateAndDeletePermissionsForChannel()
    {
        $this->channel->createPermission('view');

        $this->assertDatabaseHas('permissions', ['name' => "channels.view.{$this->channel->slug}"]);

        $this->channel->deletePermission('view');

        $this->assertDatabaseMissing('permissions', ['name' => "channels.view.{$this->channel->slug}"]);
    }

    /** @test */
    public function testCanGetAssociatedPermission()
    {
        $this->assertNull($this->channel->vote_permission);
        $this->assertNull($this->channel->votePermission);

        $this->channel->createPermission('vote');

        $this->assertInstanceOf(Permission::class, $this->channel->vote_permission);
        $this->assertInstanceOf(Permission::class, $this->channel->votePermission);
    }
}
