// resources/js/alpine-components/onboarding.js
// Alpine components: onboardingPage

export function registerOnboarding(Alpine) {

    Alpine.data('onboardingPage', () => ({
        currentStep: 1,
        saving: false,
        completed: false,
        steps: [
            { id: 1, title: 'Profil' },
            { id: 2, title: 'Anggaran' },
            { id: 3, title: 'Kategori' },
        ],
        form: {
            full_name: '',
            phone_number: '',
            monthly_budget: null,
            theme_preference: 'light',
            category_name: '',
            category_type: 'expense',
            category_icon: '📄',
            category_color: '#6b7280',
            initial_balance: null,
        },
        errors: {},
        formatNumber(n) { if (!n) return '0'; return Number(n).toLocaleString('id-ID'); },
        async init() {
            try {
                const res = await window.apiClient.get('/v1/onboarding/status');
                const data = res.data.data;
                if (data.completed) {
                    this.completed = true;
                    this.currentStep = 4;
                    return;
                }
                this.currentStep = Math.min((data.step || 0) + 1, 3);
            } catch (e) {
                // If API fails, start at step 1
                this.currentStep = 1;
            }
        },
        prevStep() {
            if (this.currentStep > 1) this.currentStep--;
        },
        async saveStep1() {
            this.errors = {};
            if (!this.form.full_name) {
                this.errors.full_name = 'Nama lengkap wajib diisi';
                return;
            }
            this.saving = true;
            try {
                await window.apiClient.post('/v1/onboarding/step1', {
                    full_name: this.form.full_name.trim(),
                    phone_number: this.form.phone_number || null,
                });
                this.currentStep = 2;
                window.utils.showToast('success', 'Profil tersimpan');
            } catch (e) {
                window.utils.showToast('error', window.utils.parseApiError(e, 'Gagal menyimpan profil'));
            } finally {
                this.saving = false;
            }
        },
        async saveStep2() {
            this.errors = {};
            if (!this.form.monthly_budget && this.form.monthly_budget !== 0) {
                this.errors.monthly_budget = 'Anggaran bulanan wajib diisi';
                return;
            }
            this.saving = true;
            try {
                await window.apiClient.post('/v1/onboarding/step2', {
                    monthly_budget: this.form.monthly_budget,
                    theme_preference: this.form.theme_preference || 'light',
                });
                this.currentStep = 3;
                window.utils.showToast('success', 'Anggaran tersimpan');
            } catch (e) {
                window.utils.showToast('error', window.utils.parseApiError(e, 'Gagal menyimpan anggaran'));
            } finally {
                this.saving = false;
            }
        },
        async saveStep3() {
            this.saving = true;
            try {
                const payload = {
                    category_name: this.form.category_name || null,
                    category_type: this.form.category_type,
                    category_icon: this.form.category_icon || '📄',
                    category_color: this.form.category_color || '#6b7280',
                    initial_balance: this.form.initial_balance || 0,
                };
                await window.apiClient.post('/v1/onboarding/step3', payload);
                this.completed = true;
                this.currentStep = 4;
                window.utils.showToast('success', 'Onboarding selesai!');
            } catch (e) {
                window.utils.showToast('error', window.utils.parseApiError(e, 'Gagal menyelesaikan onboarding'));
            } finally {
                this.saving = false;
            }
        },
        goToDashboard() {
            window.location.href = '/dashboard';
        },
    }));

}
