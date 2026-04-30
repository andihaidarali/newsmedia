@forelse ($posts as $post)
    @include('blog.partials.post-card', ['post' => $post, 'list' => true])
@empty
    <div class="col-span-full rounded-xl border border-dashed border-[var(--color-border)] bg-[var(--color-surface-dark)] p-8 text-center text-sm text-[var(--color-text-muted)]">
        Belum ada post yang dipublikasikan.
    </div>
@endforelse
