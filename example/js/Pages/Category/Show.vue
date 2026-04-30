<template>
  <PublicLayout>
    <Head>
      <title>{{ seo.title }}</title>
      <meta name="description" :content="seo.description" />
      <meta property="og:title" :content="seo.title" />
      <meta property="og:description" :content="seo.description" />
      <meta property="og:url" :content="seo.url" />
      <link rel="canonical" :href="seo.url" />
    </Head>

    <div class="max-w-7xl mx-auto px-4 py-6">
      <!-- Header -->
      <div class="mb-8">
        <nav class="flex items-center text-sm mb-4 text-[var(--color-text-muted)]">
          <Link href="/" class="hover:text-[var(--color-primary)]">Beranda</Link>
          <span class="breadcrumb-item"></span>
          <span class="text-[var(--color-text-secondary)]">{{ category.name }}</span>
        </nav>
        <div class="flex items-center gap-3">
          <span class="w-1.5 h-10 rounded-full" :style="{ background: category.color }"></span>
          <div>
            <h1 class="text-3xl font-black text-[var(--color-secondary)]">{{ category.name }}</h1>
            <p v-if="category.description" class="text-sm text-[var(--color-text-secondary)] mt-1">{{ category.description }}</p>
          </div>
        </div>
      </div>

      <!-- Articles Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <Link v-for="article in articles.data" :key="article.id"
              :href="`/${category.slug}/${article.slug}`"
              class="article-card group bg-white rounded-xl overflow-hidden shadow-sm border border-[var(--color-border)]">
          <div class="aspect-[16/10] overflow-hidden relative">
            <img :src="article.featured_image || placeholderImage" :alt="article.title"
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
            <span v-if="article.type === 'video'" class="absolute top-3 left-3 bg-red-600 text-white text-[10px] font-bold px-2 py-1 rounded-md flex items-center gap-1">
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> VIDEO
            </span>
            <span v-if="article.type === 'advertorial'" class="sponsored-label absolute top-3 right-3">Advertorial</span>
          </div>
          <div class="p-4">
            <h3 class="text-base font-bold line-clamp-2 group-hover:text-[var(--color-primary)] transition-colors leading-snug">
              {{ article.title }}
            </h3>
            <p class="text-sm text-[var(--color-text-secondary)] line-clamp-2 mt-2">{{ article.excerpt }}</p>
            <div class="flex items-center justify-between mt-3 text-xs text-[var(--color-text-muted)]">
              <span>{{ article.author?.name }}</span>
              <span>{{ formatDate(article.published_at) }}</span>
            </div>
          </div>
        </Link>
      </div>

      <!-- Empty State -->
      <div v-if="!articles.data?.length" class="text-center py-20">
        <p class="text-lg text-[var(--color-text-muted)]">Belum ada artikel di kategori ini.</p>
      </div>

      <!-- Pagination -->
      <div v-if="articles.links?.length > 3" class="flex justify-center mt-10 gap-1">
        <template v-for="link in articles.links" :key="link.label">
          <Link v-if="link.url"
                :href="link.url"
                class="px-4 py-2 text-sm rounded-lg transition-colors"
                :class="link.active
                  ? 'bg-[var(--color-primary)] text-white'
                  : 'bg-[var(--color-surface-dark)] text-[var(--color-text-secondary)] hover:bg-[var(--color-primary)] hover:text-white'"
                v-html="link.label" />
          <span v-else class="px-4 py-2 text-sm text-[var(--color-text-muted)]" v-html="link.label" />
        </template>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
  category: Object,
  articles: Object,
  seo: Object,
});

const placeholderImage = 'data:image/svg+xml;base64,' + btoa(`<svg xmlns="http://www.w3.org/2000/svg" width="800" height="500" viewBox="0 0 800 500"><rect fill="#e9ecef" width="800" height="500"/><text fill="#adb5bd" font-family="sans-serif" font-size="30" x="50%" y="50%" dominant-baseline="middle" text-anchor="middle">Celebesmedia.id</text></svg>`);

function formatDate(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
  return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
}
</script>
