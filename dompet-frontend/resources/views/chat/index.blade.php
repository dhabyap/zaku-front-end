@extends('layouts.app')

@section('content')
<div x-data="chatPage()" style="display:flex;flex-direction:column;height:100%;">
    <div class="chat-top">
        <div class="chat-ai-dot">AI</div>
        <div class="chat-ai-info">
            <div class="chat-ai-name">DOMPET AI</div>
            <div class="chat-ai-online"><div class="online-dot"></div>Online · siap mencatat</div>
        </div>
        <button class="chat-clear-btn" @click="clearChat()">HAPUS</button>
    </div>

    <div class="chat-msgs" id="chat-msgs" x-ref="msgs">
        <div class="msg ai">
            <div class="msg-bub">
                Halo, <strong x-text="user?.name?.split(' ')[0] || 'Teman'"></strong>! 👋 Ceritain aja transaksimu ke saya.<br><br>
                Misalnya:<br>
                <em>"Tadi beli makan siang 35rb"</em><br>
                <em>"Gajian bulan ini 5 juta"</em><br>
                <em>"Bayar Grab ke kantor 28 ribu"</em>
                <div class="chips">
                    <div class="chip" @click="sendQuick('Beli makan siang 35rb')">🍜 Makan 35rb</div>
                    <div class="chip" @click="sendQuick('Bayar Grab 28 ribu')">🚗 Grab 28rb</div>
                    <div class="chip" @click="sendQuick('Terima gaji 7.5 juta')">💰 Gaji</div>
                </div>
            </div>
            <div class="msg-time">09:00</div>
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
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
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
            updateCharCount() {
                this.charCount = this.message.length;
            },
            handleKey(event) {
                if (event.key === 'Enter' && !event.shiftKey) {
                    event.preventDefault();
                    this.sendMsg();
                }
            },
            async sendMsg() {
                const val = this.message.trim();
                if (!val || this.loading) return;
                this.message = '';
                this.charCount = 0;

                const msgs = this.$refs.msgs;
                const now = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

                const u = document.createElement('div');
                u.className = 'msg usr';
                u.innerHTML = '<div class="msg-bub">' + this.escapeHtml(val) + '</div><div class="msg-time">' + now + '</div>';
                msgs.appendChild(u);

                const t = document.createElement('div');
                t.className = 'msg ai';
                t.id = 'typing';
                t.innerHTML = '<div class="msg-bub"><div class="dots"><span></span><span></span><span></span></div></div>';
                msgs.appendChild(t);
                msgs.scrollTop = msgs.scrollHeight;

                this.loading = true;
                try {
                    const res = await window.apiClient.post('/ai/chat', { message: val });
                    const data = res.data.data || res.data;
                    t.remove();

                    let bubbleHtml = this.escapeHtml(data.response);
                    if (data.amount && data.description) {
                        const sign = data.type === 'income' ? 'inc' : 'exp';
                        bubbleHtml += '<div class="confirm-card">' +
                            '<div class="confirm-row"><span class="confirm-key">DESKRIPSI</span><span class="confirm-val">' + this.escapeHtml(data.description) + '</span></div>' +
                            '<div class="confirm-row"><span class="confirm-key">JUMLAH</span><span class="confirm-val ' + sign + '">' + this.escapeHtml(data.amount_formatted) + '</span></div>';
                        if (data.category) {
                            bubbleHtml += '<div class="confirm-row"><span class="confirm-key">KATEGORI</span><span class="confirm-val">' + this.escapeHtml(data.category) + '</span></div>';
                        }
                        bubbleHtml += '<div class="confirm-row"><span class="confirm-key">TIPE</span><span class="confirm-val ' + sign + '">' + (data.type === 'income' ? '↑ PEMASUKAN' : '↓ PENGELUARAN') + '</span></div>';
                        bubbleHtml += '</div>';
                    }

                    const a = document.createElement('div');
                    a.className = 'msg ai';
                    a.innerHTML = '<div class="msg-bub">' + bubbleHtml + '</div><div class="msg-time">' + now + '</div>';
                    msgs.appendChild(a);
                } catch (e) {
                    t.remove();
                    const a = document.createElement('div');
                    a.className = 'msg ai';
                    a.innerHTML = '<div class="msg-bub">Maaf, lagi ada gangguan. Coba lagi ya! 😅</div><div class="msg-time">' + now + '</div>';
                    msgs.appendChild(a);
                } finally {
                    this.loading = false;
                    msgs.scrollTop = msgs.scrollHeight;
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
                this.$refs.msgs.innerHTML = '<div class="msg ai"><div class="msg-bub">Chat dibersihkan. Ada transaksi yang mau dicatat? 😊</div><div class="msg-time">Sekarang</div></div>';
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
