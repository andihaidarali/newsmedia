<template>
  <PublicLayout>
    <Head>
      <title>{{ seo.title }}</title>
      <meta name="description" :content="seo.description" />
      <meta property="og:title" :content="seo.title" />
      <meta property="og:description" :content="seo.description" />
      <meta property="og:url" :content="seo.url" />
      <meta property="og:type" content="article" />
      <meta property="og:image" :content="seo.image" />
      <meta property="article:published_time" :content="seo.published_time" />
      <meta property="article:modified_time" :content="seo.modified_time" />
      <meta property="article:author" :content="seo.author" />
      <meta property="article:section" :content="seo.section" />
      <meta v-for="tag in seo.tags" :key="tag" property="article:tag" :content="tag" />
      <meta name="twitter:card" content="summary_large_image" />
      <meta name="twitter:title" :content="seo.title" />
      <meta name="twitter:description" :content="seo.description" />
      <meta name="twitter:image" :content="seo.image" />
      <link rel="canonical" :href="seo.url" />
      <component :is="'script'" type="application/ld+json" v-html="JSON.stringify(jsonLd)" />
    </Head>

    <div class="max-w-7xl mx-auto px-4 py-6">
      <!-- Breadcrumb -->
      <nav class="flex items-center text-sm mb-6 text-[var(--color-text-muted)]">
        <Link href="/" class="hover:text-[var(--color-primary)] transition-colors">Beranda</Link>
        <span class="breadcrumb-item"></span>
        <Link :href="`/kategori/${article.category?.slug}`" class="hover:text-[var(--color-primary)] transition-colors">
          {{ article.category?.name }}
        </Link>
        <span class="breadcrumb-item"></span>
        <span class="text-[var(--color-text-secondary)] line-clamp-1">{{ article.title }}</span>
      </nav>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Article Content -->
        <article class="lg:col-span-8">
          <!-- Article Header -->
          <header class="mb-6">
            <div class="flex items-center gap-2 mb-3">
              <span class="category-badge" :style="{ background: article.category?.color }">
                {{ article.category?.name }}
              </span>
              <span v-if="article.type === 'advertorial'" class="sponsored-label">Advertorial</span>
              <span v-if="article.type === 'video'" class="bg-red-600 text-white text-[10px] font-bold px-2 py-1 rounded-sm uppercase">Video</span>
            </div>
            <h1 class="text-3xl md:text-4xl font-black text-[var(--color-secondary)] leading-tight">
              {{ article.title }}
            </h1>
            <div class="flex flex-wrap items-center gap-4 mt-4 text-sm text-[var(--color-text-secondary)]">
              <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-[var(--color-primary)] flex items-center justify-center text-white text-xs font-bold">
                  {{ article.author?.name?.charAt(0) }}
                </div>
                <div>
                  <span class="font-semibold text-[var(--color-text-primary)]">{{ article.author?.name }}</span>
                  <span v-if="article.editor"> | Editor: {{ article.editor?.name }}</span>
                </div>
              </div>
              <span class="text-[var(--color-text-muted)]">{{ formattedDate }}</span>
              <span class="text-[var(--color-text-muted)] flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                {{ formatViews(article.views_count) }}
              </span>
            </div>
          </header>

          <!-- Share Buttons -->
          <div class="flex items-center gap-2 mb-6 pb-6 border-b border-[var(--color-border)]">
            <span class="text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider mr-2">Bagikan:</span>
            <a :href="`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(seo.url)}`" target="_blank"
               class="share-btn w-9 h-9 rounded-full bg-[#1877F2] text-white flex items-center justify-center">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            </a>
            <a :href="`https://twitter.com/intent/tweet?url=${encodeURIComponent(seo.url)}&text=${encodeURIComponent(article.title)}`" target="_blank"
               class="share-btn w-9 h-9 rounded-full bg-black text-white flex items-center justify-center">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </a>
            <a :href="`https://wa.me/?text=${encodeURIComponent(article.title + ' ' + seo.url)}`" target="_blank"
               class="share-btn w-9 h-9 rounded-full bg-[#25D366] text-white flex items-center justify-center">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </a>
            <button @click="copyLink" class="share-btn w-9 h-9 rounded-full bg-[var(--color-surface-dark)] text-[var(--color-text-secondary)] flex items-center justify-center hover:bg-[var(--color-primary)] hover:text-white">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            </button>
          </div>

          <!-- Featured Image -->
          <figure v-if="article.featured_image" class="mb-8">
            <img :src="article.featured_image" :alt="article.featured_image_alt || article.title"
                 class="w-full rounded-xl shadow-md" />
            <figcaption v-if="article.featured_image_caption" class="text-xs text-[var(--color-text-muted)] mt-2 text-center italic">
              {{ article.featured_image_caption }}
            </figcaption>
          </figure>

          <!-- Video Embed -->
          <div v-if="article.type === 'video' && article.video_url" class="mb-8 aspect-video rounded-xl overflow-hidden bg-black">
            <iframe :src="embedUrl" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
          </div>

          <!-- Gallery -->
          <div v-if="article.type === 'gallery' && article.images?.length" class="mb-8">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 rounded-xl overflow-hidden">
              <div v-for="img in article.images" :key="img.id" class="aspect-square overflow-hidden">
                <img :src="img.image_path" :alt="img.alt_text || ''" class="w-full h-full object-cover hover:scale-110 transition-transform duration-500 cursor-pointer" />
              </div>
            </div>
          </div>

          <!-- Article Body -->
          <div class="prose-article" v-html="article.content"></div>

          <!-- In-Body Recommendations -->
          <div v-if="recommendedArticles.length" class="my-8 p-5 bg-[var(--color-surface-dark)] rounded-xl border border-[var(--color-border)]">
            <h3 class="text-sm font-bold text-[var(--color-secondary)] mb-3 uppercase tracking-wider">Baca Juga</h3>
            <div class="space-y-2">
              <Link v-for="rec in recommendedArticles" :key="rec.id"
                    :href="`/${rec.category?.slug}/${rec.slug}`"
                    class="block text-sm font-semibold text-[var(--color-accent)] hover:text-[var(--color-primary)] transition-colors">
                → {{ rec.title }}
              </Link>
            </div>
          </div>

          <!-- Tags -->
          <div v-if="article.tags?.length" class="flex flex-wrap items-center gap-2 mt-8 pb-6 border-b border-[var(--color-border)]">
            <span class="text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider">Tags:</span>
            <span v-for="tag in article.tags" :key="tag.id"
                  class="px-3 py-1 text-xs font-medium bg-[var(--color-surface-dark)] text-[var(--color-text-secondary)] rounded-full hover:bg-[var(--color-primary)] hover:text-white transition-colors cursor-pointer">
              {{ tag.name }}
            </span>
          </div>

          <!-- Related Articles -->
          <section v-if="relatedArticles.length" class="mt-10">
            <h3 class="text-xl font-black text-[var(--color-secondary)] mb-6 flex items-center gap-2">
              <span class="w-1 h-6 bg-[var(--color-primary)] rounded-full"></span>
              Berita Terkait
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <Link v-for="rel in relatedArticles" :key="rel.id"
                    :href="`/${rel.category?.slug}/${rel.slug}`"
                    class="article-card group bg-white rounded-xl overflow-hidden shadow-sm border border-[var(--color-border)]">
                <div class="aspect-[16/10] overflow-hidden">
                  <img :src="rel.featured_image || placeholderImage" :alt="rel.title"
                       class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                </div>
                <div class="p-4">
                  <h4 class="text-sm font-bold line-clamp-2 group-hover:text-[var(--color-primary)] transition-colors">{{ rel.title }}</h4>
                  <p class="text-xs text-[var(--color-text-muted)] mt-1.5">{{ formatDate(rel.published_at) }}</p>
                </div>
              </Link>
            </div>
          </section>
        </article>

        <!-- Sidebar -->
        <aside class="lg:col-span-4">
          <div class="sidebar-sticky space-y-6">
            <div v-if="ads?.sidebar_top" class="rounded-xl overflow-hidden">
              <a :href="ads.sidebar_top.target_url" target="_blank" rel="noopener sponsored">
                <img :src="ads.sidebar_top.image_path" :alt="ads.sidebar_top.name" class="w-full rounded-xl" />
              </a>
            </div>
            <div v-else class="bg-[var(--color-surface-dark)] rounded-xl h-64 flex items-center justify-center border border-dashed border-[var(--color-border)]">
              <span class="text-sm text-[var(--color-text-muted)]">Advertisement</span>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
  article: Object,
  relatedArticles: { type: Array, default: () => [] },
  recommendedArticles: { type: Array, default: () => [] },
  ads: Object,
  seo: Object,
  jsonLd: Object,
});

const placeholderImage = 'data:image/svg+xml;base64,' + btoa(`<svg xmlns="http://www.w3.org/2000/svg" width="800" height="500" viewBox="0 0 800 500"><rect fill="#e9ecef" width="800" height="500"/><text fill="#adb5bd" font-family="sans-serif" font-size="30" x="50%" y="50%" dominant-baseline="middle" text-anchor="middle">Celebesmedia.id</text></svg>`);

const formattedDate = computed(() => {
  const d = new Date(props.article.published_at || props.article.created_at);
  const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
  const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  return `${days[d.getDay()]}, ${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()} - ${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')} WITA`;
});

const embedUrl = computed(() => {
  const url = props.article.video_url || '';
  const match = url.match(/(?:youtu\.be\/|youtube\.com\/watch\?v=)([\w-]+)/);
  return match ? `https://www.youtube.com/embed/${match[1]}` : url;
});

function formatDate(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
  return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
}

function formatViews(v) {
  if (!v) return '0';
  if (v >= 1000) return (v / 1000).toFixed(1) + 'K';
  return v.toString();
}

function copyLink() {
  navigator.clipboard.writeText(window.location.href);
  alert('Link berhasil disalin!');
}
</script>
