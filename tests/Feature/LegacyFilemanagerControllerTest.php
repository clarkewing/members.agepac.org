<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class LegacyFilemanagerControllerTest extends TestCase
{
    private string $fileFixture;
    private string $imageFixture;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();

        // Realistic legacy layout: storage/files/{userId}/{name}, same for photos.
        $this->fileFixture = base_path('storage/files/999999/test-document.pdf');
        $this->imageFixture = base_path('storage/photos/999999/test-image.jpg');

        File::ensureDirectoryExists(dirname($this->fileFixture));
        File::ensureDirectoryExists(dirname($this->imageFixture));
        File::put($this->fileFixture, '%PDF-1.4 fake test pdf payload');
        File::put($this->imageFixture, 'fake test image bytes');
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(base_path('storage/files/999999'));
        File::deleteDirectory(base_path('storage/photos/999999'));

        parent::tearDown();
    }

    /** @test */
    public function testAuthenticatedUserCanFetchALegacyFile()
    {
        $this->signIn();

        $response = $this->get('/laravel-filemanager/files/999999/test-document.pdf');

        $response->assertOk();
        $this->assertSame($this->fileFixture, $response->baseResponse->getFile()->getPathname());
    }

    /** @test */
    public function testAuthenticatedUserCanFetchALegacyImage()
    {
        $this->signIn();

        $response = $this->get('/laravel-filemanager/photos/999999/test-image.jpg');

        $response->assertOk();
        $this->assertSame($this->imageFixture, $response->baseResponse->getFile()->getPathname());
    }

    /** @test */
    public function testUrlEncodedSpacesInFilenameAreSupported()
    {
        // Real legacy filenames have spaces (e.g. "Statuts 2022 - signed.pdf").
        $path = base_path('storage/files/999999/Statuts 2022 - signed.pdf');
        File::put($path, 'pdf with spaces');

        $this->signIn();

        $response = $this->get('/laravel-filemanager/files/999999/Statuts%202022%20-%20signed.pdf');

        $response->assertOk();
        $this->assertSame($path, $response->baseResponse->getFile()->getPathname());
    }

    /** @test */
    public function testGuestsAreRedirectedToLogin()
    {
        $this->get('/laravel-filemanager/files/999999/test-document.pdf')
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function testMissingFileReturns404()
    {
        $this->signIn();

        $this->get('/laravel-filemanager/files/999999/does-not-exist.pdf')
            ->assertNotFound();
    }

    /** @test */
    public function testPathTraversalInBasePathIsRejected()
    {
        // Drop a sensitive file two levels up — somewhere the legitimate
        // routes can resolve to via a '..' parameter if there's no guard.
        $sensitive = base_path('storage/files/secrets.txt');
        File::put($sensitive, 'this should never be reachable');

        $this->signIn();

        $this->get('/laravel-filemanager/files/..%2F/secrets.txt')->assertNotFound();
        $this->get('/laravel-filemanager/files/../secrets.txt')->assertNotFound();

        File::delete($sensitive);
    }

    /** @test */
    public function testPathTraversalInFilenameIsRejected()
    {
        $sensitive = base_path('storage/laravel.log');
        File::put($sensitive, 'log contents');

        $this->signIn();

        $this->get('/laravel-filemanager/files/999999/..%2F..%2Flaravel.log')->assertNotFound();
        $this->get('/laravel-filemanager/files/999999/../../laravel.log')->assertNotFound();

        File::delete($sensitive);
    }
}
