<template>
  <DashboardLayout>
    <Head><title>Edit Artikel</title></Head>

    <div class="max-w-4xl">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-black text-[var(--color-secondary)]">Edit Artikel</h1>
        <Link href="/dashboard/articles" class="text-sm text-[var(--color-text-secondary)] hover:text-[var(--color-primary)]">← Kembali</Link>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-[var(--color-border)]">
          <label class="block text-sm font-bold text-[var(--color-secondary)] mb-2">Judul Artikel *</label>
          <input v-model="form.title" type="text" required class="w-full px-4 py-3 rounded-xl bg-[var(--color-surface-dark)] text-lg font-semibold focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="bg-white rounded-xl p-5 shadow-sm border border-[var(--color-border)]">
            <label class="block text-sm font-bold text-[var(--color-secondary)] mb-2">Tipe</label>
            <select v-model="form.type" class="w-full px-4 py-3 rounded-xl bg-[var(--color-surface-dark)] text-sm focus:outline-none">
              <option value="text">Text</option>
              <option value="video">Video</option>
              <option value="gallery">Gallery</option>
              <option value="infographic">Infografis</option>
              <option value="advertorial">Advertorial</option>
            </select>
          </div>
          <div class="bg-white rounded-xl p-5 shadow-sm border border-[var(--color-border)]">
            <label class="block text-sm font-bold text-[var(--color-secondary)] mb-2">Kategori</label>
            <select v-model="form.category_id" class="w-full px-4 py-3 rounded-xl bg-[var(--color-surface-dark)] text-sm focus:outline-none">
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
          </div>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border border-[var(--color-border)]">
          <label class="block text-sm font-bold text-[var(--color-secondary)] mb-2">Ringkasan</label>
          <textarea v-model="form.excerpt" rows="3" class="w-full px-4 py-3 rounded-xl bg-[var(--color-surface-dark)] text-sm focus:outline-none resize-none"></textarea>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border border-[var(--color-border)]">
          <label class="block text-sm font-bold text-[var(--color-secondary)] mb-2">Konten *</label>
          <textarea v-model="form.content" rows="15" required class="w-full px-4 py-3 rounded-xl bg-[var(--color-surface-dark)] text-sm font-mono focus:outline-none resize-y"></textarea>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border border-[var(--color-border)]">
          <label class="block text-sm font-bold text-[var(--color-secondary)] mb-2">Tags</label>
          <div class="flex flex-wrap gap-2">
            <label v-for="tag in tags" :key="tag.id"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs cursor-pointer transition-all"
                   :class="form.tags.includes(tag.id) ? 'bg-[var(--color-primary)] text-white' : 'bg-[var(--color-surface-dark)] text-[var(--color-text-secondary)]'">
              <input type="checkbox" :value="tag.id" v-model="form.tags" class="hidden" />
              {{ tag.name }}
            </label>
          </div>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border border-[var(--color-border)]">
          <div class="flex flex-wrap gap-6">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" v-model="form.is_featured" class="w-4 h-4 rounded text-[var(--color-primary)]" />
              <span class="text-sm">Featured</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" v-model="form.is_breaking" class="w-4 h-4 rounded text-[var(--color-primary)]" />
              <span class="text-sm">Breaking</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" v-model="form.is_headline" class="w-4 h-4 rounded text-[var(--color-primary)]" />
              <span class="text-sm">Headline</span>
            </label>
          </div>
        </div>

        <div class="flex items-center gap-3 justify-end">
          <button type="submit" @click="form.status = 'draft'" :disabled="form.processing"
                  class="px-6 py-3 bg-[var(--color-surface-dark)] text-[var(--color-text-secondary)] font-bold text-sm rounded-xl hover:bg-[var(--color-surface-darker)] transition-colors">
            Simpan Draf
          </button>
          <button type="submit" @click="form.status = 'published'" :disabled="form.processing"
                  class="px-6 py-3 bg-[var(--color-primary)] text-white font-bold text-sm rounded-xl hover:bg-[var(--color-primary-dark)] transition-colors shadow-lg shadow-[var(--color-primary)]/25">
            Terbitkan
          </button>
        </div>
      </form>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
  article: Object,
  categories: Array,
  tags: Array,
});

const form = useForm({
  title: props.article.title,
  content: props.article.content,
  excerpt: props.article.excerpt || '',
  type: props.article.type,
  category_id: props.article.category_id,
  video_url: props.article.video_url || '',
  tags: props.article.tags?.map(t => t.id) || [],
  status: props.article.status,
  is_featured: props.article.is_featured,
  is_breaking: props.article.is_breaking,
  is_headline: props.article.is_headline,
});

function submit() {
  form.put(`/dashboard/articles/${props.article.id}`);
}
</script>
