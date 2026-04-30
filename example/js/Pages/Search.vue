<template>
  <PublicLayout>
    <Head>
      <title>{{ seo.title }}</title>
      <meta name="description" :content="seo.description" />
    </Head>

    <div class="max-w-7xl mx-auto px-4 py-6">
      <!-- Search Header -->
      <div class="mb-8">
        <form @submit.prevent="doSearch" class="max-w-xl mx-auto">
          <div class="relative">
            <input v-model="searchInput" type="text" placeholder="Cari berita, topik, atau kata kunci..."
                   class="w-full px-6 py-4 rounded-2xl bg-white border-2 border-[var(--color-border)] focus:border-[var(--color-primary)] focus:outline-none text-lg shadow-sm transition-all" />
            <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 p-2 text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </button>
          </div>
        </form>
      </div>

      <!-- Results -->
      <div v-if="query">
        <p class="text-sm text-[var(--color-text-secondary)] mb-6">
          Menampilkan hasil untuk: <strong class="text-[var(--color-text-primary)]">"{{ query }}"</strong>
          <span v-if="articles.data?.length"> ({{ articles.total || articles.data.length }} hasil)</span>
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <Link v-for="article in articles.data" :key="article.id"
                :href="`/${article.category?.slug}/${article.slug}`"
                class="article-card group bg-white rounded-xl overflow-hidden shadow-sm border border-[var(--color-border)]">
            <div class="aspect-[16/10] overflow-hidden">
              <img :src="article.featured_image || placeholderImage" :alt="article.title"
                   class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
            </div>
            <div class="p-4">
              <span class="text-[10px] font-bold uppercase tracking-wider" :style="{ color: article.category?.color }">
                {{ article.category?.name }}
              </span>
              <h3 class="text-base font-bold line-clamp-2 mt-1 group-hover:text-[var(--color-primary)] transition-colors">
                {{ article.title }}
              </h3>
              <p class="text-sm text-[var(--color-text-secondary)] line-clamp-2 mt-2">{{ article.excerpt }}</p>
            </div>
          </Link>
        </div>

        <div v-if="!articles.data?.length" class="text-center py-20">
          <svg class="w-16 h-16 mx-auto mb-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <p class="text-lg text-[var(--color-text-muted)]">Tidak ditemukan hasil untuk "{{ query }}"</p>
          <p class="text-sm text-[var(--color-text-muted)] mt-2">Coba kata kunci lain atau periksa ejaan.</p>
        </div>
      </div>

      <div v-else class="text-center py-20">
        <svg class="w-16 h-16 mx-auto mb-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <p class="text-lg text-[var(--color-text-muted)]">Ketik kata kunci untuk mencari berita</p>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
  query: { type: String, default: '' },
  articles: { type: [Object, Array], default: () => ({ data: [] }) },
  seo: Object,
});

const searchInput = ref(props.query);
const placeholderImage = 'data:image/svg+xml;base64,' + btoa(`<svg xmlns="http://www.w3.org/2000/svg" width="800" height="500" viewBox="0 0 800 500"><rect fill="#e9ecef" width="800" height="500"/><text fill="#adb5bd" font-family="sans-serif" font-size="30" x="50%" y="50%" dominant-baseline="middle" text-anchor="middle">Celebesmedia.id</text></svg>`);

function doSearch() {
  if (searchInput.value.trim()) {
    router.get('/cari', { q: searchInput.value });
  }
}
</script>
