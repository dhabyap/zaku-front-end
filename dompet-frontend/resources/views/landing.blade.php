<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ZAKU — Catat Duit, Gak Ribet</title>
<link rel="icon" type="image/svg+xml" href="{{ asset('zaku-favicon.svg') }}">
<meta name="description" content="ZAKU — Catat keuanganmu dengan chat. Tanpa form ribet. AI langsung paham dan analisis pengeluaran & pemasukan kamu.">
<meta name="keywords" content="catat keuangan, finance tracker, AI finance, pengeluaran, pemasukan, aplikasi keuangan, zaku">
<meta name="author" content="ZAKU">
<meta name="robots" content="index, follow">

<!-- Open Graph -->
<meta property="og:title" content="ZAKU — Catat Duit, Gak Ribet">
<meta property="og:description" content="Catat keuanganmu dengan chat. Tanpa form ribet. AI langsung paham dan analisis.">
<meta property="og:type" content="website">
<meta property="og:url" content="https://zaku.abysoft.my.id">
<meta property="og:image" content="https://zaku.abysoft.my.id/og-image.png">
<meta property="og:site_name" content="ZAKU">
<meta property="og:locale" content="id_ID">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="ZAKU — Catat Duit, Gak Ribet">
<meta name="twitter:description" content="Catat keuanganmu dengan chat. Tanpa form ribet. AI langsung paham dan analisis.">
<meta name="twitter:image" content="https://zaku.abysoft.my.id/og-image.png">

<!-- Canonical -->
<link rel="canonical" href="https://zaku.abysoft.my.id">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Mono:wght@400;500&family=Fraunces:ital,opsz,wght@1,9..144,300;1,9..144,700&display=swap" rel="stylesheet">

<!-- Schema.org JSON-LD for SEO -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "SoftwareApplication",
  "name": "ZAKU",
  "applicationCategory": "FinanceApplication",
  "operatingSystem": "Web",
  "url": "https://zaku.abysoft.my.id",
  "description": "ZAKU — Catat keuanganmu dengan chat. Tanpa form ribet. AI langsung paham dan analisis pengeluaran & pemasukan kamu.",
  "offers": {"@type": "Offer", "price": "0", "priceCurrency": "IDR"},
  "inLanguage": "id-ID"
}
</script>

<link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>

<!-- NAV -->
<nav class="nav">
  <a href="{{ url('/') }}" class="nav-logo">
    <div class="nav-logo-box">Z</div>
    <div class="nav-logo-text">ZAKU</div>
  </a>
  <ul class="nav-links">
    <li><a href="#how">CARA KERJA</a></li>
    <li><a href="#features">FITUR</a></li>
    <li><a href="#pricing">HARGA</a></li>
  </ul>
  <div class="nav-right">
    <a href="{{ url('/login') }}" class="nav-login">MASUK</a>
    <a href="{{ url('/register') }}" class="nav-cta">COBA GRATIS →</a>
  </div>
</nav>

<!-- ================================================================
     HERO
============================================================== -->
<section class="hero" id="top">
  <div class="hero-bg-text">ZAKU</div>

  <div class="hero-inner">
    <div class="hero-left reveal">
      <div class="hero-eyebrow">AI FINANCE TRACKER · 2026</div>
      <h1 class="hero-h1">
        Catat duit,
        <span class="hero-h1-italic">ngobrol aja.</span>
      </h1>
      <p class="hero-sub">
        Ketik kayak chat biasa — ZAKU langsung <strong>paham, catat, dan analisis</strong> keuanganmu secara real-time. Gak ada form manual. Gak ada ribet.
      </p>
      <div class="hero-btns">
        <a href="{{ url('/register') }}" class="btn-primary">MULAI GRATIS SEKARANG →</a>
        <a href="#how" class="btn-ghost">LIHAT CARA KERJANYA ↓</a>
      </div>
    </div>

    <div class="hero-right">
      <div class="phone-wrap">
        <div class="phone-glow"></div>
        <div class="phone-frame">
          <div class="phone-notch"><div class="phone-notch-pill"></div></div>
          <div class="phone-screen">
            <div class="mini-header">
              <div>
                <div class="mini-greet">SELAMAT PAGI ☀️</div>
                <div class="mini-name">pengguna</div>
              </div>
              <div class="mini-av">P</div>
            </div>
            <div class="mini-balance">
              <div class="mini-bal-label">SALDO BULAN INI</div>
              <div class="mini-bal-amt">Rp 0</div>
              <div class="mini-bal-row">
                <div class="mini-bal-stat" style="margin-right:2px">
                  <div class="mini-bal-stat-l">▲ PEMASUKAN</div>
                  <div class="mini-bal-stat-v">Rp 0</div>
                </div>
                <div class="mini-bal-stat">
                  <div class="mini-bal-stat-l">▼ PENGELUARAN</div>
                  <div class="mini-bal-stat-v">Rp 0</div>
                </div>
              </div>
            </div>
            <div class="mini-tx-label">TRANSAKSI TERAKHIR</div>
            <div class="mini-tx exp">
              <div class="mini-tx-ico">🍜</div>
              <div class="mini-tx-d">
                <div class="mini-tx-n">Makan siang</div>
                <div class="mini-tx-c">MAKANAN</div>
              </div>
              <div class="mini-tx-a">-Rp 25.000</div>
            </div>
            <div class="mini-tx inc">
              <div class="mini-tx-ico">💰</div>
              <div class="mini-tx-d">
                <div class="mini-tx-n">Gaji bulanan</div>
                <div class="mini-tx-c">PEMASUKAN</div>
              </div>
              <div class="mini-tx-a">+Rp 0</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="hero-stats">
    <div class="hero-stat reveal">
      <div class="hs-num" id="stat-users">—</div>
      <div class="hs-label">PENGGUNA AKTIF</div>
    </div>
    <div class="hero-stat reveal">
      <div class="hs-num" id="stat-transactions">—</div>
      <div class="hs-label">TRANSAKSI TERCATAT</div>
    </div>
    <div class="hero-stat reveal">
      <div class="hs-num">98%</div>
      <div class="hs-label">AKURASI AI PARSING</div>
    </div>
  </div>
</section>

<!-- MARQUEE -->
<div class="marquee-wrap">
  <div class="marquee-track">
    <div class="m-item">CATAT TANPA RIBET</div>
    <div class="m-item">AI YANG NGERTI KAMU</div>
    <div class="m-item">INSIGHT REAL-TIME</div>
    <div class="m-item">BUDGET ALERT OTOMATIS</div>
    <div class="m-item">LAPORAN BULANAN</div>
    <div class="m-item">GANTI KEBIASAAN BURUK</div>
    <div class="m-item">CATAT TANPA RIBET</div>
    <div class="m-item">AI YANG NGERTI KAMU</div>
    <div class="m-item">INSIGHT REAL-TIME</div>
    <div class="m-item">BUDGET ALERT OTOMATIS</div>
    <div class="m-item">LAPORAN BULANAN</div>
    <div class="m-item">GANTI KEBIASAAN BURUK</div>
  </div>
</div>

<!-- ================================================================
     PROBLEM
============================================================== -->
<div class="problem-bg">
  <div class="section">
    <div class="problem-grid">
      <div class="reveal">
        <div class="eyebrow">THE PROBLEM</div>
        <h2 class="prob-heading">Kenapa kamu selalu <em>gagal</em> catat keuangan?</h2>
        <p class="prob-desc">Bukan karena kamu males. Tapi karena tool yang ada sekarang terlalu ribet untuk dipakai setiap hari.</p>
      </div>
      <div class="prob-list">
        <div class="prob-item reveal">
          <div class="prob-icon">😩</div>
          <div>
            <div class="prob-title">Form manual itu menyiksa</div>
            <div class="prob-text-desc">Buka app → pilih kategori → ketik nominal → pilih tanggal → simpan. Siapa yang mau lakuin itu tiap habis jajan 15 ribu?</div>
          </div>
        </div>
        <div class="prob-item reveal">
          <div class="prob-icon">📊</div>
          <div>
            <div class="prob-title">Spreadsheet keren, tapi gak kepake</div>
            <div class="prob-text-desc">Semangat awal bulan, minggu kedua sudah lupa. Akhir bulan bengong duit kemana perginya.</div>
          </div>
        </div>
        <div class="prob-item reveal">
          <div class="prob-icon">🤖</div>
          <div>
            <div class="prob-title">Bot Telegram cuma catat, tanpa insight</div>
            <div class="prob-text-desc">Data masuk, tapi kamu tetap buta soal kondisi keuanganmu. Input doang, analisis nol besar.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ================================================================
     HOW IT WORKS
============================================================== -->
<div class="how-bg section-full" id="how">
  <div class="section-inner">
    <div class="section" style="padding-bottom:0">
      <div class="eyebrow" style="color:rgba(255,255,255,.2)">HOW IT WORKS</div>
      <h2 class="how-heading">Semudah kirim pesan.</h2>
      <p class="how-sub">Tiga langkah. Gak perlu tutorial. Langsung jalan.</p>
    </div>
    <div style="padding: 0 var(--pad) clamp(80px,10vw,140px);">
      <div class="steps-grid reveal">
        <div class="step-card">
          <div class="step-num">1</div>
          <div class="step-tag">INPUT</div>
          <div class="step-title">Ketik apa yang terjadi</div>
          <div class="step-desc">Pakai bahasa kamu sendiri, sesantai chat WA. Gak perlu format khusus, gak perlu singkatan aneh.</div>
          <div class="step-chat">
            <div class="sc-bubble usr">"Tadi beli makan siang 35rb di warteg"</div>
            <div class="sc-bubble usr">"Gajian bulan ini 6.5 juta udah masuk"</div>
          </div>
        </div>
        <div class="step-card">
          <div class="step-num">2</div>
          <div class="step-tag">AI PROCESS</div>
          <div class="step-title">ZAKU ngerti & langsung catat</div>
          <div class="step-desc">AI parsing nominal, kategori, dan tipe transaksi — semua otomatis. Plus langsung kasih feedback relevan.</div>
          <div class="step-chat">
            <div class="sc-bubble ai">✓ Dicatat! Makan siang −Rp 35.000 · MAKANAN<br>Budget makananmu udah 78% bulan ini 👀</div>
          </div>
        </div>
        <div class="step-card">
          <div class="step-num">3</div>
          <div class="step-tag">DASHBOARD</div>
          <div class="step-title">Dashboard update real-time</div>
          <div class="step-desc">Saldo, grafik, kategori, dan insight keuangan — semuanya berubah otomatis. Kondisi keuangan kamu selalu up-to-date tanpa effort.</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ================================================================
     VS COMPARISON
============================================================== -->
<div class="vs-bg section-full">
  <div class="section-inner">
    <div class="section">
      <div class="vs-grid">
        <div class="reveal">
          <div class="eyebrow" style="color:rgba(17,16,16,.35)">COMPARISON</div>
          <h2 class="vs-heading">Beda banget sama bot chat biasa.</h2>
          <p class="vs-sub">Bot cuma bisa dengar dan catat. ZAKU bisa <strong>dengar, analisis, memperingatkan, dan kasih saran</strong> berdasarkan kondisi keuanganmu saat ini.</p>
          <div class="sc-bubble ai" style="border-color:var(--ink);background:rgba(17,16,16,.08);color:rgba(17,16,16,.6);font-size:11px;max-width:100%">
            "Eh, kamu udah hampir over budget buat transportasi bulan ini. Mau saya ingatkan tiap kali input di kategori ini?"
          </div>
          <div style="margin-top:8px;font-family:'DM Mono',monospace;font-size:9px;letter-spacing:1.5px;color:rgba(17,16,16,.35)">ZAKU AI · INSIGHT PROAKTIF</div>
        </div>
        <div class="vs-table reveal">
          <div class="vs-head">
            <div class="vs-hcell">FITUR</div>
            <div class="vs-hcell">ZAKU</div>
            <div class="vs-hcell">BOT</div>
          </div>
          <div class="vs-row hl">
            <div class="vs-cell">Catat transaksi via chat</div>
            <div class="vs-cell chk">✓</div>
            <div class="vs-cell chk">✓</div>
          </div>
          <div class="vs-row">
            <div class="vs-cell">Dashboard visual real-time</div>
            <div class="vs-cell chk">✓</div>
            <div class="vs-cell crs">✗</div>
          </div>
          <div class="vs-row">
            <div class="vs-cell">Insight & analisis otomatis</div>
            <div class="vs-cell chk">✓</div>
            <div class="vs-cell crs">✗</div>
          </div>
          <div class="vs-row">
            <div class="vs-cell">Budget alert cerdas</div>
            <div class="vs-cell chk">✓</div>
            <div class="vs-cell crs">✗</div>
          </div>
          <div class="vs-row">
            <div class="vs-cell">Grafik per kategori</div>
            <div class="vs-cell chk">✓</div>
            <div class="vs-cell crs">✗</div>
          </div>
          <div class="vs-row">
            <div class="vs-cell">Laporan PDF bulanan</div>
            <div class="vs-cell chk">✓</div>
            <div class="vs-cell crs">✗</div>
          </div>
          <div class="vs-row">
            <div class="vs-cell">AI tahu konteks keuanganmu</div>
            <div class="vs-cell chk">✓</div>
            <div class="vs-cell crs">✗</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ================================================================
     FEATURES
============================================================== -->
<div class="feat-bg section-full" id="features">
  <div class="section-inner">
    <div class="section" style="padding-bottom:0">
      <div class="eyebrow">FEATURES</div>
      <h2 class="feat-heading">Lebih dari <em>sekedar catat.</em></h2>
      <p class="feat-sub">Semua yang kamu butuh untuk kontrol keuangan ada di satu tempat.</p>
    </div>
    <div style="padding: 0 var(--pad) clamp(80px,10vw,140px);">
      <div class="feat-grid reveal">
        <div class="feat-card c-orange">
          <div class="feat-bg-num">01</div>
          <div class="feat-tag">CORE FEATURE</div>
          <span class="feat-ico">💬</span>
          <div class="feat-title">AI Chat Input</div>
          <div class="feat-desc">Ketik pakai bahasa sehari-hari. "Habis makan 25rb" langsung tersimpan dengan kategori, nominal, dan tanggal yang tepat. Akurasi 98%.</div>
        </div>
        <div class="feat-card c-yellow">
          <div class="feat-bg-num">02</div>
          <div class="feat-tag">VISUALIZATION</div>
          <span class="feat-ico">📊</span>
          <div class="feat-title">Dashboard Real-time</div>
          <div class="feat-desc">Saldo, pengeluaran per kategori, dan tren keuangan — semua dalam satu layar yang langsung update setiap ada transaksi baru.</div>
        </div>
        <div class="feat-card c-green">
          <div class="feat-bg-num">03</div>
          <div class="feat-tag">INTELLIGENCE</div>
          <span class="feat-ico">🧠</span>
          <div class="feat-title">Insight Proaktif</div>
          <div class="feat-desc">ZAKU tahu kalau pengeluaran transportasimu naik 40% minggu ini — dan kasih tahu kamu sebelum kamu sadar sendiri.</div>
        </div>
        <div class="feat-card c-blue">
          <div class="feat-bg-num">04</div>
          <div class="feat-tag">ALERT SYSTEM</div>
          <span class="feat-ico">⚠️</span>
          <div class="feat-title">Budget Health Score</div>
          <div class="feat-desc">Skor risiko keuangan real-time. Tahu persis kapan harus rem pengeluaran sebelum dompet jebol di akhir bulan.</div>
        </div>
      </div>

      <div class="feat-grid-2">
        <div class="feat-card-2 reveal">
          <div class="feat-card-2-ico">📄</div>
          <div>
            <div class="feat-card-2-title">Laporan PDF Bulanan</div>
            <div class="feat-card-2-desc">Export laporan keuangan lengkap dalam format PDF yang rapi. Ada grafik, breakdown kategori, dan perbandingan bulan sebelumnya.</div>
          </div>
        </div>
        <div class="feat-card-2 reveal">
          <div class="feat-card-2-ico">🔄</div>
          <div>
            <div class="feat-card-2-title">Recurring Transaction</div>
            <div class="feat-card-2-desc">AI mendeteksi pola transaksi rutin dan otomatis sarankan reminder. Tagihan bulanan tidak akan pernah kelewat lagi.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ================================================================
     QUOTE
============================================================== -->
<!-- ================================================================
     SOCIAL PROOF / TESTIMONIALS
============================================================= -->
<div class="testimonial-bg section-full">
  <div class="section-inner">
    <div class="section">
      <div class="reveal">
        <div class="eyebrow">SOCIAL PROOF</div>
        <h2 class="testimonial-heading">Apa kata <em>pengguna ZAKU</em>?</h2>
      </div>
      <div class="testimonial-grid reveal">
        <div class="testimonial-card">
          <div class="testimonial-stars">★★★★★</div>
          <div class="testimonial-text">"Akhirnya bisa tau duit ku pergi kemana. Chat aja, ZAKU yang ngurus sisanya. Budget alert-nya bener-bener nyelamatkan akhir bulan!"</div>
          <div class="testimonial-author">
            <div class="author-avatar">R</div>
            <div>
              <div class="author-name">Rizki Pratama</div>
              <div class="author-meta">Freelancer • 6 bln pakai</div>
            </div>
          </div>
        </div>
        <div class="testimonial-card">
          <div class="testimonial-stars">★★★★★</div>
          <div class="testimonial-text">"Dulu pake spreadsheet, minggu ke-2 udah males. Sekarang cuma chat, udah auto categorize. Insight bulanan bikin sadar belanja pemborosan."</div>
          <div class="testimonial-author">
            <div class="author-avatar">S</div>
            <div>
              <div class="author-name">Siti Rahma</div>
              <div class="author-meta">Karyawan • 4 bln pakai</div>
            </div>
          </div>
        </div>
        <div class="testimonial-card">
          <div class="testimonial-stars">★★★★★</div>
          <div class="testimonial-text">"Fitur recurring transaction favorit. Tagihan listrik, internet, langganan — ZAKU yang ingetin. Gak pernah kelewat bayar lagi."</div>
          <div class="testimonial-author">
            <div class="author-avatar">A</div>
            <div>
              <div class="author-name">Andi Wijaya</div>
              <div class="author-meta">Startup Founder • 8 bln pakai</div>
            </div>
          </div>
        </div>
        <div class="testimonial-card">
          <div class="testimonial-stars">★★★★★</div>
          <div class="testimonial-text"><em>PDF export-nya</em> rapi banget buat arsip keuangan. Ada grafik, breakdown kategori, komparasi bulan lalu. Cocok buat evaluasi bulanan.</div>
          <div class="testimonial-author">
            <div class="author-avatar">D</div>
            <div>
              <div class="author-name">Dewi Lestari</div>
              <div class="author-meta">Account Manager • 3 bln pakai</div>
            </div>
          </div>
        </div>
      </div>
      <div class="trust-stats reveal">
        <div class="trust-stat">
          <div class="trust-num">2,847</div>
          <div class="trust-label">Pengguna Aktif</div>
        </div>
        <div class="trust-stat">
          <div class="trust-num">142K+</div>
          <div class="trust-label">Transaksi Tercatat</div>
        </div>
        <div class="trust-stat">
          <div class="trust-num">98%</div>
          <div class="trust-label">Akurasi AI</div>
        </div>
        <div class="trust-stat">
          <div class="trust-num">4.9/5</div>
          <div class="trust-label">Rating App Store</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ================================================================
     PRICING
============================================================== -->
<div class="price-bg section-full" id="pricing">
  <div class="section-inner">
    <div class="section">
      <div class="reveal">
        <div class="eyebrow price-eyebrow">PRICING</div>
        <h2 class="price-heading">Pilih yang pas buat kamu.</h2>
        <p class="price-sub">Semua fitur inti gratis selamanya. Upgrade kalau butuh lebih.</p>
      </div>
      <div class="price-grid reveal">
        <div class="price-card">
          <div class="price-card-top">
            <div class="p-plan">GRATIS <span class="p-badge">POPULER</span></div>
            <div class="p-price">Rp 0<span>/bln</span></div>
            <p class="p-desc">Cukup buat kebanyakan orang.</p>
          </div>
          <div class="price-feats">
            <div class="p-feat"><span class="p-feat-dot"></span> AI Chat Input unlimited</div>
            <div class="p-feat"><span class="p-feat-dot"></span> Dashboard real-time</div>
            <div class="p-feat"><span class="p-feat-dot"></span> Insight & analisis otomatis</div>
            <div class="p-feat"><span class="p-feat-dot"></span> Budget health score</div>
            <div class="p-feat"><span class="p-feat-dot"></span> Kategori & tag custom</div>
            <div class="p-feat"><span class="p-feat-dot"></span> Export CSV</div>
            <div class="p-feat dim"><span class="p-feat-dot"></span> Laporan PDF bulanan</div>
            <div class="p-feat dim"><span class="p-feat-dot"></span> Recurring transaction</div>
            <div class="p-feat dim"><span class="p-feat-dot"></span> Prioritas support</div>
          </div>
          <div class="price-card-cta">
            <a href="{{ url('/register') }}" class="p-btn fill">MULAI GRATIS</a>
          </div>
        </div>
        <div class="price-card popular">
          <div class="price-card-top">
            <div class="p-plan">PRO</div>
            <div class="p-price">Rp 49.000<span>/bln</span></div>
            <p class="p-desc">Buat power user yang butuh kontrol penuh.</p>
          </div>
          <div class="price-feats">
            <div class="p-feat"><span class="p-feat-dot"></span> Semua fitur Gratis</div>
            <div class="p-feat"><span class="p-feat-dot"></span> Laporan PDF bulanan</div>
            <div class="p-feat"><span class="p-feat-dot"></span> Recurring transaction</div>
            <div class="p-feat"><span class="p-feat-dot"></span> Multi-device sync</div>
            <div class="p-feat"><span class="p-feat-dot"></span> Custom categories unlimited</div>
            <div class="p-feat"><span class="p-feat-dot"></span> Advanced insights</div>
            <div class="p-feat"><span class="p-feat-dot"></span> Prioritas email support</div>
            <div class="p-feat"><span class="p-feat-dot"></span> Data export lengkap</div>
            <div class="p-feat"><span class="p-feat-dot"></span> Early access fitur baru</div>
          </div>
          <div class="price-card-cta">
            <a href="{{ url('/register') }}" class="p-btn fill">UPGRADE KE PRO</a>
          </div>
        </div>
        <div class="price-card">
          <div class="price-card-top">
            <div class="p-plan">TIM</div>
            <div class="p-price">Rp 129.000<span>/bln</span></div>
            <p class="p-desc">Buat tim/keluarga yang kelola keuangan bareng.</p>
          </div>
          <div class="price-feats">
            <div class="p-feat"><span class="p-feat-dot"></span> Semua fitur Pro</div>
            <div class="p-feat"><span class="p-feat-dot"></span> Hingga 5 anggota</div>
            <div class="p-feat"><span class="p-feat-dot"></span> Shared budgets & wallets</div>
            <div class="p-feat"><span class="p-feat-dot"></span> Team insights</div>
            <div class="p-feat"><span class="p-feat-dot"></span> Role-based access</div>
            <div class="p-feat"><span class="p-feat-dot"></span> Audit log aktivitas</div>
            <div class="p-feat"><span class="p-feat-dot"></span> Dedicated support</div>
            <div class="p-feat"><span class="p-feat-dot"></span> SSO (coming soon)</div>
            <div class="p-feat"><span class="p-feat-dot"></span> Custom onboarding</div>
          </div>
          <div class="price-card-cta">
            <a href="{{ url('/register') }}" class="p-btn outline">HUBUNGKAN KAMI</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ================================================================
     FINAL CTA
============================================================== -->
<div class="cta-bg section-full">
  <div class="section-inner">
    <div class="section">
      <div class="cta-inner reveal">
        <div>
          <h2 class="cta-heading">Mulai catat duit <em>hari ini.</em></h2>
          <p class="cta-note">Gak perlu kartu kredit. Gak perlu setup lama. Cuma butuh 30 detik.</p>
        </div>
        <div class="cta-right">
          <a href="{{ url('/register') }}" class="btn-cta-big">DAFTAR GRATIS SEKARANG →</a>
          <a href="{{ url('/login') }}" class="btn-cta-login">SUDAH PUNYA AKUN? MASUK</a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ================================================================
     FAQ SECTION
============================================================== -->
<div class="faq-bg section-full">
  <div class="section-inner">
    <div class="section">
      <div class="reveal">
        <div class="eyebrow">FAQ</div>
        <h2 class="faq-heading">Pertanyaan yang <em>sering ditanya.</em></h2>
      </div>
      <div class="faq-grid reveal">
        <details class="faq-item">
          <summary class="faq-question">Gratisnya sampe kapan?<span class="faq-icon">▼</span></summary>
          <div class="faq-answer"><p>Selamanya. Plan Gratis gak ada batas waktu, gak ada trial, gak perlu kartu kredit. Kamu bisa pakai fitur inti (AI chat, dashboard, insight, budget score) tanpa bayar apa-apa.</p></div>
        </details>
        <details class="faq-item">
          <summary class="faq-question">Data keuangan aman gak?<span class="faq-icon">▼</span></summary>
          <div class="faq-answer"><p>Aman. Data dienkripsi di transit (HTTPS) dan at-rest. Kami gak pernah jual data ke pihak ketiga. Kamu bisa export & hapus data kapan aja dari pengaturan.</p></div>
        </details>
        <details class="faq-item">
          <summary class="faq-question">Bisa pakai di HP & laptop bareng?<span class="faq-icon">▼</span></summary>
          <div class="faq-answer"><p>Bisa. ZAKU web-based, jalan di browser manapun. Data sinkron real-time lewat cloud. Login di HP, laptop, tablet — semuanya sama.</p></div>
        </details>
        <details class="faq-item">
          <summary class="faq-question">AI-nya akurat beneran?<span class="faq-icon">▼</span></summary>
          <div class="faq-answer"><p>98% akurasi untuk parsing nominal, kategori, & tipe transaksi bahasa Indonesia sehari-hari. Kalau AI salah, kamu bisa edit manual — ZAKU belajar dari koreksimu.</p></div>
        </details>
        <details class="faq-item">
          <summary class="faq-question">Bedanya Gratis & Pro apa?<span class="faq-icon">▼</span></summary>
          <div class="faq-answer"><p>Gratis: fitur inti semua ada. Pro tambah: PDF export, recurring transaction, multi-device sync, advanced insights, prioritas support. Upgrade kapan aja, downgrade juga bebas.</p></div>
        </details>
        <details class="faq-item">
          <summary class="faq-question">Bisa import data dari app lain?<span class="faq-icon">▼</span></summary>
          <div class="faq-answer"><p>Saat ini support import CSV. Format standar: Tanggal, Deskripsi, Kategori, Tipe (income/expense), Jumlah. Fitur import Excel/Google Sheets coming soon.</p></div>
        </details>
        <details class="faq-item">
          <summary class="faq-question">Gak suka, bisa hapus akun?<span class="faq-icon">▼</span></summary>
          <div class="faq-answer"><p>Bisa. Di Settings → Hapus Akun. Semua data dihapus permanen dalam 24 jam. Gak ada jejak, gak ada retensi paksa.</p></div>
        </details>
      </div>
    </div>
  </div>
</div>

<!-- ================================================================
     FOOTER
============================================================== -->
<footer class="footer">
  <div class="foot-logo">
    <div class="foot-logo-box">Z</div>
    <div class="foot-logo-text">ZAKU</div>
  </div>
  <ul class="foot-links">
    <li><a href="#features">Fitur</a></li>
    <li><a href="#pricing">Harga</a></li>
    <li><a href="/changelogs">Changelog</a></li>
    <li><a href="/privacy">Kebijakan Privasi</a></li>
    <li><a href="/terms">Syarat & Ketentuan</a></li>
  </ul>
  <div class="foot-copy">© 2026 ZAKU. Dibangun dengan ❤️ di Indonesia.</div>
</footer>

<script>
// Reveal animation on scroll
const revealElements = document.querySelectorAll('.reveal');
const revealObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('in');
      revealObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

revealElements.forEach(el => revealObserver.observe(el));

// Fetch public stats for hero
async function fetchPublicStats() {
  try {
    const res = await fetch('/api/v1/stats/public');
    const data = await res.json();
    if (data.data) {
      document.getElementById('stat-users')?.textContent = data.data.users_count?.toLocaleString('id-ID') || '—';
      document.getElementById('stat-transactions')?.textContent = data.data.transactions_count?.toLocaleString('id-ID') || '—';
    }
  } catch (e) {
    console.warn('Stats load failed:', e);
  }
}
fetchPublicStats();
</script>
</body>
</html>