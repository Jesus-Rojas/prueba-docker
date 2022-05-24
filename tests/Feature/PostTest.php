<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{
    public function test_example()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_a_post_morph_to_many_tags()
    {
        $post = new Post();
        $this->assertInstanceOf(Collection::class, $post->tags);
    }
}
