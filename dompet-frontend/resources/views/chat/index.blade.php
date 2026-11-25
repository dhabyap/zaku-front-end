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
@endsection
