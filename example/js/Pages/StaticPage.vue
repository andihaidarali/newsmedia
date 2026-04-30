<template>
  <PublicLayout>
    <Head>
      <title>{{ seo.title }}</title>
      <meta name="description" :content="seo.description" />
    </Head>

    <div class="max-w-4xl mx-auto px-4 py-8">
      <h1 class="text-3xl font-black text-[var(--color-secondary)] mb-6">{{ page.title }}</h1>

      <!-- Static Page -->
      <div v-if="page.type === 'static'" class="prose-article" v-html="page.content"></div>

      <!-- Iframe Page -->
      <div v-else-if="page.type === 'iframe'" class="rounded-xl overflow-hidden">
        <iframe :src="page.iframe_url" class="w-full min-h-[600px] border-0 rounded-xl" allowfullscreen></iframe>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
  page: Object,
  seo: Object,
});
</script>
