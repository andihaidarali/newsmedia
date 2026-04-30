<template>
  <PublicLayout>
    <Head>
      <title>{{ seo.title }}</title>
      <meta name="description" :content="seo.description" />
      <meta property="og:title" :content="seo.title" />
      <meta property="og:description" :content="seo.description" />
      <meta property="og:url" :content="seo.url" />
      <meta property="og:type" content="website" />
      <meta property="og:site_name" content="Celebesmedia.id" />
      <meta name="twitter:card" content="summary_large_image" />
      <link rel="canonical" :href="seo.url" />
    </Head>

    <div class="max-w-7xl mx-auto px-4 py-6">
      <!-- Hero / Headlines Section -->
      <section v-if="headlines.length" class="mb-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
          <!-- Main Headline -->
          <div class="lg:col-span-7">
            <Link :href="articleUrl(headlines[0])" class="block group relative rounded-2xl overflow-hidden aspect-[16/10]">
              <div class="gradient-overlay w-full h-full">
                <img :src="headlines[0].featured_image || placeholderImage"
                     :alt="headlines[0].featured_image_alt || headlines[0].title"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
              </div>
              <div class="absolute bottom-0 left-0 right-0 p-6 z-10">
                <span class="category-badge mb-3" :style="{ background: headlines[0].category?.color }">
                  {{ headlines[0].category?.name }}
                </span>
                <h2 class="text-2xl md:text-3xl font-black text-white leading-tight mt-2 group-hover:text-[var(--color-primary)] transition-colors">
                  {{ headlines[0].title }}
                </h2>
                <p class="text-sm text-gray-300 mt-2 line-clamp-2">{{ headlines[0].excerpt }}</p>
                <div class="flex items-center gap-3 mt-3 text-xs text-gray-400">
                  <span>{{ headlines[0].author?.name }}</span>
                  <span>•</span>
                  <span>{{ formatDate(headlines[0].published_at) }}</span>
                </div>
              </div>
            </Link>
          </div>

          <!-- Side Headlines -->
          <div class="lg:col-span-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-4">
            <Link v-for="article in headlines.slice(1, 4)" :key="article.id"
                  :href="articleUrl(article)"
                  class="group flex gap-4 rounded-xl bg-white p-3 shadow-sm hover:shadow-md transition-all border border-[var(--color-border)]">
              <div class="w-28 h-20 rounded-lg overflow-hidden shrink-0">
                <img :src="article.featured_image || placeholderImage"
                     :alt="article.title"
                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
              </div>
              <div class="flex-1 min-w-0">
                <span class="text-[10px] font-bold uppercase tracking-wider" :style="{ color: article.category?.color }">
                  {{ article.category?.name }}
                </span>
                <h3 class="text-sm font-bold text-[var(--color-text-primary)] line-clamp-2 mt-0.5 group-hover:text-[var(--color-primary)] transition-colors">
                  {{ article.title }}
                </h3>
                <span class="text-[11px] text-[var(--color-text-muted)] mt-1 block">{{ formatDate(article.published_at) }}</span>
              </div>
            </Link>
          </div>
        </div>
      </section>

      <!-- Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-8">
          <!-- Latest News -->
          <section class="mb-10">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-xl font-black text-[var(--color-secondary)] flex items-center gap-2">
                <span class="w-1 h-6 bg-[var(--color-primary)] rounded-full"></span>
                Berita Terbaru
              </h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <Link v-for="article in latest" :key="article.id"
                    :href="articleUrl(article)"
                    class="article-card group bg-white rounded-xl overflow-hidden shadow-sm border border-[var(--color-border)]">
                <div class="aspect-[16/10] overflow-hidden relative">
                  <img :src="article.featured_image || placeholderImage"
                       :alt="article.title"
                       class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                  <span v-if="article.type === 'video'" class="absolute top-3 left-3 bg-red-600 text-white text-[10px] font-bold px-2 py-1 rounded-md flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> VIDEO
                  </span>
                  <span v-if="article.type === 'advertorial'" class="sponsored-label absolute top-3 right-3">Advertorial</span>
                </div>
                <div class="p-4">
                  <span class="text-[10px] font-bold uppercase tracking-wider" :style="{ color: article.category?.color }">
                    {{ article.category?.name }}
                  </span>
                  <h3 class="text-base font-bold text-[var(--color-text-primary)] line-clamp-2 mt-1 group-hover:text-[var(--color-primary)] transition-colors leading-snug">
                    {{ article.title }}
                  </h3>
                  <p class="text-sm text-[var(--color-text-secondary)] line-clamp-2 mt-2">{{ article.excerpt }}</p>
                  <div class="flex items-center justify-between mt-3">
                    <span class="text-xs text-[var(--color-text-muted)]">{{ formatDate(article.published_at) }}</span>
                    <span class="text-xs text-[var(--color-text-muted)] flex items-center gap-1">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                      {{ formatViews(article.views_count) }}
                    </span>
                  </div>
                </div>
              </Link>
            </div>
          </section>

          <!-- Category Sections -->
          <section v-for="section in categorySections" :key="section.category.id" class="mb-10">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-xl font-black text-[var(--color-secondary)] flex items-center gap-2">
                <span class="w-1 h-6 rounded-full" :style="{ background: section.category.color }"></span>
                {{ section.category.name }}
              </h2>
              <Link :href="`/kategori/${section.category.slug}`"
                    class="text-sm font-semibold text-[var(--color-primary)] hover:underline">
                Lihat Semua →
              </Link>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <Link v-for="article in section.articles" :key="article.id"
                    :href="articleUrl(article)"
                    class="group flex gap-3 p-3 rounded-lg hover:bg-[var(--color-surface-dark)] transition-colors">
                <div class="w-24 h-18 rounded-lg overflow-hidden shrink-0">
                  <img :src="article.featured_image || placeholderImage"
                       :alt="article.title"
                       class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                </div>
                <div class="flex-1 min-w-0">
                  <h3 class="text-sm font-bold text-[var(--color-text-primary)] line-clamp-2 group-hover:text-[var(--color-primary)] transition-colors">
                    {{ article.title }}
                  </h3>
                  <span class="text-[11px] text-[var(--color-text-muted)] mt-1 block">{{ formatDate(article.published_at) }}</span>
                </div>
              </Link>
            </div>
          </section>
        </div>

        <!-- Sidebar -->
        <aside class="lg:col-span-4">
          <div class="sidebar-sticky space-y-8">
            <!-- Ad Slot -->
            <div v-if="ads?.sidebar_top" class="rounded-xl overflow-hidden">
              <a :href="ads.sidebar_top.target_url" target="_blank" rel="noopener sponsored">
                <img :src="ads.sidebar_top.image_path" :alt="ads.sidebar_top.name" class="w-full rounded-xl" />
              </a>
            </div>
            <div v-else class="bg-[var(--color-surface-dark)] rounded-xl h-64 flex items-center justify-center border border-dashed border-[var(--color-border)]">
              <span class="text-sm text-[var(--color-text-muted)]">Advertisement</span>
            </div>

            <!-- Popular News -->
            <div class="bg-white rounded-xl p-5 shadow-sm border border-[var(--color-border)]">
              <h3 class="text-base font-black text-[var(--color-secondary)] mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-[var(--color-primary)]" fill="currentColor" viewBox="0 0 24 24"><path d="M12.89 1.45l8 8A2 2 0 0121 10.86V20a2 2 0 01-2 2H5a2 2 0 01-2-2v-9.14a2 2 0 01.11-.42l8-8a2 2 0 012.78 0z"/></svg>
                Terpopuler
              </h3>
              <div class="space-y-4">
                <Link v-for="(article, i) in popular" :key="article.id"
                      :href="articleUrl(article)"
                      class="group flex gap-3 items-start">
                  <span class="text-2xl font-black leading-none shrink-0 w-8"
                        :class="i === 0 ? 'text-[var(--color-primary)]' : 'text-[var(--color-surface-darker)]'">
                    {{ i + 1 }}
                  </span>
                  <div class="flex-1 min-w-0">
                    <span class="text-[10px] font-bold uppercase tracking-wider" :style="{ color: article.category?.color }">{{ article.category?.name }}</span>
                    <h4 class="text-sm font-semibold text-[var(--color-text-primary)] line-clamp-2 group-hover:text-[var(--color-primary)] transition-colors">
                      {{ article.title }}
                    </h4>
                    <span class="text-[11px] text-[var(--color-text-muted)]">{{ formatViews(article.views_count) }} views</span>
                  </div>
                </Link>
              </div>
            </div>

            <!-- Topics Cloud -->
            <div class="bg-white rounded-xl p-5 shadow-sm border border-[var(--color-border)]">
              <h3 class="text-base font-black text-[var(--color-secondary)] mb-4">Topik Populer</h3>
              <div class="flex flex-wrap gap-2">
                <span v-for="tag in popularTags" :key="tag"
                      class="px-3 py-1.5 text-xs font-medium bg-[var(--color-surface-dark)] text-[var(--color-text-secondary)] rounded-full hover:bg-[var(--color-primary)] hover:text-white transition-colors cursor-pointer">
                  {{ tag }}
                </span>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
  breakingNews: { type: Array, default: () => [] },
  headlines: { type: Array, default: () => [] },
  featured: { type: Array, default: () => [] },
  latest: { type: Array, default: () => [] },
  popular: { type: Array, default: () => [] },
  categorySections: { type: Array, default: () => [] },
  ads: { type: Object, default: () => ({}) },
  seo: { type: Object, default: () => ({}) },
});

const placeholderImage = 'data:image/svg+xml;base64,' + btoa(`<svg xmlns="http://www.w3.org/2000/svg" width="800" height="500" viewBox="0 0 800 500"><rect fill="#e9ecef" width="800" height="500"/><text fill="#adb5bd" font-family="sans-serif" font-size="30" x="50%" y="50%" dominant-baseline="middle" text-anchor="middle">Celebesmedia.id</text></svg>`);

const popularTags = ['Makassar', 'Sulawesi Selatan', 'Infrastruktur', 'UMKM', 'Pilkada', 'Pendidikan', 'Pariwisata', 'PSM', 'Teknologi', 'Kesehatan', 'Hukum', 'Lingkungan'];

function articleUrl(article) {
  const catSlug = article.category?.slug || 'uncategorized';
  return `/${catSlug}/${article.slug}`;
}

function formatDate(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  const now = new Date();
  const diff = (now - d) / 1000;

  if (diff < 3600) return `${Math.floor(diff / 60)} menit lalu`;
  if (diff < 86400) return `${Math.floor(diff / 3600)} jam lalu`;
  if (diff < 604800) return `${Math.floor(diff / 86400)} hari lalu`;

  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
  return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
}

function formatViews(views) {
  if (!views) return '0';
  if (views >= 1000000) return (views / 1000000).toFixed(1) + 'M';
  if (views >= 1000) return (views / 1000).toFixed(1) + 'K';
  return views.toString();
}
</script>
