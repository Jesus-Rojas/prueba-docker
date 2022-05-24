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
        return response()->json(Movie::with('tags')->get(['id','titulo']));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string',
            "tags"    => "required|array",
            "tags.*"  => "required|string",
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $movie = Movie::with('tags')
            ->where('titulo', strtolower($request['titulo']))
            ->select('id','titulo')
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
                'titulo' => $request['titulo'],
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
            'titulo' => $request['titulo'],
            'tags' => $idTags
        ]);
    }

    public function storeMovieTaggable($movieData)
    {
        $movie = Movie::create([
            'titulo' => strtolower($movieData['titulo'])
        ]);
        foreach ($movieData['tags'] as $value) {
            $movie->tags()->attach($value);
        }
        return response()->json([
            'message' => 'Se creo registro con exito',
            'movie' => Movie::where('id', $movie->id)
                        ->with('tags')
                        ->select('titulo','id')
                        ->first()
        ]);
    }
}
