<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[var(--color-secondary)] via-[var(--color-accent)] to-[var(--color-secondary-light)]">
    <Head><title>Login - Celebesmedia.id</title></Head>

    <div class="w-full max-w-md mx-4">
      <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-8 shadow-2xl border border-white/10">
        <!-- Logo -->
        <div class="text-center mb-8">
          <Link href="/" class="inline-flex items-center gap-2">
            <div class="w-12 h-12 bg-[var(--color-primary)] rounded-xl flex items-center justify-center">
              <span class="text-white font-black text-2xl">C</span>
            </div>
          </Link>
          <h1 class="text-2xl font-black text-white mt-4">
            Celebes<span class="text-[var(--color-primary)]">media</span>.id
          </h1>
          <p class="text-sm text-gray-400 mt-1">Masuk ke Dashboard</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="submit" class="space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Email</label>
            <input v-model="form.email" type="email" required autofocus
                   class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-gray-500 focus:border-[var(--color-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)] transition-all"
                   placeholder="nama@celebesmedia.id" />
            <p v-if="form.errors.email" class="text-xs text-red-400 mt-1">{{ form.errors.email }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Password</label>
            <input v-model="form.password" type="password" required
                   class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-gray-500 focus:border-[var(--color-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)] transition-all"
                   placeholder="••••••••" />
          </div>

          <div class="flex items-center">
            <label class="flex items-center gap-2 cursor-pointer">
              <input v-model="form.remember" type="checkbox" class="w-4 h-4 rounded border-white/20 text-[var(--color-primary)] focus:ring-[var(--color-primary)]" />
              <span class="text-sm text-gray-400">Ingat saya</span>
            </label>
          </div>

          <button type="submit" :disabled="form.processing"
                  class="w-full py-3 bg-[var(--color-primary)] text-white font-bold rounded-xl hover:bg-[var(--color-primary-dark)] transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-[var(--color-primary)]/25">
            <span v-if="form.processing">Memproses...</span>
            <span v-else>Masuk</span>
          </button>
        </form>

        <p class="text-center text-xs text-gray-500 mt-6">
          <Link href="/" class="text-[var(--color-primary)] hover:underline">← Kembali ke Beranda</Link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

function submit() {
  form.post('/login', {
    onFinish: () => form.reset('password'),
  });
}
</script>
