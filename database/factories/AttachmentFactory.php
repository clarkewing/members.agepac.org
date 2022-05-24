<?php

namespace Database\Factories;

use GuzzleHttp\Psr7\MimeType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $file = $this->faker->boolean()
        ? UploadedFile::fake()->image($fileName = "{$this->faker->word()}.jpg")
        : UploadedFile::fake()->create(
            $fileName = "{$this->faker->word()}.{$this->faker->fileExtension()}",
            $this->faker->numberBetween(20, 10000),
            MimeType::fromFilename($fileName)
        );

        return [
            'post_id' => null,
            'path' => Storage::disk('public')->putFileAs(
                'attachments/' . Str::random(40),
                $file,
                $fileName
            ),
        ];
    }
}
