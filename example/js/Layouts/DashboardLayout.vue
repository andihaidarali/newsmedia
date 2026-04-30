<template>
  <div class="min-h-screen bg-[var(--color-surface-dark)]">
    <!-- Top Bar -->
    <header class="bg-[var(--color-secondary)] text-white h-14 flex items-center px-4 sticky top-0 z-50 shadow-lg">
      <button @click="sidebarOpen = !sidebarOpen" class="p-2 mr-3 lg:hidden hover:bg-white/10 rounded-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
      <Link href="/dashboard" class="flex items-center gap-2 font-bold">
        <div class="w-7 h-7 bg-[var(--color-primary)] rounded-md flex items-center justify-center text-sm font-black">C</div>
        <span class="text-sm">CMS <span class="text-[var(--color-primary)]">Dashboard</span></span>
      </Link>
      <div class="flex-1"></div>
      <Link href="/" class="text-xs text-gray-400 hover:text-white mr-4" target="_blank">
        ← Lihat Situs
      </Link>
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-full bg-[var(--color-primary)] flex items-center justify-center text-xs font-bold">
          {{ $page.props.auth?.user?.name?.charAt(0) || 'U' }}
        </div>
        <div class="hidden sm:block text-xs">
          <p class="font-medium">{{ $page.props.auth?.user?.name }}</p>
          <p class="text-gray-400 text-[10px]">{{ $page.props.auth?.user?.roles?.[0] || 'User' }}</p>
        </div>
      </div>
    </header>

    <div class="flex">
      <!-- Sidebar -->
      <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
             class="fixed lg:sticky top-14 left-0 w-60 h-[calc(100vh-3.5rem)] bg-white border-r border-[var(--color-border)] z-40 transition-transform overflow-y-auto">
        <nav class="p-3 space-y-1">
          <Link href="/dashboard"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors"
                :class="isActive('/dashboard') && !isActive('/dashboard/articles') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)]' : 'text-[var(--color-text-secondary)] hover:bg-[var(--color-surface-dark)]'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
          </Link>

          <p class="text-[10px] font-bold text-[var(--color-text-muted)] uppercase tracking-wider px-3 pt-4 pb-1">Konten</p>

          <Link href="/dashboard/articles"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors"
                :class="isActive('/dashboard/articles') ? 'bg-[var(--color-primary)]/10 text-[var(--color-primary)]' : 'text-[var(--color-text-secondary)] hover:bg-[var(--color-surface-dark)]'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
            Artikel
          </Link>

          <Link href="/dashboard/articles/create"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-[var(--color-text-secondary)] hover:bg-[var(--color-surface-dark)] transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tulis Artikel
          </Link>
        </nav>

        <!-- Bottom -->
        <div class="absolute bottom-0 left-0 right-0 p-3 border-t border-[var(--color-border)]">
          <form @submit.prevent="logout">
            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-red-500 hover:bg-red-50 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
              Keluar
            </button>
          </form>
        </div>
      </aside>

      <!-- Overlay -->
      <div v-if="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-30 lg:hidden"></div>

      <!-- Main Content -->
      <main class="flex-1 p-6 min-h-[calc(100vh-3.5rem)]">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';

const sidebarOpen = ref(false);
const page = usePage();

function isActive(path) {
  return page.url === path || page.url?.startsWith(path + '?') || page.url?.startsWith(path + '/');
}

function logout() {
  router.post('/logout');
}
</script>
