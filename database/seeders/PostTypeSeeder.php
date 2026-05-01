<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Advertorial;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PostTypeSeeder extends Seeder
{
    /**
     * Seed sample posts for every supported post type.
     */
    public function run(): void
    {
        [$administrator, $editor, $reporter] = $this->seedUsers();
        [$technology, $videoCategory, $photoCategory, $dataCategory] = $this->seedCategories();
        $advertorial = $this->seedAdvertorial();

        $this->seedPost(
            [
                'user_id' => $administrator->id,
                'category_id' => $technology->id,
                'advertorial_id' => null,
                'type' => 'article',
                'title' => 'Artikel Seed: Strategi Redaksi Mengelola Portal Berita Modern',
                'excerpt' => 'Contoh artikel standar untuk kebutuhan demo homepage, category, dan detail page.',
                'body' => $this->articleBody('Artikel'),
                'featured_image' => $this->ensureImage('posts/featured/seeder-article.png'),
                'youtube_url' => null,
                'gallery_images' => null,
                'infographic_image' => null,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(6),
                'meta_title' => 'Artikel Seed Portal Berita',
                'meta_description' => 'Seeder artikel standar untuk portal berita.',
            ],
            ['Berita Update', 'Editorial', 'Portal Berita'],
            [$administrator]
        );

        $this->seedPost(
            [
                'user_id' => $editor->id,
                'category_id' => $videoCategory->id,
                'advertorial_id' => null,
                'type' => 'video',
                'title' => 'Video Seed: Wawancara Eksklusif Tentang Inovasi AI di Ruang Redaksi',
                'excerpt' => 'Contoh post video dengan YouTube URL untuk pengujian modul frontend dan admin.',
                'body' => $this->articleBody('Video'),
                'featured_image' => $this->ensureImage('posts/featured/seeder-video.png'),
                'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'gallery_images' => null,
                'infographic_image' => null,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(4),
                'meta_title' => 'Video Seed Redaksi',
                'meta_description' => 'Seeder post video untuk portal berita.',
            ],
            ['Video', 'YouTube', 'Wawancara'],
            [$editor]
        );

        $this->seedPost(
            [
                'user_id' => $reporter->id,
                'category_id' => $videoCategory->id,
                'advertorial_id' => null,
                'type' => 'video',
                'title' => 'Video Seed: Liputan Reporter dari Lapangan',
                'excerpt' => 'Contoh video kedua dengan reporter sebagai penulis utama.',
                'body' => $this->articleBody('Video'),
                'featured_image' => $this->ensureImage('posts/featured/seeder-video-reporter.png'),
                'youtube_url' => 'https://www.youtube.com/watch?v=ysz5S6PUM-U',
                'gallery_images' => null,
                'infographic_image' => null,
                'status' => 'draft',
                'published_at' => null,
                'meta_title' => 'Video Seed Reporter',
                'meta_description' => 'Seeder draft video reporter.',
            ],
            ['Video', 'Reporter'],
            [$reporter, $editor]
        );

        $this->seedPost(
            [
                'user_id' => $editor->id,
                'category_id' => $photoCategory->id,
                'advertorial_id' => null,
                'type' => 'gallery',
                'title' => 'Gallery Seed: Potret Kegiatan Komunitas Digital',
                'excerpt' => 'Contoh gallery post dengan beberapa gambar untuk menguji slider atau thumbnail gallery.',
                'body' => $this->articleBody('Gallery'),
                'featured_image' => $this->ensureImage('posts/featured/seeder-gallery.png'),
                'youtube_url' => null,
                'gallery_images' => [
                    $this->ensureImage('posts/gallery/seeder-gallery-1.png'),
                    $this->ensureImage('posts/gallery/seeder-gallery-2.png'),
                    $this->ensureImage('posts/gallery/seeder-gallery-3.png'),
                ],
                'infographic_image' => null,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(3),
                'meta_title' => 'Gallery Seed Event',
                'meta_description' => 'Seeder gallery post dengan tiga gambar.',
            ],
            ['Gallery', 'Foto', 'Komunitas'],
            [$editor]
        );

        $this->seedPost(
            [
                'user_id' => $reporter->id,
                'category_id' => $photoCategory->id,
                'advertorial_id' => $advertorial->id,
                'type' => 'gallery',
                'title' => 'Gallery Seed: Advertorial Aktivasi Brand di Kota Besar',
                'excerpt' => 'Contoh gallery post advertorial dengan beberapa foto kegiatan kampanye brand.',
                'body' => $this->articleBody('Gallery'),
                'featured_image' => $this->ensureImage('posts/featured/seeder-gallery-adv.png'),
                'youtube_url' => null,
                'gallery_images' => [
                    $this->ensureImage('posts/gallery/seeder-gallery-adv-1.png'),
                    $this->ensureImage('posts/gallery/seeder-gallery-adv-2.png'),
                    $this->ensureImage('posts/gallery/seeder-gallery-adv-3.png'),
                ],
                'infographic_image' => null,
                'status' => 'draft',
                'published_at' => null,
                'meta_title' => 'Gallery Seed Advertorial',
                'meta_description' => 'Seeder gallery advertorial untuk modul post.',
            ],
            ['Gallery', 'Advertorial', 'Brand Activation'],
            [$reporter, $editor]
        );

        $this->seedPost(
            [
                'user_id' => $administrator->id,
                'category_id' => $dataCategory->id,
                'advertorial_id' => null,
                'type' => 'infographic',
                'title' => 'Infografis Seed: Peta Pertumbuhan Pembaca per Kanal',
                'excerpt' => 'Contoh infografis untuk pengujian detail post dan preview media infografis.',
                'body' => $this->articleBody('Infografis'),
                'featured_image' => $this->ensureImage('posts/featured/seeder-infographic.png'),
                'youtube_url' => null,
                'gallery_images' => null,
                'infographic_image' => $this->ensureImage('posts/infographics/seeder-infographic-main.png'),
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(2),
                'meta_title' => 'Infografis Seed Pembaca',
                'meta_description' => 'Seeder post infografis untuk pengujian frontend.',
            ],
            ['Infografis', 'Data', 'Analytics'],
            [$administrator]
        );

        $this->seedPost(
            [
                'user_id' => $editor->id,
                'category_id' => $dataCategory->id,
                'advertorial_id' => null,
                'type' => 'infographic',
                'title' => 'Infografis Seed: Ringkasan Kinerja Tim Liputan Bulanan',
                'excerpt' => 'Contoh infografis kedua untuk kebutuhan dashboard dan list konten.',
                'body' => $this->articleBody('Infografis'),
                'featured_image' => $this->ensureImage('posts/featured/seeder-infographic-report.png'),
                'youtube_url' => null,
                'gallery_images' => null,
                'infographic_image' => $this->ensureImage('posts/infographics/seeder-infographic-report.png'),
                'status' => 'scheduled',
                'published_at' => Carbon::now()->addDays(3),
                'meta_title' => 'Infografis Seed Kinerja',
                'meta_description' => 'Seeder scheduled infografis.',
            ],
            ['Infografis', 'Kinerja', 'Editor'],
            [$editor]
        );
    }

    /**
     * @return array{0: User, 1: User, 2: User}
     */
    private function seedUsers(): array
    {
        $administrator = User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => Role::ADMINISTRATOR,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        $editor = User::query()->firstOrCreate(
            ['email' => 'editor@example.com'],
            [
                'name' => 'Editor',
                'password' => Hash::make('password'),
                'role' => Role::EDITOR,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        $reporter = User::query()->firstOrCreate(
            ['email' => 'reporter@example.com'],
            [
                'name' => 'Reporter',
                'password' => Hash::make('password'),
                'role' => Role::REPORTER,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        return [$administrator, $editor, $reporter];
    }

    /**
     * @return array{0: Category, 1: Category, 2: Category, 3: Category}
     */
    private function seedCategories(): array
    {
        $technology = Category::query()->firstOrCreate(
            ['name' => 'Technology'],
            ['sort_order' => 0],
        );

        $videoCategory = Category::query()->firstOrCreate(
            ['name' => 'Video News', 'parent_id' => $technology->id],
            ['sort_order' => 0],
        );

        $photoCategory = Category::query()->firstOrCreate(
            ['name' => 'Photo Story', 'parent_id' => $technology->id],
            ['sort_order' => 1],
        );

        $dataCategory = Category::query()->firstOrCreate(
            ['name' => 'Data Visual', 'parent_id' => $technology->id],
            ['sort_order' => 2],
        );

        return [$technology, $videoCategory, $photoCategory, $dataCategory];
    }

    private function seedAdvertorial(): Advertorial
    {
        return Advertorial::query()->firstOrCreate(
            ['name' => 'Advertorial Seed Campaign'],
            [
                'partner' => 'PT Mitra Brand Indonesia',
                'starts_at' => Carbon::now()->subWeek()->toDateString(),
                'ends_at' => Carbon::now()->addMonth()->toDateString(),
            ],
        );
    }

    private function seedPost(array $attributes, array $tagNames, array $writers): void
    {
        $post = Post::query()->firstOrNew(['title' => $attributes['title']]);
        $post->fill($attributes);
        $post->save();

        $post->tags()->sync(
            collect($tagNames)
                ->map(fn (string $name) => Tag::query()->firstOrCreate(['name' => $name])->id)
                ->all(),
        );

        foreach ($writers as $writer) {
            $post->writerCredits()->firstOrCreate(
                ['user_id' => $writer->id],
                [
                    'created_by_user_id' => $writer->id,
                    'name' => $writer->name,
                    'source' => 'auto',
                ],
            );
        }
    }

    private function articleBody(string $label): string
    {
        return <<<HTML
<p>{$label} seed ini disiapkan untuk membantu pengujian tampilan frontend dan backend pada portal berita.</p>
<p>Konten ini mencakup struktur judul, excerpt, body, metadata, dan media tambahan sesuai tipe post yang sedang diuji.</p>
<h2>Tujuan Seeder</h2>
<p>Seeder ini memastikan seluruh tipe post memiliki contoh data yang siap dipakai untuk home, halaman kategori, detail post, filter dashboard, dan pengujian list post.</p>
HTML;
    }

    private function ensureImage(string $path): string
    {
        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, base64_decode(
                'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQIHWP4////fwAJ+wP9KobjigAAAABJRU5ErkJggg=='
            ));
        }

        return $path;
    }
}
