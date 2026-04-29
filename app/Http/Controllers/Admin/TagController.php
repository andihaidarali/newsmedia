<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Models\Tag;
use Illuminate\Support\Facades\Gate;

class TagController extends Controller
{
    /**
     * Display a listing of all tags.
     */
    public function index()
    {
        Gate::authorize('viewAny', Tag::class);

        $tags = Tag::withCount('posts')
            ->orderBy('name')
            ->paginate(30);

        return view('admin.tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new tag.
     */
    public function create()
    {
        Gate::authorize('create', Tag::class);

        return view('admin.tags.create');
    }

    /**
     * Store a newly created tag.
     */
    public function store(StoreTagRequest $request)
    {
        Gate::authorize('create', Tag::class);

        Tag::create($request->validated());

        return redirect()
            ->route('admin.tags.index')
            ->with('success', 'Tag created successfully.');
    }

    /**
     * Show the form for editing a tag.
     */
    public function edit(Tag $tag)
    {
        Gate::authorize('update', $tag);

        return view('admin.tags.edit', compact('tag'));
    }

    /**
     * Update the specified tag.
     */
    public function update(StoreTagRequest $request, Tag $tag)
    {
        Gate::authorize('update', $tag);

        $tag->update($request->validated());

        return redirect()
            ->route('admin.tags.index')
            ->with('success', 'Tag updated successfully.');
    }

    /**
     * Remove the specified tag.
     */
    public function destroy(Tag $tag)
    {
        Gate::authorize('delete', $tag);

        $tag->delete();

        return redirect()
            ->route('admin.tags.index')
            ->with('success', 'Tag deleted successfully.');
    }
}
