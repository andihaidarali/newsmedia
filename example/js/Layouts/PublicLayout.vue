<template>
  <div class="min-h-screen flex flex-col bg-[var(--color-surface)]">
    <!-- Top Bar -->
    <div class="bg-[var(--color-secondary)] text-white text-xs">
      <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-8">
        <div class="flex items-center gap-4">
          <span class="opacity-70">{{ currentDate }}</span>
          <span class="opacity-50">|</span>
          <span class="opacity-70">Portal Berita Digital Terdepan</span>
        </div>
        <div class="flex items-center gap-3">
          <a v-for="social in socials" :key="social.name" :href="social.url" target="_blank"
             class="opacity-60 hover:opacity-100 transition-opacity" :title="social.name">
            <span v-html="social.icon" class="w-4 h-4"></span>
          </a>
        </div>
      </div>
    </div>

    <!-- Breaking News Ticker -->
    <div v-if="breakingExists" class="bg-[var(--color-primary)] text-white">
      <div class="max-w-7xl mx-auto px-4 flex items-center h-9">
        <span class="bg-white text-[var(--color-primary)] px-3 py-1 text-xs font-bold uppercase tracking-wider mr-3 rounded-sm shrink-0 animate-pulse">
          Breaking
        </span>
        <div class="ticker-wrap">
          <div class="ticker">
            <span v-for="item in tickerItems" :key="item" class="mx-8 text-sm">{{ item }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50 border-b border-[var(--color-border)]">
      <div class="max-w-7xl mx-auto px-4">
        <!-- Logo Row -->
        <div class="flex items-center justify-between h-16">
          <Link href="/" class="flex items-center gap-2 group">
            <div class="w-10 h-10 bg-[var(--color-primary)] rounded-lg flex items-center justify-center group-hover:scale-105 transition-transform">
              <span class="text-white font-black text-xl">C</span>
            </div>
            <div>
              <span class="text-xl font-black text-[var(--color-secondary)] tracking-tight">Celebes</span><span class="text-xl font-black text-[var(--color-primary)]">media</span><span class="text-sm text-[var(--color-text-muted)]">.id</span>
            </div>
          </Link>

          <!-- Search Bar -->
          <div class="hidden md:flex items-center flex-1 max-w-md mx-8">
            <form @submit.prevent="doSearch" class="w-full relative">
              <input v-model="searchQuery" type="text" placeholder="Cari berita..."
                     class="w-full pl-4 pr-10 py-2 rounded-full bg-[var(--color-surface-dark)] border border-transparent focus:border-[var(--color-primary)] focus:outline-none text-sm transition-all" />
              <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
              </button>
            </form>
          </div>

          <!-- Right Actions -->
          <div class="flex items-center gap-3">
            <Link v-if="$page.props.auth?.user" href="/dashboard"
                  class="hidden sm:inline-flex px-4 py-2 bg-[var(--color-secondary)] text-white text-sm font-medium rounded-lg hover:bg-[var(--color-accent)] transition-colors">
              Dashboard
            </Link>
            <Link v-else href="/login"
                  class="hidden sm:inline-flex px-4 py-2 text-sm font-medium text-[var(--color-text-secondary)] hover:text-[var(--color-primary)] transition-colors">
              Login
            </Link>
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-[var(--color-text-secondary)]">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Navigation -->
        <nav class="hidden md:flex items-center gap-0.5 -mb-px overflow-x-auto scrollbar-none">
          <Link href="/"
                class="px-3 py-3 text-sm font-semibold whitespace-nowrap transition-colors border-b-2"
                :class="isActive('/') ? 'text-[var(--color-primary)] border-[var(--color-primary)]' : 'text-[var(--color-text-secondary)] border-transparent hover:text-[var(--color-primary)] hover:border-[var(--color-primary)]'">
            Beranda
          </Link>
          <Link v-for="cat in navigation" :key="cat.id" :href="`/kategori/${cat.slug}`"
                class="px-3 py-3 text-sm font-semibold whitespace-nowrap transition-colors border-b-2"
                :class="isActive(`/kategori/${cat.slug}`) ? 'text-[var(--color-primary)] border-[var(--color-primary)]' : 'text-[var(--color-text-secondary)] border-transparent hover:text-[var(--color-primary)] hover:border-[var(--color-primary)]'">
            {{ cat.name }}
          </Link>
        </nav>
      </div>

      <!-- Mobile Menu -->
      <div v-if="mobileMenuOpen" class="md:hidden bg-white border-t border-[var(--color-border)] shadow-lg">
        <div class="px-4 py-3">
          <form @submit.prevent="doSearch" class="mb-3">
            <input v-model="searchQuery" type="text" placeholder="Cari berita..."
                   class="w-full px-4 py-2 rounded-lg bg-[var(--color-surface-dark)] text-sm" />
          </form>
          <Link href="/" class="block py-2 text-sm font-medium" @click="mobileMenuOpen = false">Beranda</Link>
          <Link v-for="cat in navigation" :key="cat.id" :href="`/kategori/${cat.slug}`"
                class="block py-2 text-sm font-medium text-[var(--color-text-secondary)] hover:text-[var(--color-primary)]"
                @click="mobileMenuOpen = false">
            {{ cat.name }}
          </Link>
        </div>
      </div>
    </header>

    <!-- Flash Messages -->
    <div v-if="$page.props.flash?.success" class="bg-[var(--color-success)] text-white px-4 py-3 text-sm text-center">
      {{ $page.props.flash.success }}
    </div>

    <!-- Main Content -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-[var(--color-secondary)] text-white mt-12">
      <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <!-- Brand -->
          <div class="md:col-span-1">
            <Link href="/" class="flex items-center gap-2 mb-4">
              <div class="w-8 h-8 bg-[var(--color-primary)] rounded-lg flex items-center justify-center">
                <span class="text-white font-black text-lg">C</span>
              </div>
              <span class="text-lg font-black">Celebesmedia<span class="text-[var(--color-primary)]">.id</span></span>
            </Link>
            <p class="text-sm text-gray-400 leading-relaxed">Portal berita digital terdepan dari Sulawesi. Menyajikan berita cepat, akurat, dan terpercaya.</p>
          </div>

          <!-- Categories -->
          <div>
            <h4 class="text-sm font-bold uppercase tracking-wider mb-4 text-[var(--color-primary)]">Kategori</h4>
            <div class="grid grid-cols-2 gap-1">
              <Link v-for="cat in navigation?.slice(0, 8)" :key="cat.id" :href="`/kategori/${cat.slug}`"
                    class="text-sm text-gray-400 hover:text-white transition-colors py-0.5">
                {{ cat.name }}
              </Link>
            </div>
          </div>

          <!-- Menu -->
          <div>
            <h4 class="text-sm font-bold uppercase tracking-wider mb-4 text-[var(--color-primary)]">Menu</h4>
            <Link href="/halaman/tentang-kami" class="block text-sm text-gray-400 hover:text-white transition-colors py-0.5">Tentang Kami</Link>
            <Link href="/halaman/redaksi" class="block text-sm text-gray-400 hover:text-white transition-colors py-0.5">Redaksi</Link>
            <Link href="/halaman/pedoman-media-siber" class="block text-sm text-gray-400 hover:text-white transition-colors py-0.5">Pedoman Media Siber</Link>
            <Link href="/halaman/celebes-tv" class="block text-sm text-gray-400 hover:text-white transition-colors py-0.5">Celebes TV</Link>
            <Link href="/halaman/celebes-radio" class="block text-sm text-gray-400 hover:text-white transition-colors py-0.5">Celebes Radio</Link>
          </div>

          <!-- Contact -->
          <div>
            <h4 class="text-sm font-bold uppercase tracking-wider mb-4 text-[var(--color-primary)]">Kontak</h4>
            <p class="text-sm text-gray-400 mb-1">Email: redaksi@celebesmedia.id</p>
            <p class="text-sm text-gray-400 mb-3">Makassar, Sulawesi Selatan</p>
            <div class="flex items-center gap-3">
              <a v-for="social in socials" :key="social.name" :href="social.url" target="_blank"
                 class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center hover:bg-[var(--color-primary)] transition-colors">
                <span v-html="social.icon" class="w-4 h-4 text-white"></span>
              </a>
            </div>
          </div>
        </div>

        <div class="border-t border-white/10 mt-8 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
          <p class="text-xs text-gray-500">&copy; {{ new Date().getFullYear() }} Celebesmedia.id. All rights reserved.</p>
          <p class="text-xs text-gray-500">Member of <strong class="text-gray-400">Dewan Pers Indonesia</strong></p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';

const page = usePage();
const navigation = computed(() => page.props.navigation || []);
const mobileMenuOpen = ref(false);
const searchQuery = ref('');

const currentDate = computed(() => {
  const d = new Date();
  const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
  const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  return `${days[d.getDay()]}, ${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
});

const breakingExists = computed(() => {
  return page.props.breakingNews?.length > 0;
});

const tickerItems = computed(() => {
  const items = page.props.breakingNews?.map(a => `🔴 ${a.title}`) || [];
  return [...items, ...items]; // duplicate for seamless scroll
});

const socials = [
  { name: 'Facebook', url: '#', icon: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>' },
  { name: 'Twitter', url: '#', icon: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>' },
  { name: 'Instagram', url: '#', icon: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>' },
  { name: 'YouTube', url: '#', icon: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>' },
];

function isActive(path) {
  return page.url === path || page.url?.startsWith(path + '/');
}

function doSearch() {
  if (searchQuery.value.trim()) {
    router.get('/cari', { q: searchQuery.value });
  }
}
</script>
