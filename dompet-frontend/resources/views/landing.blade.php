<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ZAKU — Catat Duit, Gak Ribet</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Mono:wght@400;500&family=Fraunces:ital,opsz,wght@1,9..144,300;1,9..144,700&display=swap" rel="stylesheet">
<style>

/* =====================================================
   RESET & BASE
===================================================== */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --ink:    #0E0E0E;
  --paper:  #F2EDE3;
  --orange: #FF4800;
  --yellow: #FFD600;
  --white:  #FFFFFF;
  --gray:   #9A9590;
  --gray-l: #D4CFC6;
  --b3:     3px solid var(--ink);
  --b2:     2px solid var(--ink);
  --bs:     4px 4px 0 var(--ink);
  --bsl:    6px 6px 0 var(--ink);
  --bsxl:   8px 8px 0 var(--ink);
  --max:    1200px;
  --pad:    clamp(24px, 5vw, 80px);
}

html { scroll-behavior: smooth; }

body {
  font-family: 'Syne', sans-serif;
  background: var(--paper);
  color: var(--ink);
  -webkit-font-smoothing: antialiased;
  overflow-x: hidden;
}

/* =====================================================
   NAV
===================================================== */
.nav {
  position: fixed;
  top: 0; left: 0; right: 0;
  z-index: 100;
  background: var(--ink);
  border-bottom: var(--b3);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 var(--pad);
  height: 68px;
}

.nav-logo {
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
}

.nav-logo-box {
  width: 38px; height: 38px;
  background: var(--yellow);
  border: 2px solid var(--yellow);
  display: flex; align-items: center; justify-content: center;
  font-size: 18px;
  font-weight: 800;
  color: var(--ink);
  box-shadow: 3px 3px 0 rgba(255,214,0,.3);
}

.nav-logo-text {
  font-size: 24px;
  font-weight: 800;
  color: var(--yellow);
  letter-spacing: -1.5px;
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 32px;
  list-style: none;
}

.nav-links a {
  font-family: 'DM Mono', monospace;
  font-size: 11px;
  letter-spacing: 1.5px;
  color: rgba(255,255,255,.4);
  text-decoration: none;
  transition: color .2s;
}

.nav-links a:hover { color: var(--white); }

.nav-right {
  display: flex;
  align-items: center;
  gap: 12px;
}

.nav-login {
  font-family: 'DM Mono', monospace;
  font-size: 11px;
  letter-spacing: 1.5px;
  color: rgba(255,255,255,.4);
  text-decoration: none;
  padding: 8px 16px;
  border: 1.5px solid rgba(255,255,255,.12);
}

.nav-cta {
  background: var(--orange);
  color: var(--white);
  border: 2px solid var(--orange);
  padding: 10px 20px;
  font-family: 'DM Mono', monospace;
  font-size: 11px;
  letter-spacing: 1.5px;
  text-decoration: none;
  font-weight: 500;
  box-shadow: 3px 3px 0 rgba(255,72,0,.4);
  transition: transform .1s, box-shadow .1s;
  display: inline-block;
}

.nav-cta:hover {
  transform: translate(-1px,-1px);
  box-shadow: 4px 4px 0 rgba(255,72,0,.4);
}

/* =====================================================
   HERO
===================================================== */
.hero {
  min-height: 100vh;
  background: var(--ink);
  padding-top: 68px;
  display: flex;
  flex-direction: column;
  position: relative;
  overflow: hidden;
}

/* dot grid */
.hero::before {
  content: '';
  position: absolute; inset: 0;
  background-image: radial-gradient(rgba(255,255,255,.06) 1px, transparent 1px);
  background-size: 32px 32px;
  pointer-events: none;
}

/* big bg text */
.hero-bg-text {
  position: absolute;
  bottom: -60px; right: -20px;
  font-family: 'Fraunces', serif;
  font-size: clamp(160px, 22vw, 320px);
  font-weight: 700;
  font-style: italic;
  color: rgba(255,255,255,.025);
  letter-spacing: -10px;
  line-height: 1;
  pointer-events: none;
  user-select: none;
}

.hero-inner {
  max-width: var(--max);
  margin: 0 auto;
  padding: clamp(60px, 8vw, 120px) var(--pad) 0;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  align-items: center;
  flex: 1;
  width: 100%;
  position: relative;
  z-index: 1;
}

.hero-left {}

.hero-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  font-family: 'DM Mono', monospace;
  font-size: 10px;
  letter-spacing: 3px;
  color: var(--orange);
  margin-bottom: 24px;
}

.hero-eyebrow::before {
  content: '';
  display: inline-block;
  width: 28px; height: 2px;
  background: var(--orange);
}

.hero-h1 {
  font-size: clamp(52px, 6vw, 88px);
  font-weight: 800;
  color: var(--white);
  letter-spacing: -4px;
  line-height: .92;
  margin-bottom: 10px;
}

.hero-h1-italic {
  font-family: 'Fraunces', serif;
  font-weight: 300;
  font-style: italic;
  color: var(--yellow);
  font-size: clamp(58px, 7vw, 100px);
  display: block;
  letter-spacing: -4px;
  line-height: .88;
}

.hero-sub {
  font-family: 'DM Mono', monospace;
  font-size: 13px;
  color: rgba(255,255,255,.38);
  line-height: 1.8;
  margin: 28px 0 36px;
  max-width: 420px;
  letter-spacing: .2px;
}

.hero-sub strong { color: rgba(255,255,255,.75); font-weight: 500; }

.hero-btns {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.btn-primary {
  background: var(--orange);
  color: var(--white);
  border: var(--b3);
  border-color: var(--orange);
  padding: 16px 32px;
  font-family: 'Syne', sans-serif;
  font-size: 15px;
  font-weight: 800;
  letter-spacing: .5px;
  cursor: pointer;
  box-shadow: var(--bsl);
  text-decoration: none;
  display: inline-block;
  transition: transform .1s, box-shadow .1s;
}

.btn-primary:hover { transform: translate(-2px,-2px); box-shadow: 8px 8px 0 var(--orange); }
.btn-primary:active { transform: translate(3px,3px); box-shadow: none; }

.btn-ghost {
  background: transparent;
  color: rgba(255,255,255,.5);
  border: 2px solid rgba(255,255,255,.15);
  padding: 16px 28px;
  font-family: 'DM Mono', monospace;
  font-size: 11px;
  letter-spacing: 2px;
  cursor: pointer;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: border-color .2s, color .2s;
}

.btn-ghost:hover { border-color: rgba(255,255,255,.35); color: rgba(255,255,255,.8); }

/* Hero right — phone mockup */
.hero-right {
  display: flex;
  justify-content: center;
  align-items: flex-end;
  padding-bottom: 0;
  position: relative;
}

.phone-wrap {
  position: relative;
  width: 280px;
}

.phone-glow {
  position: absolute;
  width: 300px; height: 300px;
  background: radial-gradient(circle, rgba(255,72,0,.25), transparent 70%);
  top: 50%; left: 50%;
  transform: translate(-50%,-50%);
  pointer-events: none;
}

.phone-frame {
  width: 280px;
  background: #1A1A1A;
  border: 3px solid #333;
  box-shadow: 0 40px 80px rgba(0,0,0,.6), 0 0 0 1px rgba(255,255,255,.05);
  border-radius: 32px;
  overflow: hidden;
  position: relative;
}

.phone-notch {
  height: 28px;
  background: #111;
  display: flex;
  align-items: center;
  justify-content: center;
}

.phone-notch-pill {
  width: 80px; height: 8px;
  background: #000;
  border-radius: 4px;
}

.phone-screen {
  background: #0E0E0E;
  padding: 0;
  min-height: 480px;
}

/* mini dashboard inside phone */
.mini-header {
  background: #0E0E0E;
  padding: 14px 16px 12px;
  border-bottom: 1px solid rgba(255,255,255,.06);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.mini-greet { font-family: 'DM Mono', monospace; font-size: 8px; letter-spacing: 1.5px; color: rgba(255,255,255,.3); }
.mini-name  { font-size: 14px; font-weight: 800; color: #FFD600; letter-spacing: -.5px; margin-top: 1px; }
.mini-av    { width: 30px; height: 30px; background: #FFD600; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 12px; color: #0E0E0E; }

.mini-balance {
  background: var(--orange);
  margin: 12px;
  padding: 14px;
  border: 2px solid var(--orange);
  box-shadow: 3px 3px 0 rgba(255,72,0,.5);
}

.mini-bal-label { font-family: 'DM Mono', monospace; font-size: 7px; letter-spacing: 2px; color: rgba(0,0,0,.5); margin-bottom: 3px; }
.mini-bal-amt   { font-family: 'Fraunces', serif; font-size: 26px; font-weight: 300; font-style: italic; color: #0E0E0E; letter-spacing: -1.5px; }
.mini-bal-row   { display: flex; margin-top: 10px; gap: 1px; }
.mini-bal-stat  { flex: 1; background: rgba(0,0,0,.15); padding: 7px 8px; }
.mini-bal-stat-l{ font-family: 'DM Mono', monospace; font-size: 6px; letter-spacing: 1.5px; color: rgba(0,0,0,.45); }
.mini-bal-stat-v{ font-size: 11px; font-weight: 800; color: #0E0E0E; margin-top: 2px; }

.mini-tx-label { font-family: 'DM Mono', monospace; font-size: 7px; letter-spacing: 2px; color: rgba(255,255,255,.25); padding: 10px 14px 6px; }

.mini-tx {
  display: flex; align-items: center; gap: 10px;
  padding: 9px 14px;
  border-top: 1px solid rgba(255,255,255,.04);
}

.mini-tx-ico { width: 26px; height: 26px; background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.08); display: flex; align-items: center; justify-content: center; font-size: 11px; flex-shrink: 0; }
.mini-tx-d   { flex: 1; }
.mini-tx-n   { font-size: 10px; font-weight: 700; color: rgba(255,255,255,.7); }
.mini-tx-c   { font-family: 'DM Mono', monospace; font-size: 7px; color: rgba(255,255,255,.2); letter-spacing: 1px; margin-top: 1px; }
.mini-tx-a   { font-family: 'DM Mono', monospace; font-size: 10px; font-weight: 500; }
.mini-tx.inc .mini-tx-a { color: #00C87A; }
.mini-tx.exp .mini-tx-a { color: var(--orange); }

/* Hero bottom stats bar */
.hero-stats {
  border-top: 2px solid rgba(255,255,255,.06);
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  position: relative; z-index: 1;
  max-width: 100%;
}

.hero-stat {
  padding: 28px var(--pad);
  border-right: 1px solid rgba(255,255,255,.06);
}

.hero-stat:last-child { border-right: none; }

.hs-num {
  font-family: 'Fraunces', serif;
  font-size: clamp(28px, 3.5vw, 48px);
  font-weight: 300;
  font-style: italic;
  color: var(--yellow);
  letter-spacing: -2px;
  line-height: 1;
  margin-bottom: 4px;
}

.hs-label {
  font-family: 'DM Mono', monospace;
  font-size: 9px;
  letter-spacing: 2px;
  color: rgba(255,255,255,.25);
}

/* =====================================================
   MARQUEE
===================================================== */
.marquee-wrap {
  background: var(--orange);
  border-top: var(--b3);
  border-bottom: var(--b3);
  overflow: hidden;
  padding: 14px 0;
}

.marquee-track {
  display: flex;
  width: max-content;
  animation: ticker 22s linear infinite;
}

.m-item {
  font-family: 'DM Mono', monospace;
  font-size: 12px;
  letter-spacing: 2.5px;
  color: var(--white);
  padding: 0 28px;
  white-space: nowrap;
  display: flex; align-items: center; gap: 20px;
}

.m-item::after { content: '✦'; color: rgba(255,255,255,.4); }

@@keyframes ticker {
  from { transform: translateX(0); }
  to   { transform: translateX(-50%); }
}

/* =====================================================
   SECTION BASE
===================================================== */
.section {
  padding: clamp(80px, 10vw, 140px) var(--pad);
  max-width: var(--max);
  margin: 0 auto;
}

.section-full {
  padding: clamp(80px, 10vw, 140px) var(--pad);
}

.section-full .section-inner {
  max-width: var(--max);
  margin: 0 auto;
}

.eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  font-family: 'DM Mono', monospace;
  font-size: 9px;
  letter-spacing: 3px;
  color: var(--gray);
  margin-bottom: 20px;
}

.eyebrow::before {
  content: '';
  display: inline-block;
  width: 24px; height: 2px;
  background: var(--orange);
}

/* =====================================================
   PROBLEM SECTION
===================================================== */
.problem-bg { background: var(--paper); }

.problem-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  align-items: start;
}

.prob-heading {
  font-size: clamp(36px, 4vw, 60px);
  font-weight: 800;
  letter-spacing: -3px;
  line-height: .95;
  color: var(--ink);
}

.prob-heading em {
  font-style: italic;
  font-family: 'Fraunces', serif;
  font-weight: 300;
  color: var(--orange);
  font-size: 1.1em;
}

.prob-desc {
  font-family: 'DM Mono', monospace;
  font-size: 12px;
  color: var(--gray);
  line-height: 1.8;
  margin-top: 20px;
  max-width: 380px;
  letter-spacing: .3px;
}

.prob-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.prob-item {
  display: flex;
  gap: 16px;
  align-items: flex-start;
  background: var(--white);
  border: var(--b3);
  box-shadow: var(--bs);
  padding: 20px;
  transition: transform .2s, box-shadow .2s;
}

.prob-item:hover {
  transform: translate(-2px,-2px);
  box-shadow: 6px 6px 0 var(--ink);
}

.prob-icon {
  width: 44px; height: 44px;
  border: var(--b2);
  background: #FFF0E8;
  display: flex; align-items: center; justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
}

.prob-title {
  font-size: 15px;
  font-weight: 800;
  letter-spacing: -.3px;
  margin-bottom: 5px;
}

.prob-text-desc {
  font-family: 'DM Mono', monospace;
  font-size: 11px;
  color: var(--gray);
  line-height: 1.6;
}

/* =====================================================
   HOW IT WORKS
===================================================== */
.how-bg { background: var(--ink); }

.how-heading {
  font-size: clamp(36px, 4vw, 60px);
  font-weight: 800;
  color: var(--white);
  letter-spacing: -3px;
  line-height: .95;
  margin-bottom: 8px;
}

.how-sub {
  font-family: 'DM Mono', monospace;
  font-size: 12px;
  color: rgba(255,255,255,.3);
  letter-spacing: 1px;
  margin-bottom: 64px;
}

.steps-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0;
  border: var(--b3);
  border-color: rgba(255,255,255,.1);
  box-shadow: none;
}

.step-card {
  padding: 36px 28px;
  border-right: 2px solid rgba(255,255,255,.08);
  position: relative;
}

.step-card:last-child { border-right: none; }

.step-num {
  width: 52px; height: 52px;
  background: var(--yellow);
  border: var(--b3);
  display: flex; align-items: center; justify-content: center;
  font-size: 22px;
  font-weight: 800;
  color: var(--ink);
  box-shadow: var(--bs);
  margin-bottom: 20px;
}

.step-tag {
  font-family: 'DM Mono', monospace;
  font-size: 8px;
  letter-spacing: 2.5px;
  color: var(--orange);
  margin-bottom: 10px;
}

.step-title {
  font-size: 20px;
  font-weight: 800;
  color: var(--white);
  letter-spacing: -.5px;
  margin-bottom: 10px;
}

.step-desc {
  font-family: 'DM Mono', monospace;
  font-size: 11px;
  color: rgba(255,255,255,.3);
  line-height: 1.7;
}

.step-chat {
  margin-top: 20px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.sc-bubble {
  padding: 10px 14px;
  border: 2px solid;
  font-family: 'DM Mono', monospace;
  font-size: 10px;
  line-height: 1.5;
  display: inline-flex;
  max-width: 100%;
}

.sc-bubble.usr {
  background: var(--orange);
  color: var(--white);
  border-color: var(--orange);
  align-self: flex-end;
}

.sc-bubble.ai {
  background: rgba(255,255,255,.05);
  color: rgba(255,255,255,.6);
  border-color: rgba(255,255,255,.1);
  align-self: flex-start;
}

/* =====================================================
   VS SECTION
===================================================== */
.vs-bg { background: var(--yellow); border-top: var(--b3); border-bottom: var(--b3); }

.vs-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 80px;
  align-items: start;
}

.vs-heading {
  font-size: clamp(36px, 4vw, 60px);
  font-weight: 800;
  letter-spacing: -3px;
  line-height: .95;
  color: var(--ink);
  margin-bottom: 16px;
}

.vs-sub {
  font-family: 'DM Mono', monospace;
  font-size: 12px;
  color: rgba(17,16,16,.45);
  line-height: 1.8;
  letter-spacing: .3px;
  margin-bottom: 28px;
}

.vs-table {
  border: var(--b3);
  box-shadow: var(--bsl);
  overflow: hidden;
  background: var(--white);
}

.vs-head {
  display: grid;
  grid-template-columns: 1fr 90px 90px;
  background: var(--ink);
}

.vs-hcell {
  padding: 12px 16px;
  font-family: 'DM Mono', monospace;
  font-size: 9px;
  letter-spacing: 2px;
  color: var(--yellow);
  border-right: 1px solid rgba(255,255,255,.08);
}

.vs-hcell:nth-child(2),
.vs-hcell:nth-child(3) { text-align: center; }
.vs-hcell:last-child   { border-right: none; }

.vs-row {
  display: grid;
  grid-template-columns: 1fr 90px 90px;
  border-top: var(--b2);
}

.vs-row.hl { background: #FFFAE0; }

.vs-cell {
  padding: 14px 16px;
  border-right: var(--b2);
  display: flex;
  align-items: center;
  font-family: 'DM Mono', monospace;
  font-size: 11px;
  color: rgba(17,16,16,.6);
  letter-spacing: .3px;
}

.vs-cell:last-child { border-right: none; }

.vs-cell:nth-child(2),
.vs-cell:nth-child(3) { justify-content: center; font-size: 18px; }

.chk { color: #00A36B; }
.crs { color: var(--orange); }

/* =====================================================
   FEATURES
===================================================== */
.feat-bg { background: var(--paper); }

.feat-heading {
  font-size: clamp(36px, 4vw, 60px);
  font-weight: 800;
  letter-spacing: -3px;
  line-height: .95;
  margin-bottom: 8px;
}

.feat-heading em {
  font-family: 'Fraunces', serif;
  font-style: italic;
  font-weight: 300;
  color: var(--orange);
  font-size: 1.1em;
}

.feat-sub {
  font-family: 'DM Mono', monospace;
  font-size: 12px;
  color: var(--gray);
  letter-spacing: .5px;
  margin-bottom: 56px;
}

.feat-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0;
  border: var(--b3);
  box-shadow: var(--bsxl);
}

.feat-card {
  border-right: var(--b3);
  padding: 32px 24px;
  background: var(--white);
  position: relative;
  overflow: hidden;
  transition: background .2s;
}

.feat-card:last-child { border-right: none; }
.feat-card:hover { background: #FFFDF0; }

.feat-card.c-orange { border-top: 5px solid var(--orange); }
.feat-card.c-yellow { border-top: 5px solid var(--yellow); }
.feat-card.c-green  { border-top: 5px solid #00C87A; }
.feat-card.c-blue   { border-top: 5px solid #4DA6FF; }

.feat-bg-num {
  position: absolute;
  right: 12px; top: -10px;
  font-family: 'Fraunces', serif;
  font-size: 80px;
  font-weight: 700;
  font-style: italic;
  color: rgba(17,16,16,.04);
  line-height: 1;
  pointer-events: none;
}

.feat-tag {
  font-family: 'DM Mono', monospace;
  font-size: 8px;
  letter-spacing: 2px;
  color: rgba(17,16,16,.3);
  margin-bottom: 14px;
  display: block;
}

.feat-ico {
  font-size: 32px;
  display: block;
  margin-bottom: 14px;
}

.feat-title {
  font-size: 17px;
  font-weight: 800;
  letter-spacing: -.4px;
  margin-bottom: 10px;
  color: var(--ink);
}

.feat-desc {
  font-family: 'DM Mono', monospace;
  font-size: 11px;
  color: var(--gray);
  line-height: 1.7;
}

/* second row */
.feat-grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
  margin-top: 14px;
}

.feat-card-2 {
  background: var(--white);
  border: var(--b3);
  box-shadow: var(--bs);
  padding: 28px 24px;
  display: flex;
  gap: 20px;
  align-items: flex-start;
  transition: transform .2s, box-shadow .2s;
}

.feat-card-2:hover {
  transform: translate(-2px,-2px);
  box-shadow: 6px 6px 0 var(--ink);
}

.feat-card-2-ico {
  width: 48px; height: 48px;
  background: var(--yellow);
  border: var(--b2);
  display: flex; align-items: center; justify-content: center;
  font-size: 22px;
  flex-shrink: 0;
}

.feat-card-2:nth-child(2) .feat-card-2-ico { background: #FFE8E0; }

.feat-card-2-title { font-size: 16px; font-weight: 800; letter-spacing: -.3px; margin-bottom: 8px; }
.feat-card-2-desc  { font-family: 'DM Mono', monospace; font-size: 11px; color: var(--gray); line-height: 1.7; }

/* =====================================================
   QUOTE / SOCIAL PROOF
===================================================== */
.quote-bg {
  background: var(--orange);
  border-top: var(--b3);
  border-bottom: var(--b3);
  overflow: hidden;
  position: relative;
}

.quote-bg::before {
  content: '"';
  position: absolute;
  left: var(--pad);
  top: -40px;
  font-family: 'Fraunces', serif;
  font-size: 300px;
  font-weight: 700;
  font-style: italic;
  color: rgba(255,255,255,.08);
  line-height: 1;
  pointer-events: none;
}

.quote-inner {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 60px;
  align-items: center;
}

.quote-text {
  font-size: clamp(28px, 3.5vw, 52px);
  font-weight: 800;
  color: var(--white);
  letter-spacing: -2px;
  line-height: 1.1;
  position: relative; z-index: 1;
}

.quote-text em {
  font-style: italic;
  font-family: 'Fraunces', serif;
  font-weight: 300;
  font-size: 1.15em;
}

.quote-source {
  font-family: 'DM Mono', monospace;
  font-size: 10px;
  letter-spacing: 2px;
  color: rgba(255,255,255,.5);
  margin-top: 16px;
  position: relative; z-index: 1;
}

.quote-right {
  display: flex;
  flex-direction: column;
  gap: 12px;
  position: relative; z-index: 1;
  min-width: 220px;
}

.q-stat {
  background: rgba(255,255,255,.12);
  border: 2px solid rgba(255,255,255,.2);
  padding: 16px 20px;
}

.q-stat-num {
  font-family: 'Fraunces', serif;
  font-size: 36px;
  font-weight: 300;
  font-style: italic;
  color: var(--white);
  letter-spacing: -1.5px;
  line-height: 1;
}

.q-stat-label {
  font-family: 'DM Mono', monospace;
  font-size: 9px;
  letter-spacing: 2px;
  color: rgba(255,255,255,.5);
  margin-top: 4px;
}

/* =====================================================
   PRICING
===================================================== */
.price-bg { background: var(--ink); }

.price-eyebrow { color: rgba(255,255,255,.2); }
.price-eyebrow::before { background: var(--orange); }

.price-heading {
  font-size: clamp(36px, 4vw, 60px);
  font-weight: 800;
  color: var(--white);
  letter-spacing: -3px;
  line-height: .95;
  margin-bottom: 8px;
}

.price-sub {
  font-family: 'DM Mono', monospace;
  font-size: 12px;
  color: rgba(255,255,255,.3);
  letter-spacing: .5px;
  margin-bottom: 56px;
}

.price-grid {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 0;
  border: var(--b3);
  border-color: rgba(255,255,255,.12);
  box-shadow: none;
  overflow: hidden;
}

.price-card {
  background: #141414;
  border-right: 2px solid rgba(255,255,255,.08);
  display: flex;
  flex-direction: column;
}

.price-card:last-child { border-right: none; }

.price-card.popular {
  background: #1C1C1C;
  border-top: 4px solid var(--orange);
}

.price-card-top {
  padding: 32px 28px 24px;
  border-bottom: 2px solid rgba(255,255,255,.06);
  flex-shrink: 0;
}

.p-plan {
  font-family: 'DM Mono', monospace;
  font-size: 10px;
  letter-spacing: 2.5px;
  color: rgba(255,255,255,.3);
  margin-bottom: 12px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.p-badge {
  background: var(--orange);
  color: var(--white);
  font-size: 7px;
  letter-spacing: 1.5px;
  padding: 3px 8px;
}

.p-price {
  font-family: 'Fraunces', serif;
  font-size: 48px;
  font-weight: 300;
  font-style: italic;
  color: var(--white);
  letter-spacing: -2px;
  line-height: 1;
  margin-bottom: 4px;
}

.p-price span {
  font-family: 'DM Mono', monospace;
  font-size: 11px;
  font-style: normal;
  color: rgba(255,255,255,.25);
  letter-spacing: 1px;
}

.p-desc {
  font-family: 'DM Mono', monospace;
  font-size: 11px;
  color: rgba(255,255,255,.25);
  letter-spacing: .3px;
  line-height: 1.6;
  margin-top: 10px;
}

.price-feats {
  padding: 24px 28px;
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.p-feat {
  display: flex;
  align-items: center;
  gap: 10px;
  font-family: 'DM Mono', monospace;
  font-size: 11px;
  color: rgba(255,255,255,.5);
  letter-spacing: .3px;
}

.p-feat-dot {
  width: 5px; height: 5px;
  background: var(--orange);
  flex-shrink: 0;
}

.p-feat.dim { opacity: .3; }
.p-feat.dim .p-feat-dot { background: rgba(255,255,255,.3); }

.price-card-cta {
  padding: 20px 28px;
  border-top: 2px solid rgba(255,255,255,.06);
}

.p-btn {
  display: block;
  width: 100%;
  padding: 14px;
  text-align: center;
  font-family: 'Syne', sans-serif;
  font-size: 13px;
  font-weight: 800;
  letter-spacing: .5px;
  text-decoration: none;
  cursor: pointer;
  border: var(--b2);
  transition: transform .1s, box-shadow .1s;
}

.p-btn.outline {
  background: transparent;
  color: rgba(255,255,255,.4);
  border-color: rgba(255,255,255,.12);
}

.p-btn.outline:hover { border-color: rgba(255,255,255,.3); color: rgba(255,255,255,.7); }

.p-btn.fill {
  background: var(--orange);
  color: var(--white);
  border-color: var(--orange);
  box-shadow: var(--bsl);
}

.p-btn.fill:hover { transform: translate(-2px,-2px); box-shadow: 8px 8px 0 rgba(255,72,0,.4); }
.p-btn.fill:active { transform: translate(3px,3px); box-shadow: none; }

/* =====================================================
   FINAL CTA
===================================================== */
.cta-bg {
  background: var(--paper);
  border-top: var(--b3);
  overflow: hidden;
  position: relative;
}

.cta-bg::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(17,16,16,.04) 1px, transparent 1px),
    linear-gradient(90deg, rgba(17,16,16,.04) 1px, transparent 1px);
  background-size: 40px 40px;
}

.cta-inner {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 80px;
  align-items: center;
  position: relative; z-index: 1;
}

.cta-heading {
  font-size: clamp(44px, 6vw, 96px);
  font-weight: 800;
  letter-spacing: -5px;
  line-height: .88;
  color: var(--ink);
}

.cta-heading em {
  font-style: italic;
  font-family: 'Fraunces', serif;
  font-weight: 300;
  color: var(--orange);
  font-size: 1.1em;
  display: block;
}

.cta-note {
  font-family: 'DM Mono', monospace;
  font-size: 10px;
  color: var(--gray);
  letter-spacing: 2px;
  margin-top: 20px;
}

.cta-right {
  display: flex;
  flex-direction: column;
  gap: 12px;
  min-width: 280px;
}

.btn-cta-big {
  display: block;
  background: var(--orange);
  color: var(--white);
  border: var(--b3);
  border-color: var(--orange);
  padding: 22px 32px;
  font-family: 'Syne', sans-serif;
  font-size: 16px;
  font-weight: 800;
  text-align: center;
  text-decoration: none;
  box-shadow: var(--bsxl);
  cursor: pointer;
  transition: transform .1s, box-shadow .1s;
  letter-spacing: .5px;
}

.btn-cta-big:hover { transform: translate(-3px,-3px); box-shadow: 11px 11px 0 var(--orange); }
.btn-cta-big:active { transform: translate(4px,4px); box-shadow: none; }

.btn-cta-login {
  display: block;
  background: transparent;
  color: var(--gray);
  border: var(--b2);
  border-color: var(--gray-l);
  padding: 14px 32px;
  font-family: 'DM Mono', monospace;
  font-size: 11px;
  letter-spacing: 2px;
  text-align: center;
  text-decoration: none;
  cursor: pointer;
  transition: border-color .2s, color .2s;
}

.btn-cta-login:hover { border-color: var(--ink); color: var(--ink); }

/* =====================================================
   FOOTER
===================================================== */
.footer {
  background: var(--ink);
  border-top: var(--b3);
  padding: 28px var(--pad);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.foot-logo {
  display: flex;
  align-items: center;
  gap: 10px;
}

.foot-logo-box {
  width: 28px; height: 28px;
  background: var(--yellow);
  display: flex; align-items: center; justify-content: center;
  font-size: 12px;
  font-weight: 800;
  color: var(--ink);
}

.foot-logo-text {
  font-size: 18px;
  font-weight: 800;
  color: var(--yellow);
  letter-spacing: -1px;
}

.foot-links {
  display: flex;
  gap: 24px;
  list-style: none;
}

.foot-links a {
  font-family: 'DM Mono', monospace;
  font-size: 10px;
  letter-spacing: 1.5px;
  color: rgba(255,255,255,.2);
  text-decoration: none;
  transition: color .2s;
}

.foot-links a:hover { color: rgba(255,255,255,.6); }

.foot-copy {
  font-family: 'DM Mono', monospace;
  font-size: 9px;
  letter-spacing: 1.5px;
  color: rgba(255,255,255,.15);
}

/* =====================================================
   REVEAL ANIMATION
===================================================== */
.reveal {
  opacity: 0;
  transform: translateY(28px);
  transition: opacity .55s ease, transform .55s ease;
}

.reveal.in {
  opacity: 1;
  transform: translateY(0);
}

/* =====================================================
   RESPONSIVE
===================================================== */
@@media (max-width: 1024px) {
  .hero-inner      { grid-template-columns: 1fr; gap: 40px; }
  .hero-right      { display: none; }
  .problem-grid    { grid-template-columns: 1fr; gap: 40px; }
  .vs-grid         { grid-template-columns: 1fr; gap: 40px; }
  .feat-grid       { grid-template-columns: 1fr 1fr; }
  .feat-card:nth-child(2) { border-right: none; }
  .feat-card:nth-child(3) { border-top: var(--b3); }
  .steps-grid      { grid-template-columns: 1fr; }
  .step-card       { border-right: none; border-bottom: 2px solid rgba(255,255,255,.08); }
  .step-card:last-child { border-bottom: none; }
  .price-grid      { grid-template-columns: 1fr; border: none; gap: 12px; }
  .price-card      { border: var(--b3); border-color: rgba(255,255,255,.12); box-shadow: 4px 4px 0 rgba(255,255,255,.04); }
  .quote-inner     { grid-template-columns: 1fr; }
  .cta-inner       { grid-template-columns: 1fr; gap: 40px; }
  .hero-stats      { grid-template-columns: repeat(3, 1fr); }
}

@@media (max-width: 640px) {
  .nav-links { display: none; }
  .feat-grid { grid-template-columns: 1fr; }
  .feat-card { border-right: none; border-bottom: var(--b3); }
  .feat-card:last-child { border-bottom: none; }
  .feat-grid-2 { grid-template-columns: 1fr; }
  .hero-stats { grid-template-columns: 1fr 1fr; }
  .hero-stat:last-child { grid-column: span 2; border-right: none; }
}

</style>
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
================================================================ -->
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
                <div class="mini-name">dhaby</div>
              </div>
              <div class="mini-av">D</div>
            </div>
            <div class="mini-balance">
              <div class="mini-bal-label">SALDO BULAN INI</div>
              <div class="mini-bal-amt">Rp 1.602.000</div>
              <div class="mini-bal-row">
                <div class="mini-bal-stat" style="margin-right:2px">
                  <div class="mini-bal-stat-l">▲ PEMASUKAN</div>
                  <div class="mini-bal-stat-v">Rp 4.000.000</div>
                </div>
                <div class="mini-bal-stat">
                  <div class="mini-bal-stat-l">▼ PENGELUARAN</div>
                  <div class="mini-bal-stat-v">Rp 2.398.000</div>
                </div>
              </div>
            </div>
            <div class="mini-tx-label">TRANSAKSI TERAKHIR</div>
            <div class="mini-tx exp">
              <div class="mini-tx-ico">🚗</div>
              <div class="mini-tx-d">
                <div class="mini-tx-n">Bensin</div>
                <div class="mini-tx-c">TRANSPORT</div>
              </div>
              <div class="mini-tx-a">-Rp 20.000</div>
            </div>
            <div class="mini-tx inc">
              <div class="mini-tx-ico">💰</div>
              <div class="mini-tx-d">
                <div class="mini-tx-n">Gaji Mei</div>
                <div class="mini-tx-c">PEMASUKAN</div>
              </div>
              <div class="mini-tx-a">+Rp 4.000.000</div>
            </div>
            <div class="mini-tx exp">
              <div class="mini-tx-ico">🍜</div>
              <div class="mini-tx-d">
                <div class="mini-tx-n">Makan siang</div>
                <div class="mini-tx-c">MAKANAN</div>
              </div>
              <div class="mini-tx-a">-Rp 35.000</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="hero-stats">
    <div class="hero-stat reveal">
      <div class="hs-num" id="stat-users">2.000+</div>
      <div class="hs-label">PENGGUNA AKTIF</div>
    </div>
    <div class="hero-stat reveal">
      <div class="hs-num" id="stat-transactions">47jt</div>
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
================================================================ -->
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
================================================================ -->
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
================================================================ -->
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
================================================================ -->
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
================================================================ -->
<div class="quote-bg section-full">
  <div class="section-inner">
    <div class="section">
      <div class="quote-inner">
        <div class="reveal">
          <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:3px;color:rgba(255,255,255,.4);margin-bottom:20px;display:flex;align-items:center;gap:10px"><span style="display:inline-block;width:24px;height:2px;background:rgba(255,255,255,.3)"></span>EARLY ADOPTER</div>
          <div class="quote-text">
            "Akhirnya ada yang <em>beneran ngerti</em> cara orang Indonesia ngomong soal duit."
          </div>
          <div class="quote-source">— PENGGUNA AKTIF · BANDUNG</div>
        </div>
        <div class="quote-right reveal">
          <div class="q-stat">
            <div class="q-stat-num">4.9/5</div>
            <div class="q-stat-label">RATING PENGGUNA</div>
          </div>
          <div class="q-stat">
            <div class="q-stat-num">3 menit</div>
            <div class="q-stat-label">RATA-RATA ONBOARDING</div>
          </div>
          <div class="q-stat">
            <div class="q-stat-num">92%</div>
            <div class="q-stat-label">RETENTION BULAN KE-3</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ================================================================
     PRICING
================================================================ -->
<div class="price-bg section-full" id="pricing">
  <div class="section-inner">
    <div class="section" style="padding-bottom:0">
      <div class="eyebrow price-eyebrow">PRICING</div>
      <h2 class="price-heading">Mulai gratis, upgrade kalau mau.</h2>
      <p class="price-sub">Gak ada kartu kredit. Gak ada hidden fee. Gak ada jebakan.</p>
    </div>
    <div style="padding: 0 var(--pad) clamp(80px,10vw,140px);">
      <div class="price-grid reveal">

        <div class="price-card">
          <div class="price-card-top">
            <div class="p-plan">FREE FOREVER</div>
            <div class="p-price">Rp 0 <span>/bulan</span></div>
            <div class="p-desc">Cocok buat yang baru mau mulai kebiasaan catat keuangan.</div>
          </div>
          <div class="price-feats">
            <div class="p-feat"><div class="p-feat-dot"></div>50 transaksi / bulan</div>
            <div class="p-feat"><div class="p-feat-dot"></div>AI chat input</div>
            <div class="p-feat"><div class="p-feat-dot"></div>Dashboard basic</div>
            <div class="p-feat dim"><div class="p-feat-dot"></div>Insight lanjutan</div>
            <div class="p-feat dim"><div class="p-feat-dot"></div>Export PDF</div>
            <div class="p-feat dim"><div class="p-feat-dot"></div>Budget health score</div>
          </div>
          <div class="price-card-cta">
            <a href="{{ url('/register') }}" class="p-btn outline">MULAI GRATIS</a>
          </div>
        </div>

        <div class="price-card popular">
          <div class="price-card-top">
            <div class="p-plan">PRO <span class="p-badge">POPULER</span></div>
            <div class="p-price">Rp 29.000 <span>/bulan</span></div>
            <div class="p-desc">Untuk yang serius kontrol keuangan dan mau insight lebih dalam.</div>
          </div>
          <div class="price-feats">
            <div class="p-feat"><div class="p-feat-dot"></div>Transaksi unlimited</div>
            <div class="p-feat"><div class="p-feat-dot"></div>AI insight proaktif</div>
            <div class="p-feat"><div class="p-feat-dot"></div>Budget health score</div>
            <div class="p-feat"><div class="p-feat-dot"></div>Export PDF laporan</div>
            <div class="p-feat"><div class="p-feat-dot"></div>Recurring transaction</div>
            <div class="p-feat"><div class="p-feat-dot"></div>Prioritas support</div>
          </div>
          <div class="price-card-cta">
            <a href="{{ url('/register') }}" class="p-btn fill">UPGRADE SEKARANG →</a>
          </div>
        </div>

        <div class="price-card">
          <div class="price-card-top">
            <div class="p-plan">BUSINESS</div>
            <div class="p-price">Rp 99.000 <span>/bulan</span></div>
            <div class="p-desc">Untuk UMKM dan tim kecil yang butuh pencatatan keuangan bisnis.</div>
          </div>
          <div class="price-feats">
            <div class="p-feat"><div class="p-feat-dot"></div>Semua fitur Pro</div>
            <div class="p-feat"><div class="p-feat-dot"></div>Multi-user (5 akun)</div>
            <div class="p-feat"><div class="p-feat-dot"></div>Laporan keuangan bisnis</div>
            <div class="p-feat"><div class="p-feat-dot"></div>API access</div>
            <div class="p-feat"><div class="p-feat-dot"></div>Dedicated support</div>
            <div class="p-feat"><div class="p-feat-dot"></div>Custom kategori</div>
          </div>
          <div class="price-card-cta">
            <a href="{{ url('/register') }}" class="p-btn outline">HUBUNGI KAMI</a>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- ================================================================
     FINAL CTA
================================================================ -->
<div class="cta-bg section-full" id="cta">
  <div class="section-inner">
    <div class="section">
      <div class="cta-inner">
        <div class="reveal">
          <h2 class="cta-heading">
            Duit kamu,
            <em>ZAKU yang jaga.</em>
          </h2>
          <p class="cta-note">GRATIS · TANPA KARTU KREDIT · LANGSUNG JALAN</p>
        </div>
        <div class="cta-right reveal">
          <a href="{{ url('/register') }}" class="btn-cta-big">DAFTAR SEKARANG, GRATIS →</a>
          <a href="{{ url('/login') }}" class="btn-cta-login">Sudah punya akun? Masuk →</a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- FOOTER -->
<footer class="footer">
  <div class="foot-logo">
    <div class="foot-logo-box">Z</div>
    <div class="foot-logo-text">ZAKU</div>
  </div>
  <ul class="foot-links">
    <li><a href="#">TENTANG</a></li>
    <li><a href="#">PRIVACY</a></li>
    <li><a href="#">TERMS</a></li>
    <li><a href="#">KONTAK</a></li>
  </ul>
  <div class="foot-copy">© 2026 ZAKU · MADE IN INDONESIA</div>
</footer>

<script>
const io = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
  });
}, { threshold: 0.1 });

document.querySelectorAll('.reveal').forEach(el => io.observe(el));

document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const id = a.getAttribute('href');
    if (id === '#') return;
    e.preventDefault();
    const t = document.querySelector(id);
    if (t) t.scrollIntoView({ behavior: 'smooth' });
  });
});

// Fetch public stats from API and update the page
fetch('{{ url("/api/v1/stats/public") }}')
  .then(res => res.json())
  .then(data => {
    const stats = data.data || data;
    const usersEl = document.getElementById('stat-users');
    const txEl = document.getElementById('stat-transactions');

    if (usersEl && stats.user_count != null) {
      const count = Number(stats.user_count);
      if (count >= 1000) {
        const formatted = count.toLocaleString('id-ID');
        usersEl.textContent = formatted + '+';
      } else {
        usersEl.textContent = count + '+';
      }
    }

    if (txEl && stats.transaction_count != null) {
      const count = Number(stats.transaction_count);
      if (count >= 1000) {
        const inMillions = count / 1000000;
        if (inMillions >= 1) {
          txEl.textContent = Math.round(inMillions) + 'jt';
        } else {
          const inThousands = count / 1000;
          txEl.textContent = Math.round(inThousands) + 'rb transaksi';
        }
      } else {
        txEl.textContent = count + ' transaksi';
      }
    }
  })
  .catch(() => {
    // Fallback: keep the default hardcoded values already in the HTML
  });
</script>
</body>
</html>
