@extends('layouts.app')

@section('content')
<div x-data="chatPage" style="display:flex;flex-direction:column;height:100%;">
    <div class="chat-top">
        <div class="chat-ai-dot">AI</div>
        <div class="chat-ai-info">
            <div class="chat-ai-name">ZAKU AI</div>
            <div class="chat-ai-online"><div class="online-dot"></div>Online · siap mencatat</div>
        </div>
        <button class="chat-clear-btn" @click="clearChat()" aria-label="Hapus riwayat chat">HAPUS</button>
    </div>

    <div class="chat-msgs" x-ref="msgs">
        <template x-for="(msg, i) in messages" :key="i">
            <div class="msg" :class="msg.role === 'usr' ? 'usr' : 'ai'">
                <template x-if="msg.html">
                    <div class="msg-bub" x-html="msg.content"></div>
                </template>
                <template x-if="!msg.html">
                    <div class="msg-bub" x-text="msg.content"></div>
                </template>
                <div class="msg-time" x-text="msg.time"></div>
            </div>
        </template>
        <template x-if="typing">
            <div class="msg ai">
                <div class="msg-bub"><div class="dots"><span></span><span></span><span></span></div></div>
            </div>
        </template>
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
            <button class="send-btn" @click="sendMsg()" :disabled="!message.trim() || loading" aria-label="Kirim pesan">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
            </button>
        </div>
    </div>
</div>
@endsection
