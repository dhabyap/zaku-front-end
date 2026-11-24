@extends('layouts.app')

@section('content')
<div x-data="chatPage()" style="display:flex;flex-direction:column;height:100%;">
    <div class="chat-top">
        <div class="chat-ai-dot">AI</div>
        <div class="chat-ai-info">
            <div class="chat-ai-name">ZAKU AI</div>
            <div class="chat-ai-online"><div class="online-dot"></div>Online · siap mencatat</div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            <a href="/manual-input" style="background:var(--punch-2);border:2px solid var(--punch-2);color:var(--ink);font-family:var(--font-mono);font-size:9px;font-weight:500;padding:6px 10px;text-decoration:none;display:flex;align-items:center;gap:4px;">
                <span>+</span> INPUT MANUAL
            </a>
            <button class="chat-clear-btn" @click="clearChat()">HAPUS</button>
        </div>
    </div>

    <div class="chat-msgs" id="chat-msgs" x-ref="msgs">
        <template x-for="(msg, idx) in messages" :key="idx">
            <div :class="'msg ' + msg.role">
                <div class="msg-bub">
                    <template x-if="msg.html"><div x-html="msg.content"></div></template>
                    <template x-if="!msg.html"><div x-text="msg.content"></div></template>
                </div>
                <div class="msg-time" x-text="msg.time"></div>
            </div>
        </template>
        <div class="msg ai" x-show="loading" style="display: none;" id="typing">
            <div class="msg-bub" style="display:flex;align-items:center;gap:8px;">
                <div class="dots"><span></span><span></span><span></span></div>
                <span style="font-size:11px;color:rgba(17,16,16,.5);font-family:var(--font-mono);">ZAKU sedang memproses...</span>
            </div>
        </div>
    </div>

    <div class="chat-input-area">
        <div class="chat-input-toolbar">
            <div class="chat-input-label">// TULIS TRANSAKSIMU</div>
            <div class="chat-char-count" x-text="charCount + ' / 300'" :class="charCount > 280 ? 'warn' : ''">0 / 300</div>
        </div>
        <div class="chat-input-row">
            <textarea class="chat-input" id="chat-inp" x-ref="inp"
                placeholder="Ceritain transaksimu di sini...&#10;Misal: &quot;Beli makan siang 35rb&quot;"
                maxlength="300"
                rows="2"
                x-model="message"
                @input="updateCharCount()"
                @keydown="handleKey($event)"
            ></textarea>
            <button class="send-btn" @click="sendMsg()" :disabled="!message.trim() || loading">
                <template x-if="!loading">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                </template>
                <template x-if="loading">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="animate-spin">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="32" stroke-linecap="round"></circle>
                    </svg>
                </template>
            </button>
        </div>
    </div>
</div>

<script>
    function chatPage() {
        return {
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
                    localStorage.setItem(this.STORAGE_KEY, JSON.stringify(this.messages));
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

                // Push user message
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
        }
    }
</script>
@endsection
