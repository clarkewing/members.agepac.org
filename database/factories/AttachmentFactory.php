<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Attachment;
use function GuzzleHttp\Psr7\mimetype_from_filename;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Attachment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $file = $this->faker->boolean
        ? UploadedFile::fake()->image($fileName = "{$this->faker->word}.jpg")
        : UploadedFile::fake()->create(
            $fileName = "{$this->faker->word}.{$this->faker->fileExtension}",
            $this->faker->numberBetween(20, 10000),
            mimetype_from_filename($fileName)
        );

        return [
            'post_id' => null,
            'path' => Storage::disk('public')->putFileAs(
                'attachments/'.Str::random(40),
                $file,
                $fileName
            ),
        ];
    }
}
