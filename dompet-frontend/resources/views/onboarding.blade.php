@extends('layouts.app')

@section('title', 'Mulai - Zaku')

@section('content')
<div x-data="onboardingPage()" x-init="init()" class="min-h-screen bg-[var(--paper)]">
    <!-- Progress Bar -->
    <div class="fixed top-0 left-0 right-0 z-50 h-1 bg-[var(--border)]" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="3" aria-label="Kemajuan onboarding">
        <div :style="{ width: (currentStep / 3 * 100) + '%' }" class="h-full bg-[var(--ink)] transition-all duration-300" aria-hidden="true"></div>
    </div>

    <main class="pt-6 pb-12 px-4 max-w-md mx-auto">
        <!-- Step Indicator -->
        <div class="flex items-center justify-between mb-8" role="tablist" aria-label="Langkah onboarding">
            <template x-for="(step, index) in steps" :key="step.id">
                <div class="flex flex-col items-center relative" :class="{ 'flex-1': index < steps.length - 1 }">
                    <div 
                        class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300 z-10"
                        :class="{
                            'bg-[var(--ink)] text-[var(--paper)]': currentStep > index + 1,
                            'bg-[var(--ink)] text-[var(--paper)] ring-4 ring-[var(--ink)]/20': currentStep === index + 1,
                            'bg-[var(--paper)] text-[var(--muted)] border-2 border-[var(--border)]': currentStep < index + 1
                        }"
                        :aria-current="currentStep === index + 1 ? 'step' : 'false"
                        :aria-label="'Langkah ' + (index + 1) + ': ' + step.title"
                    >
                        <span x-show="currentStep > index + 1">✓</span>
                        <span x-show="currentStep <= index + 1" x-text="index + 1"></span>
                    </div>
                    <p class="text-xs text-[var(--muted)] mt-2 text-center max-w-xs" x-text="step.title"></p>
                    <div x-show="index < steps.length - 1" class="absolute top-5 left-1/2 w-full h-1 bg-[var(--border)] -z-10" style="transform: translateX(-50%);"></div>
                </div>
            </template>
        </div>

        <!-- Step Content -->
        <div class="bg-[var(--paper)] border border-[var(--border)] rounded-xl p-6 shadow-[var(--shadow-sm)]" role="tabpanel" :aria-labelledby="'step-' + currentStep">
            
            <!-- Step 1: Profile -->
            <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 translate-y-2">
                <h2 class="text-xl font-bold text-[var(--ink)] mb-2">Profil Dasar</h2>
                <p class="text-[var(--muted)] mb-6">Isi nama dan nomor telepon agar akunmu lengkap.</p>

                <form @submit.prevent="saveStep1">
                    <div class="space-y-4">
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-[var(--ink)] mb-1">Nama Lengkap <span class="text-[var(--orange)]">*</span></label>
                            <input 
                                type="text" 
                                id="full_name" 
                                x-model="form.full_name"
                                class="w-full px-4 py-3 border border-[var(--border)] rounded-lg bg-[var(--paper)] text-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--ink)] focus:border-transparent transition"
                                placeholder="Nama lengkap kamu"
                                required
                                autocomplete="name"
                                @input="$watch('form.full_name', v => form.full_name = v.trim())"
                            >
                            <p x-show="errors.full_name" class="text-sm text-[var(--orange)] mt-1" x-text="errors.full_name"></p>
                        </div>

                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-[var(--ink)] mb-1">Nomor Telepon</label>
                            <input 
                                type="tel" 
                                id="phone_number" 
                                x-model="form.phone_number"
                                class="w-full px-4 py-3 border border-[var(--border)] rounded-lg bg-[var(--paper)] text-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--ink)] focus:border-transparent transition"
                                placeholder="08xx-xxxx-xxxx"
                                autocomplete="tel"
                            >
                            <p x-show="errors.phone_number" class="text-sm text-[var(--orange)] mt-1" x-text="errors.phone_number"></p>
                        </div>
                    </div>

                    <div class="mt-8 flex gap-3">
                        <button 
                            type="button" 
                            @click="prevStep"
                            class="flex-1 px-4 py-3 border border-[var(--border)] rounded-lg text-[var(--ink)] hover:bg-[var(--muted)]/20 transition disabled:opacity-50"
                            :disabled="currentStep === 1"
                        >
                            Kembali
                        </button>
                        <button 
                            type="submit" 
                            :disabled="saving"
                            class="flex-1 px-4 py-3 bg-[var(--ink)] text-[var(--paper)] rounded-lg hover:opacity-90 transition disabled:opacity-50 font-medium"
                        >
                            <span x-show="!saving">Lanjutkan</span>
                            <span x-show="saving" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 2: Budget & Preferences -->
            <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 translate-y-2">
                <h2 class="text-xl font-bold text-[var(--ink)] mb-2">Anggaran & Preferensi</h2>
                <p class="text-[var(--muted)] mb-6">Atur batas pengeluaran bulanan dan pilih tema tampilan.</p>

                <form @submit.prevent="saveStep2">
                    <div class="space-y-4">
                        <div>
                            <label for="monthly_budget" class="block text-sm font-medium text-[var(--ink)] mb-1">Anggaran Bulanan <span class="text-[var(--orange)]">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--muted)]">Rp</span>
                                <input 
                                    type="number" 
                                    id="monthly_budget" 
                                    x-model.number="form.monthly_budget"
                                    min="0"
                                    step="10000"
                                    class="w-full pl-10 pr-4 py-3 border border-[var(--border)] rounded-lg bg-[var(--paper)] text-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--ink)] focus:border-transparent transition"
                                    placeholder="0"
                                    required
                                    autocomplete="off"
                                >
                            </div>
                            <p class="text-xs text-[var(--muted)] mt-1">Contoh: 5000000 untuk Rp 5.000.000</p>
                            <p x-show="errors.monthly_budget" class="text-sm text-[var(--orange)] mt-1" x-text="errors.monthly_budget"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[var(--ink)] mb-1">Tema Tampilan</label>
                            <div class="grid grid-cols-3 gap-3">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="theme_preference" value="light" x-model="form.theme_preference" class="sr-only peer">
                                    <div class="p-4 border-2 rounded-lg text-center transition 
                                        peer-checked:border-[var(--ink)] peer-checked:bg-[var(--ink)]/5 
                                        border-[var(--border)] hover:border-[var(--muted)]">
                                        <div class="w-full h-16 bg-white border border-[var(--border)] rounded mb-2 flex items-center justify-center">
                                            <span class="text-3xl">☀️</span>
                                        </div>
                                        <p class="text-sm font-medium text-[var(--ink)]">Terang</p>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="theme_preference" value="dark" x-model="form.theme_preference" class="sr-only peer">
                                    <div class="p-4 border-2 rounded-lg text-center transition 
                                        peer-checked:border-[var(--ink)] peer-checked:bg-[var(--ink)]/5 
                                        border-[var(--border)] hover:border-[var(--muted)]">
                                        <div class="w-full h-16 bg-gray-900 border border-gray-700 rounded mb-2 flex items-center justify-center">
                                            <span class="text-3xl">🌙</span>
                                        </div>
                                        <p class="text-sm font-medium text-[var(--ink)]">Gelap</p>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="theme_preference" value="system" x-model="form.theme_preference" class="sr-only peer">
                                    <div class="p-4 border-2 rounded-lg text-center transition 
                                        peer-checked:border-[var(--ink)] peer-checked:bg-[var(--ink)]/5 
                                        border-[var(--border)] hover:border-[var(--muted)]">
                                        <div class="w-full h-16 bg-gradient-to-r from-white to-gray-900 border border-[var(--border)] rounded mb-2 flex items-center justify-center">
                                            <span class="text-3xl">💻</span>
                                        </div>
                                        <p class="text-sm font-medium text-[var(--ink)]">Sistem</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex gap-3">
                        <button 
                            type="button" 
                            @click="prevStep"
                            class="flex-1 px-4 py-3 border border-[var(--border)] rounded-lg text-[var(--ink)] hover:bg-[var(--muted)]/20 transition"
                        >
                            Kembali
                        </button>
                        <button 
                            type="submit" 
                            :disabled="saving"
                            class="flex-1 px-4 py-3 bg-[var(--ink)] text-[var(--paper)] rounded-lg hover:opacity-90 transition disabled:opacity-50 font-medium"
                        >
                            <span x-show="!saving">Lanjutkan</span>
                            <span x-show="saving" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 3: First Category + Initial Balance -->
            <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 translate-y-2">
                <h2 class="text-xl font-bold text-[var(--ink)] mb-2">Kategori & Saldo Awal</h2>
                <p class="text-[var(--muted)] mb-6">Buat kategori pertama dan (opsional) masukkan saldo awal.</p>

                <form @submit.prevent="saveStep3">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-[var(--ink)] mb-1">Kategori Pertama (Opsional)</label>
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <input 
                                        type="text" 
                                        x-model="form.category_name"
                                        class="w-full px-4 py-3 border border-[var(--border)] rounded-lg bg-[var(--paper)] text-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--ink)] focus:border-transparent transition"
                                        placeholder="Nama kategori"
                                        autocomplete="off"
                                    >
                                </div>
                                <div>
                                    <select x-model="form.category_type" class="w-full px-4 py-3 border border-[var(--border)] rounded-lg bg-[var(--paper)] text-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--ink)] focus:border-transparent transition">
                                        <option value="expense">Pengeluaran</option>
                                        <option value="income">Pemasukan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <input 
                                        type="text" 
                                        x-model="form.category_icon"
                                        maxlength="2"
                                        class="w-full px-4 py-3 border border-[var(--border)] rounded-lg bg-[var(--paper)] text-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--ink)] focus:border-transparent transition text-center text-2xl"
                                        placeholder="📄"
                                        autocomplete="off"
                                    >
                                    <p class="text-xs text-[var(--muted)] mt-1 text-center">Emoji (maks 2 karakter)</p>
                                </div>
                                <div>
                                    <input 
                                        type="color" 
                                        x-model="form.category_color"
                                        class="w-full h-12 border border-[var(--border)] rounded-lg cursor-pointer p-1"
                                        title="Warna kategori"
                                    >
                                    <p class="text-xs text-[var(--muted)] mt-1 text-center">Warna kategori</p>
                                </div>
                            </div>
                        </div>

                        <hr class="border-[var(--border)] my-4">

                        <div>
                            <label for="initial_balance" class="block text-sm font-medium text-[var(--ink)] mb-1">Saldo Awal (Opsional)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--muted)]">Rp</span>
                                <input 
                                    type="number" 
                                    id="initial_balance" 
                                    x-model.number="form.initial_balance"
                                    min="0"
                                    step="10000"
                                    class="w-full pl-10 pr-4 py-3 border border-[var(--border)] rounded-lg bg-[var(--paper)] text-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--ink)] focus:border-transparent transition"
                                    placeholder="0"
                                    autocomplete="off"
                                >
                            </div>
                            <p class="text-xs text-[var(--muted)] mt-1">Jika kamu sudah punya uang di dompet, masukkan di sini.</p>
                        </div>
                    </div>

                    <div class="mt-8 flex gap-3">
                        <button 
                            type="button" 
                            @click="prevStep"
                            class="flex-1 px-4 py-3 border border-[var(--border)] rounded-lg text-[var(--ink)] hover:bg-[var(--muted)]/20 transition"
                        >
                            Kembali
                        </button>
                        <button 
                            type="submit" 
                            :disabled="saving"
                            class="flex-1 px-4 py-3 bg-[var(--orange)] text-[var(--ink)] rounded-lg hover:opacity-90 transition disabled:opacity-50 font-medium"
                        >
                            <span x-show="!saving">Selesai & Mulai</span>
                            <span x-show="saving" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Menyelesaikan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- Completion State -->
        <div x-show="completed" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="text-center py-12">
            <div class="w-20 h-20 mx-auto mb-4 bg-[var(--ink)]/10 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-[var(--ink)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-[var(--ink)] mb-2">Selamat Datang di Zaku!</h2>
            <p class="text-[var(--muted)] mb-6">Onboarding selesai. Kami siap membantu mengelola keuanganmu.</p>
            <button 
                @click="goToDashboard"
                class="px-8 py-3 bg-[var(--ink)] text-[var(--paper)] rounded-lg hover:opacity-90 transition font-medium"
            >
                Mulai Menggunakan Zaku
            </button>
        </div>
    </main>
</div>
@endsection