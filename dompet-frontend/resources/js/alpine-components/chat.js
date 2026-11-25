// resources/js/alpine-components/chat.js
// Alpine components: chatPage

export function registerChat(Alpine) {

    Alpine.data('chatPage', () => ({
        user: window.auth.getUser(),
        loading: false,
        message: '',
        charCount: 0,
        STORAGE_KEY: 'zaku_chat_history',
        messages: [],
        init() {
            try {
                const saved = localStorage.getItem(this.STORAGE_KEY);
                if (saved) {
                    const parsed = JSON.parse(saved);
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        this.messages = parsed;
                        this.scrollBottom();
                        return;
                    }
                }
            } catch { /* ignore */ }

            const userName = window.auth.getUser()?.name?.split(' ')[0] || 'Teman';
            this.messages.push({
                role: 'ai',
                html: true,
                content: 'Halo, <strong>' + userName + '</strong>! 👋 Saya ZAKU AI, asisten keuangan pribadimu.<br><br>Kamu bisa catat pengeluaran atau pemasukan langsung lewat chat ini. Contoh:<br><em>"Makan siang bakso 25rb"</em><br><em>"Dapat transfer freelance 500rb"</em>',
                time: this.now()
            });
            this.scrollBottom();
        },
        persist() {
            try {
                const maxMessages = 100;
                const toStore = this.messages.slice(-maxMessages);
                localStorage.setItem(this.STORAGE_KEY, JSON.stringify(toStore));
            } catch { /* quota exceeded */ }
        },
        updateCharCount() {
            this.charCount = this.message.length;
        },
        handleKey(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                this.sendMsg();
            }
        },
        now() {
            return new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        },
        scrollBottom() {
            this.$nextTick(() => {
                const msgs = this.$refs.msgs;
                if (msgs) msgs.scrollTop = msgs.scrollHeight;
            });
        },
        async sendMsg() {
            const val = this.message.trim();
            if (!val || this.loading) return;
            this.message = '';
            this.charCount = 0;

            this.messages.push({
                role: 'usr',
                html: false,
                content: val,
                time: this.now()
            });
            this.persist();

            this.loading = true;
            this.scrollBottom();

            try {
                const res = await window.apiClient.post('/v1/ai/chat', { message: val }, { timeout: 20000 });
                const data = res.data;
                let bubbleHtml = '';

                if (data.data) {
                    const inner = data.data;
                    const parsed = inner.parsed_data || inner;
                    if (inner.reply_message) {
                        bubbleHtml = this.escapeHtml(inner.reply_message);
                    } else if (inner.response) {
                        bubbleHtml = this.escapeHtml(inner.response);
                    }
                    if (parsed.amount && parsed.description) {
                        const sign = parsed.type === 'income' ? 'inc' : 'exp';
                        bubbleHtml += '<div class="confirm-card">' +
                            '<div class="confirm-row"><span class="confirm-key">DESKRIPSI</span><span class="confirm-val">' + this.escapeHtml(parsed.description) + '</span></div>' +
                            '<div class="confirm-row"><span class="confirm-key">JUMLAH</span><span class="confirm-val ' + sign + '">' + this.formatAmount(parsed.amount) + '</span></div>';
                        if (parsed.category) {
                            bubbleHtml += '<div class="confirm-row"><span class="confirm-key">KATEGORI</span><span class="confirm-val">' + this.escapeHtml(parsed.category) + '</span></div>';
                        }
                        bubbleHtml += '<div class="confirm-row"><span class="confirm-key">TIPE</span><span class="confirm-val ' + sign + '">' + (parsed.type === 'income' ? '↑ PEMASUKAN' : '↓ PENGELUARAN') + '</span></div>' +
                            '</div>';
                    } else if (parsed.message) {
                        bubbleHtml = this.escapeHtml(parsed.message);
                    }
                } else if (data.response) {
                    bubbleHtml = this.escapeHtml(data.response);
                } else if (data.message) {
                    bubbleHtml = this.escapeHtml(data.message);
                }

                if (!bubbleHtml) {
                    bubbleHtml = 'Transaksi berhasil diproses, tapi format respons tidak lengkap.';
                }

                this.messages.push({
                    role: 'ai',
                    html: true,
                    content: bubbleHtml,
                    time: this.now()
                });
            } catch (e) {
                console.error('AI chat error:', e);
                let errorMsg = '⚠️ Maaf, AI sedang lambat atau tidak dapat merespons. ';
                if (e.code === 'ECONNABORTED' || e.message?.includes('timeout')) {
                    errorMsg = '⏱️ Waktu tunggu habis (timeout). AI sedang sibuk. Silakan coba kirim ulang atau gunakan menu Input Manual.';
                } else if (e.response && e.response.status === 429) {
                    errorMsg = '⏳ Terlalu banyak permintaan dalam waktu singkat. Mohon tunggu beberapa detik lagi.';
                } else {
                    const parsed = window.utils.parseApiError(e, '');
                    if (parsed) errorMsg += parsed;
                    else errorMsg += 'Silakan coba lagi beberapa saat lagi atau input manual.';
                }
                this.messages.push({
                    role: 'ai',
                    html: false,
                    content: errorMsg,
                    time: this.now()
                });
            } finally {
                this.loading = false;
                this.persist();
                this.scrollBottom();
            }
        },
        sendQuick(text) {
            this.message = text;
            this.charCount = text.length;
            this.sendMsg();
        },
        async clearChat() {
            const ok = await window.utils.confirmDialog({
                title: 'Hapus Pesan?',
                message: 'Semua riwayat chat akan dibersihkan.',
                okLabel: 'YA, HAPUS',
                danger: false
            });
            if (!ok) return;
            localStorage.removeItem(this.STORAGE_KEY);
            this.messages = [];
            this.messages.push({
                role: 'ai',
                html: true,
                content: 'Chat dibersihkan. Ada transaksi yang mau dicatat? 😊',
                time: this.now()
            });
            this.persist();
        },
        formatAmount(n) {
            return 'Rp ' + Number(n).toLocaleString('id-ID');
        },
        escapeHtml(text) {
            const d = document.createElement('div');
            d.textContent = text;
            return d.innerHTML;
        }
    }));

}