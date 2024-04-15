<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class RosterFileTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload(): void
    {
        $response = $this->json('POST', 'api/roster/upload', [
            'file' => UploadedFile::fake()->createWithContent(
                'roster2.html',
                file_get_contents(__DIR__ . '/../../resources/roster.html')
            )
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment(['activitiesCount' => 54]);
    }

    public function test_wrong_upload(): void
    {
        $response = $this->json('POST', 'api/roster/upload', [
            'file' => UploadedFile::fake()->createWithContent(
                'roster2.html',
                file_get_contents(__DIR__ . '/../../phpunit.xml')
            )
        ]);
        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Invalid roster: format not recognized.']);
    }

    public function test_upload_corrupted_file(): void
    {
        $response = $this->json('POST', 'api/roster/upload', [
            'file' => UploadedFile::fake()->createWithContent(
                'roster2.html',
                file_get_contents(__DIR__ . '/../../resources/roster_evil.html')
            )
        ]);
        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Invalid hours format: 07451']);
    }

    public function test_upload_empty_file(): void
    {
        $response = $this->json('POST', 'api/roster/upload', [
            'file' => UploadedFile::fake()->createWithContent('roster.html', '')
        ]);
        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'The file field must be between 1 and 1024 kilobytes.']);
    }

    public function test_upload_without_file(): void
    {
        $response = $this->json('POST', 'api/roster/upload', [
            'not_correct' => UploadedFile::fake()->createWithContent('roster.html', '')
        ]);
        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'The file field is required.']);
    }

}
