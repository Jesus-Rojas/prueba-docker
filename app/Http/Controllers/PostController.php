<?php

namespace App\Http\Controllers;

use App\Models\{
    Post,
    Tag
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
    {
        return response()->json(Post::with('tags')->get(['id','name']));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            "tags"    => "required|array",
            "tags.*"  => "required|string",
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $post = Post::with('tags')
            ->where('name', strtolower($request['name']))
            ->select('id','name')
            ->first();

        if ($post) {
            return response()->json([
                'message' => 'El post ya existe intenta con otro',
                'post' => $post
            ]);
        }

        $tags = array_unique($request['tags'], SORT_STRING);

        $tags = array_map(function ($value) {
            return strtolower($value);
        }, $tags);

        $results = Tag::whereIn('name', $tags)->get(['id','name'])->toArray();

        if (count($results) == count($tags)) {
            $results = array_map(function ($value) {
                return $value['id'];
            }, $results);

            return $this->storePostTaggable([
                'name' => $request['name'],
                'tags' => $results
            ]);
        }

        $idTags = [];
        foreach ($tags as $value) {
            $condicion = true;
            foreach ($results as $item) {
                if ($value == $item['name']) {
                    $idTags[] = $item['id'];
                    $condicion = false;
                    break;
                }
            }
            if ($condicion) {
                $tag = Tag::create([
                    'name' => $value
                ]);
                $idTags[] = $tag->id;
            }
        }

        return $this->storePostTaggable([
            'name' => $request['name'],
            'tags' => $idTags
        ]);
    }

    public function storePostTaggable($postData)
    {
        $post = Post::create([
            'name' => strtolower($postData['name'])
        ]);
        foreach ($postData['tags'] as $value) {
            $post->tags()->attach($value);
        }
        return response()->json([
            'message' => 'Se creo registro con exito',
            'post' => Post::where('id', $post->id)
                        ->with('tags')
                        ->select('name','id')
                        ->first()
        ]);
    }
}
