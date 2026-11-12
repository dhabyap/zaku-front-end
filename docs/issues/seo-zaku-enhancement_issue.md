# 📄 ZAKU SEO + Performance Enhancement — Rank Math 78/100 + Lighthouse

## Konteks
Audit dua sumber untuk `https://zaku.abysoft.my.id/`:
1. **Rank Math SEO Analyzer** → skor **78/100** (Basic SEO, sudah lumayan)
2. **Lighthouse** → FCP 1.4s, LCP 1.9s, TBT 910ms, SpeedIndex 3.3s, total weight 147.9 KB (90% = font Google)

> **CATATAN PENTING:** Lighthouse kamu pakai **browser dengan ekstensi aktif** (terdapat chrome-extension DataHive, inpage.js, dll). Skrip ekstensi ini menyumbang **±744ms TBT** dan beberapa long task. **Semua angka Lighthouse di bawah masih terpengaruh ekstensi** — validasi ulang di incognito sebelum klaim improvement.

## Yang Sudah Bagus (jangan ubah)
- Meta description, OG tag, canonical tag, title & H1/H2
- CSS & JS sudah minified
- HTTPS aktif, 0 broken link, directory listing off, theme/plugin tidak terlihat
- Main thread work relatif kecil (4.2s) dan CLS 0.004 (sangat rendah)

## Yang Perlu Di-fix / Enhanse
| No | Kategori | Prioritas | Item |
|----|----------|-----------|------|
| 1 | SEO | P0 | Tambah **Schema.org JSON-LD** (`SoftwareApplication`) |
| 2 | SEO | P0 | Tambah **sitemap.xml** + update `robots.txt` |
| 3 | SEO | P0 | **301 redirect www → non-www** di `.htaccess` |
| 4 | SEO | P1 | Tambah 1–2 **internal link** relevan (footer CTA) |
| 5 | SEO | P2 | Tambah sinyal **freshness** (`Last-Modified` via `.htaccess`/Laravel) |
| 6 | Performance | P0 | Self-host / subset **webfont** (Fraunces 81KB, Syne, DM Mono) |
| 7 | Performance | P1 | **Defer / async** script non-kritis (jika ada yang render-blocking) |
| 8 | Performance | P2 | Tambah header **Cache-Control** untuk font & asset |

## File yang Perlu Diubah
| No | File | Aksi | Prioritas |
|----|------|------|-----------|
| 1 | `resources/views/...` (landing view utama) | Tambah JSON-LD di `<head>` | P0 |
| 2 | `public/.htaccess` | Redirect www + cache header | P0–P2 |
| 3 | `public/sitemap.xml` | **Buat baru** | P0 |
| 4 | `public/robots.txt` | Tambah `Sitemap:` | P0 |
| 5 | `public/...` (font lokal) | Self-host woff2 (optional) | P0 |

> **Langkah Investigasi:** Cek dulu file view landing mana yang sebenarnya dipanggil untuk `GET /` (biasanya `routes/web.php` → `welcome.blade.php` atau layout landing). Jangan asal ubah file lain.

## Langkah-Langkah Implementasi

### 1) Schema.org JSON-LD — landing view
Blok ini taruh **sebelum `</head>`** di view landing:
```html
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
```

### 2) Sitemap + Robots
`public/sitemap.xml` (buat baru):
```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>https://zaku.abysoft.my.id/</loc>
    <lastmod>2026-10-23</lastmod>
    <changefreq>weekly</changefreq>
    <priority>1.0</priority>
  </url>
</urlset>
```

`public/robots.txt` → tambah:
```
Sitemap: https://zaku.abysoft.my.id/sitemap.xml
```

### 3) .htaccess — www redirect + cache
Tambah setelah `RewriteEngine On`:
```apache
# www → non-www 301
RewriteCond %{HTTP_HOST} ^www\.zaku\.abysoft\.my\.id$ [NC]
RewriteRule ^(.*)$ https://zaku.abysoft.my.id/$1 [L,R=301]

# Cache font & asset statis
<IfModule mod_headers.c>
  <FilesMatch "\.(woff2|css|js|svg)$">
    Header set Cache-Control "public, max-age=604800"
  </FilesMatch>
</IfModule>
```

### 4) Internal Link (footer CTA)
Di footer landing, tambah:
```html
<nav class="footer-links">
  <a href="{{ route('login') }}">Masuk</a>
  <a href="{{ route('register') }}">Daftar Gratis</a>
</nav>
```

### 5) Webfont Self-Host (opsional, impact besar)
Fraunces italic 700 = **81.8 KB**, Syne = 34.6 KB, DM Mono = 8.7 KB. Jika ingin performa maksimal, host font lokal + subset woff2. Jika tidak, tetap pakai Google Fonts (lebih praktis, trade-off speed).

### 6) Defer/Async JS Non-Kritis
Kalau ada `defer`/`async` yang belum, terapkan untuk skrip yang tidak blocking render.

## Acceptance Criteria
- [ ] `curl -s https://zaku.abysoft.my.id/ | grep -A2 "application/ld+json"` → JSON-LD muncul
- [ ] `curl -sI https://www.zaku.abysoft.my.id/` → `301`
- [ ] `curl -s https://zaku.abysoft.my.id/sitemap.xml` → 200, valid
- [ ] `curl -s https://zaku.abysoft.my.id/robots.txt` → ada `Sitemap:`
- [ ] Internal link bertambah (>=4)
- [ ] **Uji Lighthouse ulang di incognito** (tanpa ekstensi) → FCP/LCP/TBT turun
- [ ] Visual page tetap normal (no regression)

## Cara Test
```bash
curl -sL https://zaku.abysoft.my.id/ | grep -n "schema.org"
curl -sI https://www.zaku.abysoft.my.id/ | head -3
curl -s https://zaku.abysoft.my.id/sitemap.xml | head -5
curl -s https://zaku.abysoft.my.id/robots.txt
```
Lalu:
- Lighthouse **incognito** di Chrome
- Rank Math Analyzer ulang

## Catatan
- Jangan ubah `app/Http/Controllers/*`, `routes/api.php`, DB/migration.
- Semua perubahan: `public/`, view landing, `.htaccess`.
- Jika self-host font, pastikan subset woff2 (bukan raw TTF) untuk mobile.

## Reference
- Rank Math: `https://zaku.abysoft.my.id` 2026-10-23
- Lighthouse audit: `message.txt` (attached)
- Web fonts: [web.dev/perform/optimize-loading-fonts](https://web.dev/articles/optimize-loading-fonts)
- Cache headers: [developer.mozilla.org/docs/Web/HTTP/Headers/Cache-Control](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control)
