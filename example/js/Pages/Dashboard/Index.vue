<template>
  <DashboardLayout>
    <Head><title>Dashboard</title></Head>

    <div>
      <h1 class="text-2xl font-black text-[var(--color-secondary)] mb-6">Dashboard Overview</h1>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-[var(--color-border)]">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-[var(--color-text-muted)]">Total Artikel</p>
              <p class="text-3xl font-black text-[var(--color-secondary)] mt-1">{{ stats.total_articles }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-[var(--color-border)]">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-[var(--color-text-muted)]">Diterbitkan</p>
              <p class="text-3xl font-black text-[var(--color-success)] mt-1">{{ stats.published_articles }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
              <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-[var(--color-border)]">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-[var(--color-text-muted)]">Draf</p>
              <p class="text-3xl font-black text-[var(--color-warning)] mt-1">{{ stats.draft_articles }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center">
              <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-[var(--color-border)]">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-[var(--color-text-muted)]">Total Views</p>
              <p class="text-3xl font-black text-[var(--color-primary)] mt-1">{{ formatViews(stats.total_views) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center">
              <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Articles -->
        <div class="bg-white rounded-xl shadow-sm border border-[var(--color-border)]">
          <div class="px-5 py-4 border-b border-[var(--color-border)]">
            <h2 class="font-bold text-[var(--color-secondary)]">Artikel Terbaru</h2>
          </div>
          <div class="divide-y divide-[var(--color-border)]">
            <div v-for="article in recentArticles" :key="article.id" class="px-5 py-3 flex items-center justify-between hover:bg-[var(--color-surface-dark)] transition-colors">
              <div class="min-w-0 flex-1 mr-3">
                <p class="text-sm font-medium text-[var(--color-text-primary)] truncate">{{ article.title }}</p>
                <div class="flex items-center gap-2 mt-0.5">
                  <span class="text-[10px] font-bold uppercase" :style="{ color: article.category?.color }">{{ article.category?.name }}</span>
                  <span class="text-[10px] text-[var(--color-text-muted)]">{{ article.author?.name }}</span>
                </div>
              </div>
              <span class="shrink-0 text-[10px] font-bold uppercase px-2 py-1 rounded-md"
                    :class="{
                      'bg-green-100 text-green-700': article.status === 'published',
                      'bg-yellow-100 text-yellow-700': article.status === 'draft',
                      'bg-blue-100 text-blue-700': article.status === 'review',
                    }">
                {{ article.status }}
              </span>
            </div>
          </div>
        </div>

        <!-- Top Articles -->
        <div class="bg-white rounded-xl shadow-sm border border-[var(--color-border)]">
          <div class="px-5 py-4 border-b border-[var(--color-border)]">
            <h2 class="font-bold text-[var(--color-secondary)]">Artikel Terpopuler</h2>
          </div>
          <div class="divide-y divide-[var(--color-border)]">
            <div v-for="(article, i) in topArticles" :key="article.id" class="px-5 py-3 flex items-center gap-3 hover:bg-[var(--color-surface-dark)] transition-colors">
              <span class="text-xl font-black w-6 text-center" :class="i === 0 ? 'text-[var(--color-primary)]' : 'text-[var(--color-surface-darker)]'">{{ i + 1 }}</span>
              <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-[var(--color-text-primary)] truncate">{{ article.title }}</p>
                <span class="text-[10px] text-[var(--color-text-muted)]">{{ formatViews(article.views_count) }} views</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

defineProps({
  stats: Object,
  recentArticles: Array,
  topArticles: Array,
});

function formatViews(v) {
  if (!v) return '0';
  if (v >= 1000000) return (v / 1000000).toFixed(1) + 'M';
  if (v >= 1000) return (v / 1000).toFixed(1) + 'K';
  return v.toString();
}
</script>
