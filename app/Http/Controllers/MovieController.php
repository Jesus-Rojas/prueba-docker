<?php

namespace App\Http\Controllers;

use App\Models\{
    Movie,
    Tag
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MovieController extends Controller
{
    public function index()
    {
        return response()->json(Movie::with('tags')->get(['id','title']));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            "tags"    => "required|array",
            "tags.*"  => "required|string",
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $movie = Movie::with('tags')
            ->where('title', strtolower($request['title']))
            ->select('id','title')
            ->first();

        if ($movie) {
            return response()->json([
                'message' => 'El movie ya existe intenta con otro',
                'movie' => $movie
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

            return $this->storeMovieTaggable([
                'title' => $request['title'],
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

        return $this->storeMovieTaggable([
            'title' => $request['title'],
            'tags' => $idTags
        ]);
    }

    public function storeMovieTaggable($movieData)
    {
        $movie = Movie::create([
            'title' => strtolower($movieData['title'])
        ]);
        foreach ($movieData['tags'] as $value) {
            $movie->tags()->attach($value);
        }
        return response()->json([
            'message' => 'Se creo registro con exito',
            'movie' => Movie::where('id', $movie->id)
                        ->with('tags')
                        ->select('title','id')
                        ->first()
        ]);
    }
}
