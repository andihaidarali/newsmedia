<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Edit Post</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $post->slug }}</p>
            </div>
            <a href="{{ route('admin.posts.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">Back to posts</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.posts.update', $post) }}" enctype="multipart/form-data" class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800" x-data="{ postType: '{{ old('type', $post->type) }}', showAdvertorialModal: {{ $errors->has('new_advertorial_name') || $errors->has('new_advertorial_partner') || $errors->has('new_advertorial_starts_at') || $errors->has('new_advertorial_ends_at') ? 'true' : 'false' }}, showCategoryModal: {{ $errors->has('new_category_name') || $errors->has('new_category_slug') || $errors->has('new_category_description') ? 'true' : 'false' }} }">
                @csrf
                @method('PATCH')

                <div class="grid gap-8 lg:grid-cols-[18rem_1fr]">
                    <aside class="space-y-6">
                        <div>
                            <x-input-label for="type" value="Post Type" />
                            <select id="type" name="type" x-model="postType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                <option value="article">Article</option>
                                <option value="video">Video</option>
                                <option value="gallery">Gallery</option>
                                <option value="infographic">Infographic</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        @if (auth()->user()->isReporter())
                            <input type="hidden" name="status" value="draft">
                        @else
                            <div>
                                <x-input-label for="status" value="Status" />
                                <select id="status" name="status" data-status-selector class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                    @foreach (['draft' => 'Draft', 'published' => 'Published', 'scheduled' => 'Scheduled', 'archived' => 'Archived'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('status', $post->status) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        @endif

                        <div>
                            <x-input-label for="published_at" value="Publish Date" />
                            <x-text-input id="published_at" name="published_at" type="datetime-local" class="mt-1 block w-full" :value="old('published_at', $post->published_at?->format('Y-m-d\\TH:i'))" data-publish-date-input />
                            <p class="mt-2 text-sm text-gray-500" data-publish-date-helper>Use a future date when the post status is scheduled.</p>
                            <x-input-error :messages="$errors->get('published_at')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="featured_image" value="Featured Image" />
                            @if ($post->featured_image)
                                <div class="mt-1 rounded-md bg-gray-50 px-3 py-2 text-sm text-gray-600 dark:bg-gray-900 dark:text-gray-300">{{ $post->featured_image }}</div>
                            @endif
                            <input id="featured_image" name="featured_image" type="file" accept="image/*" class="mt-2 block w-full text-sm text-gray-700 file:mb-2 file:mr-4 file:rounded-md file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-gray-700 hover:file:bg-gray-200 dark:text-gray-200">
                            <x-input-error :messages="$errors->get('featured_image')" class="mt-2" />
                        </div>

                        <div>
                            <div class="flex items-center justify-between gap-3">
                                <x-input-label for="category_id" value="Category" />
                                @if (! auth()->user()->isReporter())
                                    @can('create', App\Models\Category::class)
                                        <button type="button" class="text-sm font-medium text-indigo-600 hover:text-indigo-700" x-on:click="showCategoryModal = true">
                                            Add Category
                                        </button>
                                    @endcan
                                @endif
                            </div>
                            <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                <option value="">Uncategorized</option>
                                @foreach ($categories as $category)
                                    @include('admin.posts.partials.category-option', ['category' => $category, 'selectedCategoryId' => old('category_id', $post->category_id)])
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        @if (! auth()->user()->isReporter())
                            @can('create', App\Models\Category::class)
                                <div x-show="showCategoryModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 px-4">
                                    <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl dark:bg-gray-800" x-on:click.outside="showCategoryModal = false">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Add Category</h2>
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This category will be created when the post is saved.</p>
                                            </div>
                                            <button type="button" class="text-gray-400 hover:text-gray-600" x-on:click="showCategoryModal = false">
                                                <span class="sr-only">Close</span>
                                                &times;
                                            </button>
                                        </div>

                                        <div class="mt-6 space-y-4">
                                            <div>
                                                <x-input-label for="new_category_name" value="Name" />
                                                <x-text-input id="new_category_name" name="new_category_name" type="text" class="mt-1 block w-full" :value="old('new_category_name')" />
                                                <x-input-error :messages="$errors->get('new_category_name')" class="mt-2" />
                                            </div>

                                            <div>
                                                <x-input-label for="new_category_slug" value="Slug" />
                                                <x-text-input id="new_category_slug" name="new_category_slug" type="text" class="mt-1 block w-full" :value="old('new_category_slug')" />
                                                <x-input-error :messages="$errors->get('new_category_slug')" class="mt-2" />
                                            </div>

                                            <div>
                                                <x-input-label for="new_category_description" value="Description" />
                                                <textarea id="new_category_description" name="new_category_description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('new_category_description') }}</textarea>
                                                <x-input-error :messages="$errors->get('new_category_description')" class="mt-2" />
                                            </div>
                                        </div>

                                        <div class="mt-6 flex justify-end gap-3">
                                            <button type="button" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50" x-on:click="showCategoryModal = false">
                                                Cancel
                                            </button>
                                            <button type="button" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700" x-on:click="showCategoryModal = false">
                                                Use Category
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endcan
                        @endif

                        <div>
                            <div class="flex items-center justify-between gap-3">
                                <x-input-label for="advertorial_id" value="Advertorial" />
                                @if (! auth()->user()->isReporter())
                                    <button type="button" class="text-sm font-medium text-indigo-600 hover:text-indigo-700" x-on:click="showAdvertorialModal = true">
                                        Add Advertorial
                                    </button>
                                @endif
                            </div>
                            <select id="advertorial_id" name="advertorial_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                <option value="">No advertorial</option>
                                @foreach ($advertorials as $advertorial)
                                    <option value="{{ $advertorial->id }}" @selected((string) old('advertorial_id', $post->advertorial_id) === (string) $advertorial->id)>
                                        {{ $advertorial->name }} - {{ $advertorial->partner }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('advertorial_id')" class="mt-2" />
                        </div>

                        @if (! auth()->user()->isReporter())
                            <div x-show="showAdvertorialModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 px-4">
                                <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl dark:bg-gray-800" x-on:click.outside="showAdvertorialModal = false">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Add Advertorial</h2>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Advertorial ini akan dibuat saat post disimpan.</p>
                                        </div>
                                        <button type="button" class="text-gray-400 hover:text-gray-600" x-on:click="showAdvertorialModal = false">
                                            <span class="sr-only">Close</span>
                                            &times;
                                        </button>
                                    </div>

                                    <div class="mt-6 space-y-4">
                                        <div>
                                            <x-input-label for="new_advertorial_name" value="Nama Advertorial" />
                                            <x-text-input id="new_advertorial_name" name="new_advertorial_name" type="text" class="mt-1 block w-full" :value="old('new_advertorial_name')" />
                                            <x-input-error :messages="$errors->get('new_advertorial_name')" class="mt-2" />
                                        </div>

                                        <div>
                                            <x-input-label for="new_advertorial_partner" value="Kerjasama Oleh" />
                                            <x-text-input id="new_advertorial_partner" name="new_advertorial_partner" type="text" class="mt-1 block w-full" :value="old('new_advertorial_partner')" />
                                            <x-input-error :messages="$errors->get('new_advertorial_partner')" class="mt-2" />
                                        </div>

                                        <div class="grid gap-4 sm:grid-cols-2">
                                            <div>
                                                <x-input-label for="new_advertorial_starts_at" value="Awal Kerjasama" />
                                                <x-text-input id="new_advertorial_starts_at" name="new_advertorial_starts_at" type="date" class="mt-1 block w-full" :value="old('new_advertorial_starts_at')" />
                                                <x-input-error :messages="$errors->get('new_advertorial_starts_at')" class="mt-2" />
                                            </div>

                                            <div>
                                                <x-input-label for="new_advertorial_ends_at" value="Akhir Kerjasama" />
                                                <x-text-input id="new_advertorial_ends_at" name="new_advertorial_ends_at" type="date" class="mt-1 block w-full" :value="old('new_advertorial_ends_at')" />
                                                <x-input-error :messages="$errors->get('new_advertorial_ends_at')" class="mt-2" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-6 flex justify-end gap-3">
                                        <button type="button" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50" x-on:click="showAdvertorialModal = false">
                                            Cancel
                                        </button>
                                        <button type="button" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700" x-on:click="showAdvertorialModal = false">
                                            Use Advertorial
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div>
                            <x-input-label for="tags" value="Tags" />
                            @php($selectedTags = old('tags', $post->tags->pluck('id')->map(fn ($id) => (string) $id)->all()))
                            <select id="tags" name="tags[]" multiple data-select2-tags class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}" @selected(in_array((string) $tag->id, $selectedTags, true))>{{ $tag->name }}</option>
                                @endforeach
                                @foreach (collect(old('tags', []))->filter(fn ($tag) => Str::startsWith($tag, '__new__:')) as $tag)
                                    <option value="{{ $tag }}" selected>{{ Str::replaceFirst('__new__:', '', $tag) }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                        </div>
                    </aside>

                    <main class="space-y-6">
                        <div>
                            <x-input-label for="title" value="Title" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $post->title)" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="rounded-md border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">Recorded Writers</div>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @forelse ($post->writerCredits as $writer)
                                    <span class="rounded-full bg-white px-3 py-1 text-sm text-gray-700 shadow-sm dark:bg-gray-800 dark:text-gray-200">
                                        {{ $writer->name }}
                                        @if ($writer->source === 'auto')
                                            <span class="text-gray-400">(auto)</span>
                                        @endif
                                    </span>
                                @empty
                                    <span class="text-sm text-gray-500">No writer credits recorded yet.</span>
                                @endforelse
                            </div>
                            <p class="mt-3 text-sm text-gray-500">Automatically recorded writers cannot be removed from this form.</p>
                        </div>

                        @if (auth()->user()->isEditor())
                            <div>
                                <x-input-label for="writer_user_ids" value="Add Writers From Users" />
                                @php($selectedWriterUserIds = collect(old('writer_user_ids', []))->map(fn ($id) => (string) $id)->all())
                                <select id="writer_user_ids" name="writer_user_ids[]" multiple class="mt-1 block min-h-28 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                    @foreach ($writerUsers as $writerUser)
                                        <option value="{{ $writerUser->id }}" @selected(in_array((string) $writerUser->id, $selectedWriterUserIds, true))>
                                            {{ $writerUser->name }} ({{ $writerUser->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-2 text-sm text-gray-500">Your name will also be recorded automatically when you save edits.</p>
                                <x-input-error :messages="$errors->get('writer_user_ids')" class="mt-2" />
                                <x-input-error :messages="$errors->get('writer_user_ids.*')" class="mt-2" />
                            </div>
                        @endif

                        <div>
                            <x-input-label for="excerpt" value="Excerpt" />
                            <textarea id="excerpt" name="excerpt" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('excerpt', $post->excerpt) }}</textarea>
                            <x-input-error :messages="$errors->get('excerpt')" class="mt-2" />
                        </div>

                        <div x-show="postType === 'video'" x-cloak>
                            <x-input-label for="youtube_url" value="YouTube Link" />
                            <x-text-input id="youtube_url" name="youtube_url" type="url" class="mt-1 block w-full" :value="old('youtube_url', $post->youtube_url)" placeholder="https://www.youtube.com/watch?v=..." />
                            <x-input-error :messages="$errors->get('youtube_url')" class="mt-2" />
                        </div>

                        <div x-show="postType === 'gallery'" x-cloak>
                            <x-input-label for="gallery_images" value="Gallery Photos" />
                            @if (! empty($post->gallery_images))
                                <div class="mt-1 rounded-md bg-gray-50 px-3 py-2 text-sm text-gray-600 dark:bg-gray-900 dark:text-gray-300">{{ implode(', ', $post->gallery_images) }}</div>
                            @endif
                            <input id="gallery_images" name="gallery_images[]" type="file" accept="image/*" multiple class="mt-2 block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-gray-700 hover:file:bg-gray-200 dark:text-gray-200">
                            <x-input-error :messages="$errors->get('gallery_images')" class="mt-2" />
                            <x-input-error :messages="$errors->get('gallery_images.*')" class="mt-2" />
                        </div>

                        <div x-show="postType === 'infographic'" x-cloak>
                            <x-input-label for="infographic_image" value="Infographic Image" />
                            @if ($post->infographic_image)
                                <div class="mt-1 rounded-md bg-gray-50 px-3 py-2 text-sm text-gray-600 dark:bg-gray-900 dark:text-gray-300">{{ $post->infographic_image }}</div>
                            @endif
                            <input id="infographic_image" name="infographic_image" type="file" accept="image/*" class="mt-2 block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-gray-700 hover:file:bg-gray-200 dark:text-gray-200">
                            <x-input-error :messages="$errors->get('infographic_image')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="body" value="Body" />
                            <input id="body" name="body" type="hidden" value="{{ old('body', $post->body) }}" required>
                            <div data-quill-editor data-quill-input="#body" class="mt-1 min-h-[28rem] rounded-b-md bg-white dark:bg-gray-900">{!! old('body', $post->body) !!}</div>
                            <x-input-error :messages="$errors->get('body')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="meta_title" value="Meta Title" />
                            <x-text-input id="meta_title" name="meta_title" type="text" class="mt-1 block w-full" :value="old('meta_title', $post->meta_title)" />
                            <x-input-error :messages="$errors->get('meta_title')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="meta_description" value="Meta Description" />
                            <textarea id="meta_description" name="meta_description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('meta_description', $post->meta_description) }}</textarea>
                            <x-input-error :messages="$errors->get('meta_description')" class="mt-2" />
                        </div>
                    </main>
                </div>

                <div class="mt-8 flex justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-700">
                    <a href="{{ route('admin.posts.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Cancel</a>
                    <x-primary-button>Save Changes</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
