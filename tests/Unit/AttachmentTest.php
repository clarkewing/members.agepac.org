<?php

namespace Tests\Unit;

use App\Attachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class AttachmentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    /** @test */
    public function testGetsUuidOnCreation()
    {
        $attachment = Attachment::factory()->make();

        $this->assertEquals('', $attachment->id);

        $attachment->save();

        $this->assertTrue(Str::isUuid((string) $attachment->id));
    }

    /** @test */
    public function testDeletingDeletesAssociatedFileInStorage()
    {
        $attachment = Attachment::factory()->create();

        $this->assertDatabaseHas('attachments', ['id' => $attachment->id]);
        Storage::disk('public')->assertExists($attachment->path);

        $attachment->delete();

        $this->assertDatabaseMissing('attachments', ['id' => $attachment->id]);
        Storage::disk('public')->assertMissing($attachment->path);
    }
}
