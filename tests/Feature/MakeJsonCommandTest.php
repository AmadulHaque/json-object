<?php

namespace Laravel\JsonObject\Tests\Feature;

use Laravel\JsonObject\Tests\TestCase;
use Illuminate\Support\Facades\File;

class MakeJsonCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Clean up before running
        if (File::exists(app_path('Json'))) {
            File::deleteDirectory(app_path('Json'));
        }
    }

    /** @test */
    public function it_creates_json_object_class()
    {
        $this->artisan('make:json', ['name' => 'ProductAttributes'])
            ->assertExitCode(0);

        $path = app_path('Json/ProductAttributes.php');
        
        $this->assertTrue(File::exists($path));
        
        $content = File::get($path);
        
        $this->assertStringContainsString('class ProductAttributes extends JsonObject', $content);
        $this->assertStringContainsString('namespace App\Json;', $content);
    }
}
