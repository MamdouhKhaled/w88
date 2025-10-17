<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookmarkRequest;
use App\Http\Requests\UpdateBookmarkRequest;
use App\Http\Resources\BookmarkResource;
use App\Models\Bookmark;
use App\Models\Tag;
use Illuminate\Support\Str;

class BookmarkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return BookmarkResource::collection(Bookmark::with('tags')
            ->where(
                'user_id', auth()->id()
            )->whereAny(
                ['title', 'note'],
                'like',
                request()->string('q')
            )
            ->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookmarkRequest $request)
    {
        $bookmark = auth()->user()->bookmark()->create($request->validated());
        $tags = collect( $request->validated('tags'))->map(function ($tag) {
            return Tag::firstOrCreate([
                'name' => $tag,
            ],['name' => $tag]);
        }) ;
        $bookmark->tags()->sync($tags->pluck('id'));

        return BookmarkResource::make($bookmark)->additional([
            'message' => 'Bookmark created successfully.',
            'status' => 201
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(Bookmark $bookmark)
    {
        return BookmarkResource::make($bookmark->load('tags'))->additional([
            'message' => 'Bookmark retrieved successfully.',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookmarkRequest $request, Bookmark $bookmark)
    {
        $bookmark->update($request->validated());
        $tags = collect( $request->validated('tags'))->map(function ($tag) {
            return Tag::firstOrCreate([
                'name' => $tag,
            ],['name' => $tag]);
        }) ;
        $bookmark->tags()->sync($tags->pluck('id'));

        $bookmark->refresh();

        return BookmarkResource::make($bookmark)->additional([
            'message' => 'Bookmark Updated successfully.',
            'status' => 200
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bookmark $bookmark)
    {
        if ($bookmark->delete()){
            return response()->json([
                'message' => 'Bookmark deleted successfully.',
                'status' => 200
            ],200);
        }
        return response()->json([
            'message' => 'something went wrong.',
        ],'400');
    }

    public function share_store(Bookmark $bookmark)
    {
        $bookmark->update(['share_token' => Str::uuid()->toString()]);
        $bookmark = $bookmark->refresh();
        return BookmarkResource::make($bookmark)->additional([
            'message' => 'Bookmark shared successfully.',
            'status' => 200
        ]);
    }

    public function share(Bookmark $bookmark)
    {
        return BookmarkResource::make($bookmark)->additional([
            'message' => 'Bookmark shared successfully.',
            'status' => 200
        ]);
    }
}
