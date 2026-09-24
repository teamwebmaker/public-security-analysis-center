<?php

namespace Tests\Feature;

use App\Models\Publication;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PublicSortParameterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh');

        Publication::query()->create([
            'title' => ['ka' => 'ტესტ პუბლიკაცია', 'en' => 'Test publication'],
            'description' => ['ka' => 'აღწერა', 'en' => 'Description'],
            'image' => 'publication.jpg',
            'file' => 'publication.pdf',
            'visibility' => '1',
        ]);
    }

    public function test_public_listing_accepts_array_sort_parameter_without_failing(): void
    {
        $this->get(route('publications.page', ['sort' => ['oldest']]))
            ->assertOk()
            ->assertSee('ტესტ პუბლიკაცია')
            ->assertSee('value="newest" selected', false);
    }
}
