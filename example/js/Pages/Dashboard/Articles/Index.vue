<template>
  <DashboardLayout>
    <Head><title>Kelola Artikel</title></Head>

    <div>
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-black text-[var(--color-secondary)]">Kelola Artikel</h1>
        <Link href="/dashboard/articles/create"
              class="px-4 py-2.5 bg-[var(--color-primary)] text-white text-sm font-bold rounded-xl hover:bg-[var(--color-primary-dark)] transition-colors shadow-lg shadow-[var(--color-primary)]/25">
          + Tulis Artikel
        </Link>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-xl p-4 mb-6 flex flex-wrap gap-3 items-center shadow-sm border border-[var(--color-border)]">
        <input v-model="filters.search" type="text" placeholder="Cari judul..."
               class="flex-1 min-w-[200px] px-4 py-2 rounded-lg bg-[var(--color-surface-dark)] text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
               @input="debouncedSearch" />
        <select v-model="filters.status" @change="applyFilters"
                class="px-3 py-2 rounded-lg bg-[var(--color-surface-dark)] text-sm focus:outline-none">
          <option value="">Semua Status</option>
          <option value="published">Published</option>
          <option value="draft">Draft</option>
          <option value="review">Review</option>
        </select>
        <select v-model="filters.type" @change="applyFilters"
                class="px-3 py-2 rounded-lg bg-[var(--color-surface-dark)] text-sm focus:outline-none">
          <option value="">Semua Tipe</option>
          <option value="text">Text</option>
          <option value="video">Video</option>
          <option value="gallery">Gallery</option>
          <option value="infographic">Infografis</option>
          <option value="advertorial">Advertorial</option>
        </select>
      </div>

      <!-- Articles Table -->
      <div class="bg-white rounded-xl shadow-sm border border-[var(--color-border)] overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-[var(--color-surface-dark)]">
              <tr>
                <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-[var(--color-text-muted)]">Judul</th>
                <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-[var(--color-text-muted)]">Kategori</th>
                <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-[var(--color-text-muted)]">Tipe</th>
                <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-[var(--color-text-muted)]">Status</th>
                <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider text-[var(--color-text-muted)]">Views</th>
                <th class="text-right px-5 py-3 text-xs font-bold uppercase tracking-wider text-[var(--color-text-muted)]">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-border)]">
              <tr v-for="article in articles.data" :key="article.id"
                  class="hover:bg-[var(--color-surface-dark)] transition-colors">
                <td class="px-5 py-3">
                  <p class="font-medium text-[var(--color-text-primary)] max-w-xs truncate">{{ article.title }}</p>
                  <p class="text-[11px] text-[var(--color-text-muted)]">{{ article.author?.name }} · {{ formatDate(article.created_at) }}</p>
                </td>
                <td class="px-5 py-3">
                  <span class="text-xs font-bold" :style="{ color: article.category?.color }">{{ article.category?.name }}</span>
                </td>
                <td class="px-5 py-3">
                  <span class="text-xs uppercase font-medium text-[var(--color-text-secondary)]">{{ article.type }}</span>
                </td>
                <td class="px-5 py-3">
                  <span class="text-[10px] font-bold uppercase px-2 py-1 rounded-md"
                        :class="{
                          'bg-green-100 text-green-700': article.status === 'published',
                          'bg-yellow-100 text-yellow-700': article.status === 'draft',
                          'bg-blue-100 text-blue-700': article.status === 'review',
                        }">
                    {{ article.status }}
                  </span>
                </td>
                <td class="px-5 py-3 text-xs text-[var(--color-text-muted)]">{{ article.views_count }}</td>
                <td class="px-5 py-3 text-right">
                  <Link :href="`/dashboard/articles/${article.id}/edit`"
                        class="text-xs font-medium text-[var(--color-primary)] hover:underline mr-3">Edit</Link>
                  <button @click="deleteArticle(article.id)"
                          class="text-xs font-medium text-red-500 hover:underline">Hapus</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="articles.links?.length > 3" class="flex justify-center mt-6 gap-1">
        <template v-for="link in articles.links" :key="link.label">
          <Link v-if="link.url" :href="link.url"
                class="px-3 py-1.5 text-sm rounded-lg"
                :class="link.active ? 'bg-[var(--color-primary)] text-white' : 'bg-white text-[var(--color-text-secondary)] hover:bg-[var(--color-primary)] hover:text-white'"
                v-html="link.label" />
        </template>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
  articles: Object,
  filters: Object,
});

const filters = reactive({
  search: props.filters?.search || '',
  status: props.filters?.status || '',
  type: props.filters?.type || '',
});

let searchTimeout = null;
function debouncedSearch() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(applyFilters, 400);
}

function applyFilters() {
  router.get('/dashboard/articles', {
    search: filters.search || undefined,
    status: filters.status || undefined,
    type: filters.type || undefined,
  }, { preserveState: true, preserveScroll: true });
}

function deleteArticle(id) {
  if (confirm('Yakin ingin menghapus artikel ini?')) {
    router.delete(`/dashboard/articles/${id}`);
  }
}

function formatDate(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  return `${d.getDate()}/${d.getMonth() + 1}/${d.getFullYear()}`;
}
</script>
