<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdvertorialRequest;
use App\Models\Advertorial;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdvertorialController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Advertorial::class);

        $advertorials = Advertorial::withCount('posts')
            ->orderByDesc('starts_at')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.advertorials.index', compact('advertorials'));
    }

    public function show(Advertorial $advertorial): View
    {
        Gate::authorize('view', $advertorial);

        $advertorial->load([
            'posts' => fn ($query) => $query
                ->with('author')
                ->orderByDesc('published_at')
                ->orderByDesc('created_at'),
        ]);

        return view('admin.advertorials.show', compact('advertorial'));
    }

    public function create(): View
    {
        Gate::authorize('create', Advertorial::class);

        return view('admin.advertorials.create');
    }

    public function store(StoreAdvertorialRequest $request)
    {
        Gate::authorize('create', Advertorial::class);

        Advertorial::create($request->validated());

        return redirect()
            ->route('admin.advertorials.index')
            ->with('success', 'Advertorial created successfully.');
    }

    public function edit(Advertorial $advertorial): View
    {
        Gate::authorize('update', $advertorial);

        return view('admin.advertorials.edit', compact('advertorial'));
    }

    public function update(StoreAdvertorialRequest $request, Advertorial $advertorial)
    {
        Gate::authorize('update', $advertorial);

        $advertorial->update($request->validated());

        return redirect()
            ->route('admin.advertorials.index')
            ->with('success', 'Advertorial updated successfully.');
    }

    public function destroy(Advertorial $advertorial)
    {
        Gate::authorize('delete', $advertorial);

        $advertorial->delete();

        return redirect()
            ->route('admin.advertorials.index')
            ->with('success', 'Advertorial deleted successfully.');
    }
}
