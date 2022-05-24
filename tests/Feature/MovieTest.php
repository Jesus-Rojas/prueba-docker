<?php

namespace Tests\Feature;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MovieTest extends TestCase
{
    public function test_example()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_a_movie_morph_to_many_tags()
    {
        $movie = new Movie();
        $this->assertInstanceOf(Collection::class, $movie->tags);
    }
}
