<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MatrixEdu — Grade de horários &amp; distribuição de notebooks</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Space+Grotesk:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  :root{
    --chalk-bg:#153029;
    --chalk-bg-2:#1c3d33;
    --chalk-bg-3:#20463b;
    --paper:#f7f2e3;
    --paper-2:#efe7d0;
    --paper-line:#d9c9a3;
    --ink:#1e2a24;
    --ink-soft:#4a564e;
    --chalk-white:#f4f1e6;
    --amber:#e8b84b;
    --amber-deep:#c9932a;
    --sky:#93c3d8;
    --red-pen:#d9614f;
    --green-check:#8ab876;
    --font-display:'Caveat', cursive;
    --font-body:'Space Grotesk', sans-serif;
    --font-mono:'IBM Plex Mono', monospace;
    --radius:14px;
    --shadow-paper: 0 10px 30px rgba(0,0,0,.28), 0 2px 0 rgba(0,0,0,.15);
  }
  *{box-sizing:border-box;}
  html{scroll-behavior:smooth;}
  body.reduce-motion, body.reduce-motion *{scroll-behavior:auto !important; animation-duration:.001s !important; transition-duration:.001s !important;}
  body{
    margin:0;
    background:var(--chalk-bg);
    background-image:
      linear-gradient(var(--chalk-bg-2) 1px, transparent 1px),
      linear-gradient(90deg, var(--chalk-bg-2) 1px, transparent 1px);
    background-size:42px 42px;
    color:var(--chalk-white);
    font-family:var(--font-body);
    line-height:1.5;
    -webkit-font-smoothing:antialiased;
  }
  body.font-lg{font-size:112.5%;}
  a{color:inherit;}
  .skip-link{position:absolute; left:-999px; top:0; background:var(--amber); color:var(--ink); padding:.6em 1em; border-radius:0 0 8px 0; z-index:200; font-weight:600;}
  .skip-link:focus{left:0;}
  img,svg{display:block; max-width:100%;}
  h1,h2,h3{font-family:var(--font-body); margin:0 0 .3em; letter-spacing:-.01em;}
  p{margin:0 0 1em;}
  .wrap{max-width:1180px; margin:0 auto; padding:0 24px;}
  .eyebrow{font-family:var(--font-mono); font-size:.78rem; letter-spacing:.14em; text-transform:uppercase; color:var(--amber); margin-bottom:.6em; display:flex; align-items:center; gap:.5em;}
  .eyebrow::before{content:''; width:18px; height:2px; background:var(--amber); display:inline-block;}

  /* ---------- Header ---------- */
  header.site{
    position:sticky; top:0; z-index:100;
    background:rgba(21,48,41,.92);
    backdrop-filter:blur(6px);
    border-bottom:1px solid rgba(244,241,230,.12);
  }
  .nav-inner{display:flex; align-items:center; justify-content:space-between; padding:14px 0;}
  .logo{display:flex; align-items:center; gap:10px; text-decoration:none; color:var(--chalk-white);}
  .logo-grid{display:grid; grid-template-columns:repeat(6,15px); gap:2px;}
  .logo-grid span{width:15px; height:15px; border:1.4px solid var(--sky); display:flex; align-items:center; justify-content:center; font-family:var(--font-mono); font-size:.62rem; font-weight:600; color:var(--sky);}
  .logo-grid span.on{background:var(--amber); border-color:var(--amber); color:var(--ink);}
  .logo-edu{font-family:var(--font-display); font-size:1.9rem; color:var(--amber); line-height:1; transform:rotate(-3deg); margin-left:2px;}
  nav.main-links{display:flex; gap:26px; font-size:.94rem;}
  nav.main-links a{text-decoration:none; opacity:.85; position:relative; padding:4px 0;}
  nav.main-links a:hover, nav.main-links a:focus-visible{opacity:1;}
  nav.main-links a::after{content:''; position:absolute; left:0; right:100%; bottom:-2px; height:2px; background:var(--amber); transition:right .25s ease;}
  nav.main-links a:hover::after, nav.main-links a:focus-visible::after{right:0;}
  .burger{display:none; background:none; border:1px solid rgba(244,241,230,.4); color:var(--chalk-white); border-radius:8px; padding:8px 10px; font-size:1.1rem;}
  #authBtn{white-space:nowrap;}

  /* ---------- Hero ---------- */
  .hero{padding:88px 0 70px; position:relative; overflow:hidden;}
  .hero-doodles{position:absolute; inset:0; pointer-events:none; opacity:.5;}
  .hero-inner{position:relative; display:grid; grid-template-columns:1.15fr .85fr; gap:50px; align-items:center;}
  .hero h1{font-size:clamp(2.4rem, 4.6vw, 3.7rem); line-height:1.04;}
  .hero h1 .script{font-family:var(--font-display); color:var(--amber); font-size:1.12em; display:inline-block; transform:rotate(-2deg);}
  .hero p.lede{font-size:1.12rem; color:#d8d3c2; max-width:46ch;}
  .hero-cta{display:flex; gap:14px; flex-wrap:wrap; margin-top:8px;}
  .btn{
    font-family:var(--font-body); font-weight:600; font-size:.95rem; border:none; cursor:pointer;
    padding:13px 22px; border-radius:10px; text-decoration:none; display:inline-flex; align-items:center; gap:8px;
  }
  .btn-primary{background:var(--amber); color:var(--ink);}
  .btn-primary:hover{background:var(--amber-deep);}
  .btn-ghost{background:transparent; color:var(--chalk-white); border:1.5px solid rgba(244,241,230,.4);}
  .btn-ghost:hover{border-color:var(--amber); color:var(--amber);}
  .btn:focus-visible, a:focus-visible, input:focus-visible, select:focus-visible, button:focus-visible{outline:3px solid var(--sky); outline-offset:2px;}

  .hero-card{
    background:var(--paper); color:var(--ink); border-radius:var(--radius); padding:26px;
    box-shadow:var(--shadow-paper); transform:rotate(1.4deg);
    background-image:repeating-linear-gradient(var(--paper) 0 31px, var(--paper-line) 31px 32px);
    position:relative;
  }
  .hero-card::before{content:''; position:absolute; left:34px; top:0; bottom:0; width:2px; background:var(--red-pen); opacity:.55;}
  .hero-card h2{font-family:var(--font-mono); font-size:.85rem; text-transform:uppercase; letter-spacing:.08em; color:var(--ink-soft); margin-bottom:14px; padding-left:22px;}
  .quick-list{list-style:none; margin:0; padding:0 0 0 22px; display:flex; flex-direction:column; gap:13px;}
  .quick-list a{display:flex; justify-content:space-between; text-decoration:none; color:var(--ink); font-weight:600; padding-bottom:11px; border-bottom:1px dashed var(--paper-line);}
  .quick-list a span.num{font-family:var(--font-mono); color:var(--red-pen); font-weight:500;}

  section{padding:76px 0;}
  .section-head{max-width:640px; margin-bottom:38px;}
  .section-head h2{font-size:clamp(1.8rem,3vw,2.4rem);}
  .section-head p{color:#d8d3c2;}

  /* ---------- Grade section ---------- */
  #grade{background:var(--chalk-bg-2); border-top:1px solid rgba(244,241,230,.1); border-bottom:1px solid rgba(244,241,230,.1);}
  .shift-tabs{display:inline-flex; background:var(--chalk-bg); border-radius:999px; padding:4px; gap:4px; margin-bottom:26px;}
  .shift-tabs button{
    font-family:var(--font-mono); font-size:.82rem; letter-spacing:.04em; text-transform:uppercase;
    background:transparent; border:none; color:var(--chalk-white); opacity:.7; padding:9px 20px; border-radius:999px; cursor:pointer;
  }
  .shift-tabs button.active{background:var(--amber); color:var(--ink); opacity:1; font-weight:600;}
  .shift-meta{font-family:var(--font-mono); font-size:.85rem; color:var(--sky); margin-bottom:22px;}
  .grade-scroll{overflow-x:auto; background:var(--paper); border-radius:var(--radius); box-shadow:var(--shadow-paper); padding:6px;}
  table.grade{border-collapse:collapse; width:100%; min-width:760px; color:var(--ink);}
  table.grade th, table.grade td{border:1px solid var(--paper-line); padding:0;}
  table.grade thead th{
    font-family:var(--font-mono); font-size:.78rem; text-transform:uppercase; letter-spacing:.05em;
    background:var(--paper-2); padding:12px 8px; position:sticky; top:0;
  }
  table.grade thead th:first-child{text-align:left; padding-left:14px;}
  table.grade td.time-cell{
    font-family:var(--font-mono); font-size:.8rem; white-space:nowrap; padding:10px 14px; background:var(--paper-2); color:var(--ink-soft);
  }
  table.grade tr.interval td{background:repeating-linear-gradient(135deg, var(--paper-2), var(--paper-2) 6px, var(--paper) 6px, var(--paper) 12px); font-family:var(--font-display); font-size:1.2rem; color:var(--red-pen); text-align:center; padding:6px;}
  table.grade td.cell input{
    width:100%; border:none; background:transparent; font-family:var(--font-body); font-size:.86rem; color:var(--ink);
    padding:10px 8px; text-align:center;
  }
  table.grade td.cell input::placeholder{color:#b9ac8c;}
  table.grade td.cell input:focus{background:#fffdf6; outline:2px solid var(--amber); outline-offset:-2px;}
  .grade-note{font-family:var(--font-mono); font-size:.8rem; color:#c9c3ae; margin-top:14px;}

  .day-tabs{display:flex; gap:6px; margin-bottom:16px; flex-wrap:wrap;}
  .day-tabs button{font-family:var(--font-mono); font-size:.74rem; text-transform:uppercase; letter-spacing:.03em; background:var(--chalk-bg-3); color:var(--chalk-white); border:1px solid rgba(244,241,230,.2); padding:7px 14px; border-radius:8px; cursor:pointer; opacity:.75;}
  .day-tabs button:hover{opacity:1;}
  .day-tabs button.active{background:var(--sky); color:var(--chalk-bg); opacity:1; font-weight:600; border-color:var(--sky);}

  .bell-card{background:var(--paper); color:var(--ink); border-radius:var(--radius); box-shadow:var(--shadow-paper); padding:16px 20px; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:26px;}
  .bell-status{display:flex; align-items:center; gap:12px;}
  .bell-status strong{display:block; font-size:.98rem;}
  .bell-sub{font-family:var(--font-mono); font-size:.78rem; color:var(--ink-soft); margin-top:2px;}
  .bell-dot{width:12px; height:12px; border-radius:50%; flex:none; background:#b9ac8c;}
  .bell-dot.aula{background:var(--green-check);}
  .bell-dot.intervalo{background:var(--red-pen);}
  .bell-dot.fora{background:#b9ac8c;}

  .lock-banner{background:#fde3dd; color:#a8402f; border:1px solid #f3b6ab; border-radius:8px; padding:10px 14px; font-size:.85rem; font-weight:600; margin:12px 0;}
  .manual-lock-row{display:flex; align-items:center; justify-content:space-between; gap:12px; border:1px dashed var(--paper-line); border-radius:8px; padding:10px 12px; margin-bottom:6px;}
  .manual-lock-row .lbl{font-weight:600; font-size:.85rem; color:var(--ink);}
  .manual-lock-row .desc{font-size:.74rem; color:var(--ink-soft); margin-top:1px;}
  .checkout-form select:disabled, .checkout-form input:disabled, .checkout-form button:disabled{opacity:.5; cursor:not-allowed;}
  .action-toggle label:has(input:disabled){opacity:.5; cursor:not-allowed;}

  /* ---------- Carrinhos section ---------- */
  .cart-grid{display:grid; grid-template-columns:repeat(3,1fr); gap:22px; margin-bottom:40px;}
  .cart-card{background:var(--paper); color:var(--ink); border-radius:var(--radius); padding:22px; box-shadow:var(--shadow-paper);}
  .cart-card .tag{font-family:var(--font-mono); font-size:.72rem; text-transform:uppercase; letter-spacing:.08em; background:var(--chalk-bg); color:var(--chalk-white); display:inline-block; padding:4px 10px; border-radius:999px; margin-bottom:10px;}
  .cart-card h3{font-size:1.35rem;}
  .gauge{height:10px; border-radius:999px; background:var(--paper-2); overflow:hidden; margin:14px 0 8px;}
  .gauge-fill{height:100%; background:var(--green-check); border-radius:999px;}
  .cart-stats{display:flex; justify-content:space-between; font-family:var(--font-mono); font-size:.82rem; color:var(--ink-soft);}
  .cart-stats strong{color:var(--ink); font-size:1rem;}

  .checkout-panel{background:var(--paper); color:var(--ink); border-radius:var(--radius); box-shadow:var(--shadow-paper); padding:30px; display:grid; grid-template-columns:1.1fr .9fr; gap:34px;}
  .checkout-form label{display:block; font-family:var(--font-mono); font-size:.76rem; text-transform:uppercase; letter-spacing:.06em; color:var(--ink-soft); margin:14px 0 6px;}
  .checkout-form label:first-child{margin-top:0;}
  .checkout-form select, .checkout-form input[type=number]{
    width:100%; padding:11px 12px; border-radius:8px; border:1.5px solid var(--paper-line); font-family:var(--font-body); font-size:.95rem; background:#fffdf7; color:var(--ink);
  }
  .action-toggle{display:flex; gap:10px; margin-top:6px;}
  .action-toggle label{
    flex:1; margin:0; display:flex; align-items:center; justify-content:center; gap:8px; text-transform:none; letter-spacing:0;
    border:1.5px solid var(--paper-line); border-radius:8px; padding:11px; cursor:pointer; font-family:var(--font-body); font-size:.9rem; color:var(--ink); font-weight:600;
  }
  .action-toggle input{accent-color:var(--amber);}
  .action-toggle label.checked{border-color:var(--amber); background:#fff6e0;}
  .checkout-form .submit{margin-top:20px; width:100%; justify-content:center;}
  .log-col h4{font-family:var(--font-mono); font-size:.8rem; text-transform:uppercase; letter-spacing:.06em; color:var(--ink-soft); margin-bottom:12px;}
  .log-list{list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:10px; max-height:330px; overflow-y:auto;}
  .log-list li{border-bottom:1px dashed var(--paper-line); padding-bottom:9px; font-size:.86rem; display:flex; justify-content:space-between; gap:10px;}
  .log-list .who{font-weight:600;}
  .log-list .badge{font-family:var(--font-mono); font-size:.68rem; padding:2px 8px; border-radius:999px; height:fit-content;}
  .badge.out{background:#fde3dd; color:#a8402f;}
  .badge.in{background:#e2f0d9; color:#4c7635;}
  .log-empty{color:#b9ac8c; font-size:.86rem;}

  /* stamp overlay */
  .stamp-overlay{position:fixed; inset:0; display:flex; align-items:center; justify-content:center; pointer-events:none; z-index:300;}
  .stamp{
    font-family:var(--font-mono); font-weight:700; font-size:2.1rem; letter-spacing:.06em; text-transform:uppercase;
    border:6px solid; padding:16px 34px; border-radius:10px; opacity:0; transform:scale(2.6) rotate(-14deg);
    background:rgba(247,242,227,.06);
  }
  .stamp.show{animation:stampSlam .95s ease forwards;}
  .stamp.out-c{color:var(--red-pen); border-color:var(--red-pen);}
  .stamp.in-c{color:var(--green-check); border-color:var(--green-check);}
  @keyframes stampSlam{
    0%{opacity:0; transform:scale(2.6) rotate(-14deg);}
    35%{opacity:1; transform:scale(1) rotate(-8deg);}
    75%{opacity:1; transform:scale(1) rotate(-8deg);}
    100%{opacity:0; transform:scale(1) rotate(-8deg);}
  }

  /* ---------- Acessibilidade informational ---------- */
  #acessibilidade .a11y-grid{display:grid; grid-template-columns:.9fr 1.1fr; gap:44px; align-items:center;}
  .bit-portrait{background:var(--paper); border-radius:var(--radius); box-shadow:var(--shadow-paper); padding:30px; text-align:center;}
  .a11y-list{list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:16px;}
  .a11y-list li{display:flex; gap:14px; align-items:flex-start; background:var(--chalk-bg-3); border:1px solid rgba(244,241,230,.12); border-radius:10px; padding:14px 16px;}
  .a11y-list .ico{font-family:var(--font-mono); background:var(--amber); color:var(--ink); width:30px; height:30px; min-width:30px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-weight:700;}

  footer{padding:34px 0 46px; border-top:1px solid rgba(244,241,230,.12); color:#a9a390; font-size:.85rem;}
  footer .wrap{display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px;}

  /* ---------- Mascote / assistant ---------- */
  #bit-launcher{
    position:fixed; right:22px; bottom:22px; z-index:150; background:var(--paper); border:none; border-radius:999px;
    width:64px; height:64px; box-shadow:0 8px 24px rgba(0,0,0,.4); cursor:pointer; display:flex; align-items:center; justify-content:center;
  }
  #bit-launcher:hover{transform:translateY(-2px);}
  #bit-bubble{
    position:fixed; right:22px; bottom:96px; z-index:150; background:var(--paper); color:var(--ink); padding:12px 16px; border-radius:12px 12px 2px 12px;
    box-shadow:var(--shadow-paper); max-width:230px; font-size:.88rem; font-weight:600; transition:opacity .4s, transform .4s;
  }
  #bit-bubble[hidden]{display:none;}

  .a11y-panel{
    position:fixed; top:0; right:0; height:100%; width:min(380px, 92vw); background:var(--chalk-bg-2); color:var(--chalk-white);
    box-shadow:-10px 0 30px rgba(0,0,0,.4); z-index:220; transform:translateX(100%); transition:transform .3s ease;
    padding:26px 24px; overflow-y:auto;
  }
  .a11y-panel.open{transform:translateX(0);}
  .a11y-panel h3{font-size:1.3rem;}
  .a11y-panel .close-x{position:absolute; top:18px; right:18px; background:none; border:none; color:var(--chalk-white); font-size:1.3rem; cursor:pointer;}
  .a11y-row{display:flex; align-items:center; justify-content:space-between; padding:14px 0; border-bottom:1px solid rgba(244,241,230,.14);}
  .a11y-row .lbl{font-weight:600; font-size:.95rem;}
  .a11y-row .desc{font-size:.78rem; color:#b9c9c2; margin-top:2px; max-width:230px;}
  .switch{position:relative; display:inline-block; width:44px; height:24px; flex:none;}
  .switch input{opacity:0; width:0; height:0;}
  .slider-tg{position:absolute; cursor:pointer; inset:0; background:#4a5a53; border-radius:999px; transition:.2s;}
  .slider-tg::before{content:''; position:absolute; height:18px; width:18px; left:3px; top:3px; background:white; border-radius:50%; transition:.2s;}
  input:checked + .slider-tg{background:var(--amber);}
  input:checked + .slider-tg::before{transform:translateX(20px);}
  .a11y-panel .speak-btn{width:100%; justify-content:center; margin-top:16px;}
  .a11y-note{font-size:.8rem; color:#b9c9c2; background:var(--chalk-bg-3); padding:12px 14px; border-radius:10px; margin-top:22px;}
  .overlay-bg{position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:210; opacity:0; pointer-events:none; transition:opacity .3s;}
  .overlay-bg.show{opacity:1; pointer-events:auto;}

  /* visual pulse flash used instead of relying on sound cues */
  body.flash-pulse{animation:pulseFlash .5s ease;}
  @keyframes pulseFlash{0%{background-color:var(--chalk-bg);} 40%{background-color:#2c5748;} 100%{background-color:var(--chalk-bg);}}

  .hover-read-highlight{outline:3px solid var(--sky); outline-offset:3px; background:rgba(143,193,217,.18); transition:outline-color .15s;}

  /* Reposiciona o boneco de Libras (VLibras) para não colidir com o botão do Bit */
  div[vw]{z-index:140 !important;}
  div[vw].enabled div[vw-access-button]{ left:22px !important; right:auto !important; bottom:22px !important; top:auto !important; }

  /* ---------- Login e administração ---------- */
  .login-modal{
    position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); z-index:230;
    background:var(--paper); color:var(--ink); border-radius:var(--radius); box-shadow:var(--shadow-paper);
    width:min(380px, 92vw); padding:28px 26px; position:fixed;
  }
  .login-modal[hidden]{display:none;}
  .login-modal .close-x{position:absolute; top:14px; right:14px; background:none; border:none; color:var(--ink-soft); font-size:1.2rem; cursor:pointer;}
  .login-tabs{display:flex; gap:6px; background:var(--paper-2); border-radius:999px; padding:4px; margin:6px 0 20px;}
  .login-tabs button{flex:1; border:none; background:transparent; padding:9px; border-radius:999px; font-family:var(--font-mono); font-size:.75rem; text-transform:uppercase; letter-spacing:.03em; cursor:pointer; color:var(--ink-soft);}
  .login-tabs button.active{background:var(--amber); color:var(--ink); font-weight:600;}
  .login-form[hidden]{display:none;}
  .login-form label{display:block; font-family:var(--font-mono); font-size:.76rem; text-transform:uppercase; letter-spacing:.06em; color:var(--ink-soft); margin:12px 0 6px;}
  .login-form label:first-of-type{margin-top:0;}
  .login-form input{width:100%; padding:11px 12px; border-radius:8px; border:1.5px solid var(--paper-line); font-family:var(--font-body); font-size:.95rem; background:#fffdf7; color:var(--ink);}
  .login-form .submit{width:100%; justify-content:center; margin-top:16px;}
  .form-msg{font-family:var(--font-mono); font-size:.78rem; margin-top:10px; color:var(--red-pen);}
  .login-note{font-size:.76rem; color:var(--ink-soft); margin-top:10px;}

  #admin{background:var(--chalk-bg-2); border-top:1px solid rgba(244,241,230,.1);}
  .admin-table-wrap{background:var(--paper); border-radius:var(--radius); box-shadow:var(--shadow-paper); padding:10px; overflow-x:auto;}
  table.admin-table{border-collapse:collapse; width:100%; color:var(--ink); min-width:560px;}
  table.admin-table th, table.admin-table td{border-bottom:1px solid var(--paper-line); padding:12px 14px; text-align:left; font-size:.88rem;}
  table.admin-table thead th{font-family:var(--font-mono); font-size:.7rem; text-transform:uppercase; letter-spacing:.05em; color:var(--ink-soft); background:var(--paper-2);}
  .status-pill{font-family:var(--font-mono); font-size:.68rem; padding:4px 10px; border-radius:999px; text-transform:uppercase;}
  .status-pill.liberada{background:#e2f0d9; color:#4c7635;}
  .status-pill.pendente{background:#fff1cf; color:#8a6410;}
  .status-pill.bloqueada{background:#fde3dd; color:#a8402f;}
  .admin-table button{font-family:var(--font-body); font-size:.78rem; font-weight:600; padding:7px 12px; border-radius:8px; border:1.5px solid var(--paper-line); background:#fff; cursor:pointer; margin-right:6px;}
  .admin-table button.liberar{border-color:var(--green-check); color:#4c7635;}
  .admin-table button.bloquear{border-color:var(--red-pen); color:#a8402f;}
  .admin-note{font-family:var(--font-mono); font-size:.78rem; color:#c9c3ae; margin-top:16px;}

  /* ---------- High contrast mode ---------- */
  body.contrast{--chalk-bg:#000000; --chalk-bg-2:#000000; --chalk-bg-3:#0d0d0d; --paper:#ffffff; --paper-2:#f0f0f0; --paper-line:#000000; --ink:#000000; --ink-soft:#000000; --chalk-white:#ffffff; --amber:#ffd23f; --sky:#7fdcff; --red-pen:#ff5a4a; --green-check:#5fe35f;}
  body.contrast{background-image:none;}
  body.contrast table.grade td.cell input:focus{outline:3px solid #000;}

  @media (max-width: 900px){
    .hero-inner, #acessibilidade .a11y-grid, .checkout-panel{grid-template-columns:1fr;}
    .cart-grid{grid-template-columns:1fr;}
    nav.main-links{position:fixed; top:64px; left:0; right:0; background:var(--chalk-bg-2); flex-direction:column; padding:18px 24px; gap:16px; display:none;}
    nav.main-links.open{display:flex;}
    .burger{display:inline-block;}
    .hero-card{transform:none;}
  }
</style>
</head>
<body>
<a href="#conteudo" class="skip-link">Pular para o conteúdo</a>

<!-- Boneco de Libras (VLibras) -->
<div vw class="enabled">
  <div vw-access-button class="active"></div>
  <div vw-plugin-wrapper>
    <div class="vw-plugin-top-wrapper"></div>
  </div>
</div>

<header class="site">
  <div class="wrap nav-inner">
    <a href="#inicio" class="logo" aria-label="MatrixEdu, página inicial">
      <span class="logo-grid" aria-hidden="true">
        <span class="on">M</span><span>A</span><span>T</span><span>R</span><span>I</span><span class="on">X</span>
      </span>
      <span class="logo-edu">edu</span>
    </a>
    <button class="burger" id="burgerBtn" aria-expanded="false" aria-controls="mainNav">☰ Menu</button>
    <button id="authBtn" class="btn btn-ghost" type="button">Entrar</button>
    <nav class="main-links" id="mainNav">
      <a href="#grade">Grade de horários</a>
      <a href="#carrinhos">Carrinhos de notebooks</a>
      <a href="#acessibilidade">Acessibilidade</a>
      <a href="#admin" id="navAdminLink" hidden>Admin · Licenças</a>
    </nav>
  </div>
</header>

<main id="conteudo">
  <section class="hero" id="inicio">
    <div class="wrap hero-inner">
      <div>
        <p class="eyebrow">Distribuição de aulas &amp; equipamentos</p>
        <h1>A grade da escola,<br><span class="script">organizada em uma matriz.</span></h1>
        <p class="lede">Monte a grade de horários de cada sala, controle a retirada e devolução dos carrinhos de notebooks e conte com um assistente de acessibilidade sempre à mão.</p>
        <div class="hero-cta">
          <a href="#grade" class="btn btn-primary">Ver grade de horários</a>
          <a href="#carrinhos" class="btn btn-ghost">Retirar ou devolver carrinho</a>
        </div>
      </div>
      <div class="hero-card">
        <h2>Acesso rápido</h2>
        <ul class="quick-list">
          <li><a href="#grade">Grade — Manhã (6º ao 9º ano) <span class="num">07h–14h</span></a></li>
          <li><a href="#grade">Grade — Tarde (1ª à 3ª série) <span class="num">14h–21h</span></a></li>
          <li><a href="#carrinhos">Carrinhos Lenovo · Positivo · Tablets <span class="num">86 un.</span></a></li>
          <li><a href="#acessibilidade">Assistente de acessibilidade <span class="num">Bit</span></a></li>
        </ul>
      </div>
    </div>
  </section>

  <section id="grade">
    <div class="wrap">
      <div class="section-head">
        <p class="eyebrow">Distribuição de aulas</p>
        <h2>Grade de horários por sala</h2>
        <p>Clique em qualquer célula para lançar a disciplina daquele horário e escolha o dia da semana. As alterações ficam salvas neste navegador.</p>
      </div>

      <div class="bell-card">
        <div class="bell-status">
          <span class="bell-dot" id="bellDot" aria-hidden="true"></span>
          <div>
            <strong id="bellStatusText">Carregando status da aula…</strong>
            <div class="bell-sub" id="bellSubText"></div>
          </div>
        </div>
        <button type="button" class="btn btn-ghost" id="bellToggleBtn">🔔 Ativar notificações de aula</button>
      </div>

      <div class="shift-tabs" role="tablist" aria-label="Selecionar turno">
        <button role="tab" id="tabManha" aria-selected="true" aria-controls="painelManha" class="active" data-shift="manha">Manhã · 6º ao 9º ano</button>
        <button role="tab" id="tabTarde" aria-selected="false" aria-controls="painelTarde" data-shift="tarde">Tarde · 1ª à 3ª série</button>
      </div>

      <div id="painelManha" role="tabpanel" aria-labelledby="tabManha">
        <p class="shift-meta">Turno da manhã · 07h00 às 14h00 · salas 6A, 6B, 7A, 7B, 8A, 8B, 9A, 9B</p>
        <div class="day-tabs" role="tablist" aria-label="Dia da semana, turno da manhã" data-shift="manha">
          <button type="button" data-day="0" class="active">Segunda</button>
          <button type="button" data-day="1">Terça</button>
          <button type="button" data-day="2">Quarta</button>
          <button type="button" data-day="3">Quinta</button>
          <button type="button" data-day="4">Sexta</button>
        </div>
        <div class="grade-scroll"><div id="gradeManha"></div></div>
      </div>
      <div id="painelTarde" role="tabpanel" aria-labelledby="tabTarde" hidden>
        <p class="shift-meta">Turno da tarde · 14h00 às 21h00 · salas 1A, 1B, 2A, 2B, 3A, 3B</p>
        <div class="day-tabs" role="tablist" aria-label="Dia da semana, turno da tarde" data-shift="tarde">
          <button type="button" data-day="0" class="active">Segunda</button>
          <button type="button" data-day="1">Terça</button>
          <button type="button" data-day="2">Quarta</button>
          <button type="button" data-day="3">Quinta</button>
          <button type="button" data-day="4">Sexta</button>
        </div>
        <div class="grade-scroll"><div id="gradeTarde"></div></div>
      </div>
      <p class="grade-note">Dica: use Tab para navegar entre as células com o teclado.</p>
    </div>
  </section>

  <section id="carrinhos">
    <div class="wrap">
      <div class="section-head">
        <p class="eyebrow">Distribuição de notebooks</p>
        <h2>Carrinhos de notebooks e tablets</h2>
        <p>Três carrinhos circulam pela escola. Registre cada retirada e devolução para saber sempre quantos equipamentos estão disponíveis. Durante o intervalo, o sistema bloqueia novas retiradas automaticamente.</p>
      </div>

      <div class="cart-grid" id="cartGrid"></div>

      <div class="checkout-panel">
        <form class="checkout-form" id="checkoutForm">
          <h3>Registrar movimentação</h3>
          <div class="lock-banner" id="lockBanner" hidden></div>
          <div class="manual-lock-row">
            <div><div class="lbl">🔒 Bloqueio manual</div><div class="desc">A equipe pode travar as retiradas mesmo fora do intervalo.</div></div>
            <label class="switch"><input type="checkbox" id="manualLockToggle"><span class="slider-tg"></span></label>
          </div>
          <label for="fCarrinho">Carrinho</label>
          <select id="fCarrinho" required>
            <option value="1">Carrinho Lenovo (27 notebooks)</option>
            <option value="2">Carrinho Positivo (30 notebooks)</option>
            <option value="3">Carrinho Tablets (29 tablets)</option>
          </select>

          <label for="fSala">Sala solicitante</label>
          <select id="fSala" required>
            <optgroup label="Manhã"></optgroup>
            <optgroup label="Tarde"></optgroup>
          </select>

          <label for="fProf">Nome do professor(a)</label>
          <input type="text" id="fProf" placeholder="Nome de quem está retirando" autocomplete="name" aria-describedby="formMsg">

          <label for="fQtd">Quantidade de equipamentos</label>
          <input type="number" id="fQtd" min="1" value="1" required>

          <label id="actionLabel">Ação</label>
          <div class="action-toggle" role="radiogroup" aria-labelledby="actionLabel">
            <label class="checked"><input type="radio" name="acao" value="retirada" checked> Retirada</label>
            <label><input type="radio" name="acao" value="devolucao"> Devolução</label>
          </div>

          <button type="submit" class="btn btn-primary submit">Carimbar registro</button>
          <p id="formMsg" role="status" style="font-family:var(--font-mono); font-size:.8rem; margin-top:10px; color:var(--red-pen);"></p>
        </form>

        <div class="log-col">
          <h4>Movimentações recentes</h4>
          <ul class="log-list" id="logList"><li class="log-empty">Nenhuma movimentação registrada ainda.</li></ul>
        </div>
      </div>
    </div>
  </section>

  <section id="acessibilidade">
    <div class="wrap a11y-grid">
      <div class="bit-portrait">
        <svg width="180" height="180" viewBox="0 0 180 180" aria-hidden="true">
          <rect x="10" y="10" width="160" height="160" rx="18" fill="var(--chalk-bg-3)" stroke="var(--sky)" stroke-width="2"/>
          <g>
            <rect x="34" y="34" width="34" height="34" rx="4" fill="var(--chalk-bg)" stroke="var(--sky)" stroke-width="2"/>
            <rect x="72" y="34" width="34" height="34" rx="4" fill="var(--amber)"/>
            <rect x="110" y="34" width="34" height="34" rx="4" fill="var(--chalk-bg)" stroke="var(--sky)" stroke-width="2"/>
            <rect x="34" y="72" width="34" height="34" rx="4" fill="var(--amber)"/>
            <rect x="72" y="72" width="34" height="34" rx="4" fill="var(--chalk-bg)" stroke="var(--sky)" stroke-width="2"/>
            <rect x="110" y="72" width="34" height="34" rx="4" fill="var(--amber)"/>
            <rect x="34" y="110" width="34" height="34" rx="4" fill="var(--chalk-bg)" stroke="var(--sky)" stroke-width="2"/>
            <rect x="72" y="110" width="34" height="34" rx="4" fill="var(--chalk-bg)" stroke="var(--sky)" stroke-width="2"/>
            <rect x="110" y="110" width="34" height="34" rx="4" fill="var(--chalk-bg)" stroke="var(--sky)" stroke-width="2"/>
          </g>
          <circle cx="83" cy="49" r="4" fill="var(--ink)"/>
          <circle cx="127" cy="49" r="4" fill="var(--ink)"/>
          <path d="M76 58 q7 6 14 0" stroke="var(--ink)" stroke-width="2.5" fill="none" stroke-linecap="round"/>
          <rect x="150" y="120" width="6" height="46" rx="3" fill="#d9a441" transform="rotate(30 150 120)"/>
        </svg>
        <p style="font-family:var(--font-display); font-size:1.6rem; color:var(--amber); margin:6px 0 0;">Esse é o Bit</p>
        <p style="font-size:.88rem; color:#c9c3ae; margin:0;">seu assistente de acessibilidade</p>
      </div>

      <div>
        <p class="eyebrow">Assistente de acessibilidade</p>
        <h2>Ajuda para navegar, ouvir e enxergar o site</h2>
        <p>O Bit fica sempre disponível no canto da tela para quem precisa de apoio visual ou auditivo. Toque no botão flutuante em qualquer página para abrir o painel de acessibilidade.</p>
        <ul class="a11y-list">
          <li><span class="ico" aria-hidden="true">A+</span><div><strong>Texto ampliado</strong><br>Aumenta a fonte de todo o site com um toque.</div></li>
          <li><span class="ico" aria-hidden="true">◐</span><div><strong>Alto contraste</strong><br>Cores fortes e bordas nítidas para quem tem baixa visão.</div></li>
          <li><span class="ico" aria-hidden="true">▶</span><div><strong>Leitura em voz alta</strong><br>O Bit lê o conteúdo da página para usuários com deficiência visual.</div></li>
          <li><span class="ico" aria-hidden="true">✓</span><div><strong>Confirmação visual</strong><br>Toda retirada e devolução de carrinho mostra um carimbo e um texto na tela — nada depende só de som, para atender usuários com deficiência auditiva.</div></li>
          <li><span class="ico" aria-hidden="true">🤟</span><div><strong>Boneco de Libras</strong><br>Um intérprete virtual (VLibras) traduz o texto da página para Língua Brasileira de Sinais. O botão fica fixo no canto da tela.</div></li>
        </ul>
      </div>
    </div>
  </section>

  <section id="admin" hidden>
    <div class="wrap">
      <div class="section-head">
        <p class="eyebrow">Restrito à administração</p>
        <h2>Central de licenças</h2>
        <p>Libere ou bloqueie o acesso de professores(as) ao sistema de retirada de carrinhos de notebooks.</p>
      </div>
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead><tr><th>Nome</th><th>E-mail</th><th>Status</th><th>Ação</th></tr></thead>
          <tbody id="adminTableBody"></tbody>
        </table>
      </div>
      <p class="admin-note">Contas novas entram como "pendente" ao serem criadas em "Criar conta" na área de login, até que você libere a licença aqui.</p>
    </div>
  </section>
</main>

<footer>
  <div class="wrap">
    <span>MatrixEdu · sistema interno de distribuição de aulas e equipamentos</span>
    <span>Feito para escolas que organizam tudo em grade.</span>
  </div>
</footer>

<!-- Mascote flutuante -->
<div id="bit-bubble" hidden>Oi! Eu sou o Bit 👋 Clique aqui se precisar de ajuda para navegar no site.</div>
<button id="bit-launcher" aria-haspopup="dialog" aria-controls="a11yPanel" aria-label="Abrir assistente de acessibilidade">
  <svg width="34" height="34" viewBox="0 0 180 180" aria-hidden="true">
    <g>
      <rect x="34" y="34" width="34" height="34" rx="4" fill="var(--chalk-bg)"/>
      <rect x="72" y="34" width="34" height="34" rx="4" fill="var(--amber)"/>
      <rect x="110" y="34" width="34" height="34" rx="4" fill="var(--chalk-bg)"/>
      <rect x="34" y="72" width="34" height="34" rx="4" fill="var(--amber)"/>
      <rect x="72" y="72" width="34" height="34" rx="4" fill="var(--chalk-bg)"/>
      <rect x="110" y="72" width="34" height="34" rx="4" fill="var(--amber)"/>
      <rect x="34" y="110" width="34" height="34" rx="4" fill="var(--chalk-bg)"/>
      <rect x="72" y="110" width="34" height="34" rx="4" fill="var(--chalk-bg)"/>
      <rect x="110" y="110" width="34" height="34" rx="4" fill="var(--chalk-bg)"/>
    </g>
  </svg>
</button>

<div class="overlay-bg" id="overlayBg"></div>
<aside class="a11y-panel" id="a11yPanel" role="dialog" aria-modal="true" aria-labelledby="a11yTitle">
  <button class="close-x" id="closeA11y" aria-label="Fechar painel de acessibilidade">✕</button>
  <h3 id="a11yTitle">Central de acessibilidade</h3>
  <p style="font-size:.88rem; color:#c9c3ae;">Oi, eu sou o Bit! Ative o que ajudar você a usar o MatrixEdu com mais conforto.</p>

  <div class="a11y-row">
    <div><div class="lbl">Aumentar texto</div><div class="desc">Deixa as letras de todo o site maiores.</div></div>
    <label class="switch"><input type="checkbox" id="toggleFont"><span class="slider-tg"></span></label>
  </div>
  <div class="a11y-row">
    <div><div class="lbl">Alto contraste</div><div class="desc">Preto, branco e amarelo para leitura mais nítida.</div></div>
    <label class="switch"><input type="checkbox" id="toggleContrast"><span class="slider-tg"></span></label>
  </div>
  <div class="a11y-row">
    <div><div class="lbl">Reduzir animações</div><div class="desc">Remove o efeito do carimbo e outras transições.</div></div>
    <label class="switch"><input type="checkbox" id="toggleMotion"><span class="slider-tg"></span></label>
  </div>
  <div class="a11y-row">
    <div><div class="lbl">Leitura ao passar o mouse</div><div class="desc">Com a leitura ativa, o Bit lê em voz alta o texto por onde o cursor passa.</div></div>
    <label class="switch"><input type="checkbox" id="toggleHoverRead"><span class="slider-tg"></span></label>
  </div>

  <button class="btn btn-primary speak-btn" id="speakBtn">▶ Ouvir esta página</button>
  <button class="btn btn-ghost speak-btn" id="stopSpeakBtn" style="display:none;">■ Parar leitura</button>

  <p class="a11y-note">Feito para deficiência visual e auditiva: leitura em voz alta, leitura ao passar o mouse e o boneco de Libras ajudam quem enxerga ou ouve menos; carimbos e confirmações sempre escritas na tela garantem que quem não ouve o "clique" também veja que a ação funcionou.</p>
</aside>

<div class="stamp-overlay"><div class="stamp" id="stampEl">Retirado</div></div>

<div class="overlay-bg" id="authOverlay"></div>
<aside class="login-modal" id="loginModal" role="dialog" aria-modal="true" aria-labelledby="loginTitle" hidden>
  <button class="close-x" id="closeLogin" aria-label="Fechar login">✕</button>
  <div class="login-tabs">
    <button type="button" class="active" data-tab="entrar">Entrar</button>
    <button type="button" data-tab="cadastro">Criar conta</button>
  </div>

  <form id="loginForm" class="login-form">
    <h3 id="loginTitle" style="font-size:1.25rem;">Entrar no MatrixEdu</h3>
    <label for="loginUser">E-mail</label>
    <input type="email" id="loginUser" autocomplete="email" required>
    <label for="loginPass">Senha</label>
    <input type="password" id="loginPass" autocomplete="current-password" required>
    <button type="submit" class="btn btn-primary submit">Entrar</button>
    <p id="loginMsg" role="status" class="form-msg"></p>
  </form>

  <form id="signupForm" class="login-form" hidden>
    <h3 style="font-size:1.25rem;">Criar conta de professor(a)</h3>
    <label for="suNome">Nome completo</label>
    <input type="text" id="suNome" autocomplete="name" required>
    <label for="suUser">E-mail</label>
    <input type="email" id="suUser" autocomplete="email" required>
    <label for="suPass">Senha</label>
    <input type="password" id="suPass" autocomplete="new-password" minlength="4" required>
    <button type="submit" class="btn btn-primary submit">Criar conta</button>
    <p id="signupMsg" role="status" class="form-msg"></p>
    <p class="login-note">Sua conta fica pendente até um(a) administrador(a) liberar a licença.</p>
  </form>
</aside>

<script>
(function(){
  "use strict";

  /* ---------- Nav mobile ---------- */
  var burger = document.getElementById('burgerBtn');
  var mainNav = document.getElementById('mainNav');
  burger.addEventListener('click', function(){
    var open = mainNav.classList.toggle('open');
    burger.setAttribute('aria-expanded', open ? 'true' : 'false');
  });
  mainNav.querySelectorAll('a').forEach(function(a){
    a.addEventListener('click', function(){ mainNav.classList.remove('open'); });
  });

  /* ---------- Dados da grade ---------- */
  var salasManha = ['6A','6B','7A','7B','8A','8B','9A','9B'];
  var salasTarde = ['1A','1B','2A','2B','3A','3B'];

  var diasSemana = ['Segunda','Terça','Quarta','Quinta','Sexta'];

  var periodosManha = [
    {t:'07:00–07:50', ini:420, fim:470}, {t:'07:50–08:40', ini:470, fim:520}, {t:'08:40–09:30', ini:520, fim:570}, {t:'09:30–10:20', ini:570, fim:620},
    {t:'10:20–10:40', ini:620, fim:640, intervalo:true},
    {t:'10:40–11:30', ini:640, fim:690}, {t:'11:30–12:20', ini:690, fim:740}, {t:'12:20–13:10', ini:740, fim:790}, {t:'13:10–14:00', ini:790, fim:840}
  ];
  var periodosTarde = [
    {t:'14:00–14:50', ini:840, fim:890}, {t:'14:50–15:40', ini:890, fim:940}, {t:'15:40–16:30', ini:940, fim:990}, {t:'16:30–17:20', ini:990, fim:1040},
    {t:'17:20–17:40', ini:1040, fim:1060, intervalo:true},
    {t:'17:40–18:30', ini:1060, fim:1110}, {t:'18:30–19:20', ini:1110, fim:1160}, {t:'19:20–20:10', ini:1160, fim:1210}, {t:'20:10–21:00', ini:1210, fim:1260}
  ];

  function pad2(n){ return n.toString().padStart(2,'0'); }
  function minutesToStr(m){ return pad2(Math.floor(m/60)) + ':' + pad2(m%60); }

  var GRADE_KEY = 'matrixedu_grade_v1';
  var gradeData = {};
  try{ gradeData = JSON.parse(localStorage.getItem(GRADE_KEY)) || {}; }catch(e){ gradeData = {}; }

  function buildGrade(containerId, shiftKey, salas, periodos, dayIdx){
    var el = document.getElementById(containerId);
    var html = '<table class="grade"><thead><tr><th>Horário</th>';
    salas.forEach(function(s){ html += '<th>' + s + '</th>'; });
    html += '</tr></thead><tbody>';
    periodos.forEach(function(p, i){
      if(p.intervalo){
        html += '<tr class="interval"><td class="time-cell">' + p.t + '</td><td colspan="' + salas.length + '">Intervalo</td></tr>';
      } else {
        html += '<tr><td class="time-cell">' + p.t + '</td>';
        salas.forEach(function(sala){
          var key = shiftKey + '_' + dayIdx + '_' + i + '_' + sala;
          var val = gradeData[key] ? gradeData[key].replace(/"/g,'&quot;') : '';
          html += '<td class="cell"><input type="text" placeholder="Disciplina" value="' + val + '" data-key="' + key + '" aria-label="Disciplina, sala ' + sala + ', ' + diasSemana[dayIdx] + ', ' + p.t + '"></td>';
        });
        html += '</tr>';
      }
    });
    html += '</tbody></table>';
    el.innerHTML = html;
    el.querySelectorAll('input[data-key]').forEach(function(inp){
      inp.addEventListener('input', function(){
        gradeData[inp.dataset.key] = inp.value;
        localStorage.setItem(GRADE_KEY, JSON.stringify(gradeData));
      });
    });
  }

  var currentDay = {manha:0, tarde:0};
  (function(){
    var idxHoje = new Date().getDay() - 1; // 0 Seg ... 4 Sex
    if(idxHoje >= 0 && idxHoje <= 4){ currentDay.manha = idxHoje; currentDay.tarde = idxHoje; }
  })();

  function renderGradeManha(){ buildGrade('gradeManha', 'manha', salasManha, periodosManha, currentDay.manha); }
  function renderGradeTarde(){ buildGrade('gradeTarde', 'tarde', salasTarde, periodosTarde, currentDay.tarde); }
  renderGradeManha();
  renderGradeTarde();

  document.querySelectorAll('.day-tabs').forEach(function(tabs){
    var shift = tabs.dataset.shift;
    var buttons = tabs.querySelectorAll('button');
    buttons.forEach(function(b){ b.classList.toggle('active', parseInt(b.dataset.day, 10) === currentDay[shift]); });
    buttons.forEach(function(b){
      b.addEventListener('click', function(){
        currentDay[shift] = parseInt(b.dataset.day, 10);
        buttons.forEach(function(x){ x.classList.remove('active'); });
        b.classList.add('active');
        if(shift === 'manha') renderGradeManha(); else renderGradeTarde();
      });
    });
  });

  var tabManha = document.getElementById('tabManha');
  var tabTarde = document.getElementById('tabTarde');
  var painelManha = document.getElementById('painelManha');
  var painelTarde = document.getElementById('painelTarde');
  function selectShift(shift){
    var isManha = shift === 'manha';
    tabManha.classList.toggle('active', isManha);
    tabTarde.classList.toggle('active', !isManha);
    tabManha.setAttribute('aria-selected', isManha);
    tabTarde.setAttribute('aria-selected', !isManha);
    painelManha.hidden = !isManha;
    painelTarde.hidden = isManha;
  }
  tabManha.addEventListener('click', function(){ selectShift('manha'); });
  tabTarde.addEventListener('click', function(){ selectShift('tarde'); });

  /* ---------- Carrinhos / MySQL ---------- */
  var carts = {};
  var log = [];
  var cartGrid = document.getElementById('cartGrid');
  var fSala = document.getElementById('fSala');
  var ogManha = fSala.querySelector('optgroup[label="Manhã"]');
  var ogTarde = fSala.querySelector('optgroup[label="Tarde"]');
  salasManha.forEach(function(s){ var o = document.createElement('option'); o.value = s; o.textContent = 'Sala ' + s; ogManha.appendChild(o); });
  salasTarde.forEach(function(s){ var o = document.createElement('option'); o.value = s; o.textContent = 'Sala ' + s; ogTarde.appendChild(o); });

  var checkoutForm = document.getElementById('checkoutForm');
  var formMsg = document.getElementById('formMsg');
  var actionLabels = document.querySelectorAll('.action-toggle label');
  var fProf = document.getElementById('fProf');

  function updateProfRequirement(){
    var acao = document.querySelector('input[name="acao"]:checked').value;
    fProf.required = acao === 'retirada';
    fProf.placeholder = acao === 'retirada' ? 'Nome de quem está retirando' : 'Nome de quem está devolvendo (opcional)';
  }
  checkoutForm.querySelectorAll('input[name="acao"]').forEach(function(r){ r.addEventListener('change', updateProfRequirement); });
  updateProfRequirement();
  actionLabels.forEach(function(lbl){
    lbl.addEventListener('click', function(){ actionLabels.forEach(function(l){ l.classList.remove('checked'); }); lbl.classList.add('checked'); });
  });

  function loadData(){
    fetch('buscar.php?action=dashboard', {credentials:'same-origin'})
      .then(function(r){ return r.json(); })
      .then(function(data){
        if(!data.ok) throw new Error(data.message || 'Erro ao carregar dados.');
        carts = {}; data.carts.forEach(function(c){ carts[c.id] = c; });
        log = data.log || [];
        renderCarts(); renderLog();
        if(data.user){ session = {user:data.user.email}; currentUser = data.user; }
        else { session = null; currentUser = null; }
        updateAuthUI();
      })
      .catch(function(err){ console.error(err); formMsg.textContent = 'Não foi possível carregar os dados do servidor.'; });
  }

  function renderCarts(){
    var html = '';
    Object.keys(carts).forEach(function(key){
      var c = carts[key];
      var disponiveis = Number(c.total) - Number(c.em_uso);
      var pct = Math.round((disponiveis / Number(c.total)) * 100);
      html += '<div class="cart-card"><span class="tag">' + c.total + ' equipamentos</span><h3>' + c.nome + '</h3>' +
        '<div class="gauge"><div class="gauge-fill" style="width:' + pct + '%"></div></div>' +
        '<div class="cart-stats"><span><strong>' + disponiveis + '</strong> disponíveis</span><span><strong>' + c.em_uso + '</strong> em uso</span></div></div>';
    });
    cartGrid.innerHTML = html;
  }

  var logList = document.getElementById('logList');
  function renderLog(){
    if(log.length === 0){ logList.innerHTML = '<li class="log-empty">Nenhuma movimentação registrada ainda.</li>'; return; }
    var html = '';
    log.slice(0, 30).forEach(function(entry){
      var profTxt = entry.professor && entry.professor !== '—' ? entry.professor + ' · ' : '';
      html += '<li><span><span class="who">Sala ' + entry.sala + '</span> · ' + profTxt + entry.carrinho + ' (' + entry.qtd + ') · ' + entry.hora + '</span>' +
        '<span class="badge ' + (entry.acao === 'retirada' ? 'out' : 'in') + '">' + (entry.acao === 'retirada' ? 'Retirada' : 'Devolução') + '</span></li>';
    });
    logList.innerHTML = html;
  }

  var stampEl = document.getElementById('stampEl');
  function showStamp(text, isOut){
    stampEl.textContent = text;
    stampEl.classList.remove('out-c','in-c','show'); void stampEl.offsetWidth;
    stampEl.classList.add(isOut ? 'out-c' : 'in-c'); stampEl.classList.add('show');
    document.body.classList.remove('flash-pulse'); void document.body.offsetWidth; document.body.classList.add('flash-pulse');
  }

  /* ---------- Status da aula, notificações e bloqueio no intervalo ---------- */
  function getScheduleStatus(d){
    var diaIdx = d.getDay() - 1; // 0 Seg ... 4 Sex
    if(diaIdx < 0 || diaIdx > 4){
      return {tipo:'fora', texto:'Sem aulas hoje — fim de semana.'};
    }
    var min = d.getHours() * 60 + d.getMinutes();
    var todos = periodosManha.concat(periodosTarde);
    for(var i = 0; i < todos.length; i++){
      var p = todos[i];
      if(min >= p.ini && min < p.fim){
        if(p.intervalo){
          return {tipo:'intervalo', texto:'Intervalo em ' + diasSemana[diaIdx] + ' — retiradas liberam às ' + minutesToStr(p.fim) + '.', fimMin:p.fim};
        }
        return {tipo:'aula', texto:'Aula em andamento (' + p.t + ') — ' + diasSemana[diaIdx] + '.', fimMin:p.fim};
      }
    }
    if(min < 420) return {tipo:'fora', texto:'As aulas começam às 07:00.'};
    if(min >= 1260) return {tipo:'fora', texto:'Aulas encerradas por hoje.'};
    return {tipo:'fora', texto:'Fora do horário de aula.'};
  }

  var bellDot = document.getElementById('bellDot');
  var bellStatusText = document.getElementById('bellStatusText');
  var bellSubText = document.getElementById('bellSubText');
  var bellToggleBtn = document.getElementById('bellToggleBtn');
  var NOTIFY_KEY = 'matrixedu_notify_v1';
  var notifyEnabled = false;
  try{ notifyEnabled = localStorage.getItem(NOTIFY_KEY) === '1'; }catch(e){ notifyEnabled = false; }

  function updateBellBtn(){
    if(!('Notification' in window)){ bellToggleBtn.textContent = 'Notificações indisponíveis neste navegador'; bellToggleBtn.disabled = true; return; }
    bellToggleBtn.textContent = notifyEnabled ? '🔔 Notificações de aula ativas' : '🔕 Ativar notificações de aula';
  }
  updateBellBtn();
  bellToggleBtn.addEventListener('click', function(){
    if(!('Notification' in window)) return;
    if(Notification.permission === 'granted'){
      notifyEnabled = !notifyEnabled;
      localStorage.setItem(NOTIFY_KEY, notifyEnabled ? '1' : '0');
      updateBellBtn();
    } else if(Notification.permission !== 'denied') {
      Notification.requestPermission().then(function(perm){
        notifyEnabled = perm === 'granted';
        localStorage.setItem(NOTIFY_KEY, notifyEnabled ? '1' : '0');
        updateBellBtn();
      });
    } else {
      formMsgIndependent('As notificações foram bloqueadas nas configurações do navegador.');
    }
  });
  function formMsgIndependent(t){ bellSubText.textContent = t; }

  var lockBanner = document.getElementById('lockBanner');
  var manualLockToggle = document.getElementById('manualLockToggle');
  var MANUAL_LOCK_KEY = 'matrixedu_manual_lock_v1';
  var manualLock = false;
  try{ manualLock = localStorage.getItem(MANUAL_LOCK_KEY) === '1'; }catch(e){ manualLock = false; }
  manualLockToggle.checked = manualLock;
  manualLockToggle.addEventListener('change', function(){
    manualLock = manualLockToggle.checked;
    localStorage.setItem(MANUAL_LOCK_KEY, manualLock ? '1' : '0');
    refreshStatus();
  });

  var isLockedNow = false;
  var lastStatusKey = null;
  function refreshStatus(){
    var now = new Date();
    var st = getScheduleStatus(now);

    bellDot.className = 'bell-dot ' + st.tipo;
    bellStatusText.textContent = st.tipo === 'aula' ? 'Aula em andamento' : (st.tipo === 'intervalo' ? 'Intervalo — bloqueio ativo' : 'Fora do horário de aula');
    bellSubText.textContent = st.texto;

    var key = st.tipo + '_' + (st.fimMin || 0);
    if(lastStatusKey !== null && key !== lastStatusKey && notifyEnabled && 'Notification' in window && Notification.permission === 'granted'){
      new Notification('MatrixEdu — Sino digital', {body: st.texto});
    }
    lastStatusKey = key;

    isLockedNow = manualLock || st.tipo === 'intervalo';
    var campos = checkoutForm.querySelectorAll('select, input, button[type=submit]');
    campos.forEach(function(f){ f.disabled = isLockedNow; });
    if(isLockedNow){
      lockBanner.hidden = false;
      lockBanner.textContent = manualLock
        ? '🔒 Retiradas bloqueadas manualmente pela equipe da escola.'
        : '🔒 Bloqueado durante o intervalo — retiradas liberam às ' + minutesToStr(st.fimMin) + '.';
    } else {
      lockBanner.hidden = true;
    }
  }
  refreshStatus();
  setInterval(refreshStatus, 15000);

  checkoutForm.addEventListener('submit', function(e){
    e.preventDefault();
    formMsg.textContent = '';
    if(isLockedNow){ formMsg.textContent = 'As retiradas estão bloqueadas no momento.'; return; }
    var carrinhoId = parseInt(document.getElementById('fCarrinho').value, 10);
    var sala = document.getElementById('fSala').value;
    var qtd = parseInt(document.getElementById('fQtd').value, 10) || 0;
    var acao = document.querySelector('input[name="acao"]:checked').value;
    if(qtd < 1){ formMsg.textContent = 'Informe uma quantidade válida.'; return; }
    if(acao === 'retirada' && !fProf.value.trim()){ formMsg.textContent = 'Informe o nome do professor responsável pela retirada.'; return; }

    var fd = new FormData();
    fd.append('acao', 'movimentacao');
    fd.append('carrinho_id', carrinhoId);
    fd.append('sala', sala);
    fd.append('professor', fProf.value.trim());
    fd.append('quantidade', qtd);
    fd.append('tipo_movimentacao', acao);

    fetch('salvar.php', {method:'POST', body:fd, credentials:'same-origin'})
      .then(function(r){ return r.json(); })
      .then(function(data){
        if(!data.ok){ formMsg.textContent = data.message || 'Não foi possível salvar.'; return; }
        carts = {}; data.carts.forEach(function(c){ carts[c.id] = c; });
        log = data.log || [];
        renderCarts(); renderLog();
        showStamp(acao === 'retirada' ? 'Retirado ✓' : 'Devolvido ✓', acao === 'retirada');
        checkoutForm.reset(); updateProfRequirement(); reapplyProfPrefill();
        actionLabels.forEach(function(l){ l.classList.remove('checked'); }); actionLabels[0].classList.add('checked');
      })
      .catch(function(){ formMsg.textContent = 'Erro de conexão com o servidor.'; });
  });


  /* ---------- Login e área de administração (MySQL) ---------- */
  var session = null;
  var currentUser = null;
  var authBtn = document.getElementById('authBtn');
  var authOverlay = document.getElementById('authOverlay');
  var loginModal = document.getElementById('loginModal');
  var closeLogin = document.getElementById('closeLogin');
  var loginForm = document.getElementById('loginForm');
  var signupForm = document.getElementById('signupForm');
  var loginMsg = document.getElementById('loginMsg');
  var signupMsg = document.getElementById('signupMsg');
  var navAdminLink = document.getElementById('navAdminLink');
  var adminSection = document.getElementById('admin');

  function reapplyProfPrefill(){
    if(currentUser && currentUser.role === 'professor' && currentUser.licenca === 'liberada') fProf.value = currentUser.nome;
  }
  function openLogin(){ loginModal.hidden = false; authOverlay.classList.add('show'); document.getElementById('loginUser').focus(); }
  function closeLoginModal(){ loginModal.hidden = true; authOverlay.classList.remove('show'); }
  authOverlay.addEventListener('click', closeLoginModal); closeLogin.addEventListener('click', closeLoginModal);
  document.addEventListener('keydown', function(e){ if(e.key === 'Escape' && !loginModal.hidden) closeLoginModal(); });

  authBtn.addEventListener('click', function(){
    if(session){
      fetch('salvar.php', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'acao=logout', credentials:'same-origin'})
        .then(function(){ session=null; currentUser=null; updateAuthUI(); });
    } else openLogin();
  });

  function updateAuthUI(){
    if(session && currentUser){
      authBtn.textContent = 'Sair (' + currentUser.nome.split(' ')[0] + ')';
      var isAdmin = currentUser.role === 'admin'; navAdminLink.hidden = !isAdmin; adminSection.hidden = !isAdmin;
      if(isAdmin) renderAdminTable();
      reapplyProfPrefill();
    } else {
      authBtn.textContent = 'Entrar'; navAdminLink.hidden = true; adminSection.hidden = true;
    }
  }

  loginForm.addEventListener('submit', function(e){
    e.preventDefault(); loginMsg.textContent = '';
    var fd = new URLSearchParams(); fd.append('acao','login'); fd.append('email',document.getElementById('loginUser').value.trim().toLowerCase()); fd.append('senha',document.getElementById('loginPass').value);
    fetch('salvar.php', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:fd.toString(), credentials:'same-origin'})
      .then(function(r){ return r.json(); }).then(function(data){
        if(!data.ok){ loginMsg.textContent=data.message || 'E-mail ou senha incorretos.'; return; }
        session={user:data.user.email}; currentUser=data.user; loginForm.reset(); closeLoginModal(); updateAuthUI(); loadData();
      }).catch(function(){ loginMsg.textContent='Erro de conexão com o servidor.'; });
  });

  signupForm.addEventListener('submit', function(e){
    e.preventDefault(); signupMsg.style.color='var(--red-pen)'; signupMsg.textContent='';
    var nome=document.getElementById('suNome').value.trim(), email=document.getElementById('suUser').value.trim().toLowerCase(), senha=document.getElementById('suPass').value;
    var emailValido=/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    if(!nome || !email || senha.length<4){ signupMsg.textContent='Preencha todos os campos (senha com ao menos 4 caracteres).'; return; }
    if(!emailValido){ signupMsg.textContent='Digite um e-mail válido.'; return; }
    var fd=new URLSearchParams(); fd.append('acao','cadastro'); fd.append('nome',nome); fd.append('email',email); fd.append('senha',senha);
    fetch('salvar.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:fd.toString(),credentials:'same-origin'})
      .then(function(r){return r.json();}).then(function(data){
        if(!data.ok){signupMsg.textContent=data.message||'Não foi possível criar a conta.';return;}
        signupForm.reset(); signupMsg.style.color='var(--green-check)'; signupMsg.textContent='Conta criada! Aguarde a liberação da licença pelo administrador.'; renderAdminTable();
      }).catch(function(){signupMsg.textContent='Erro de conexão com o servidor.';});
  });

  document.querySelectorAll('.login-tabs button').forEach(function(btn){
    btn.addEventListener('click',function(){
      document.querySelectorAll('.login-tabs button').forEach(function(b){b.classList.remove('active');}); btn.classList.add('active');
      var tab=btn.dataset.tab; loginForm.hidden=tab!=='entrar'; signupForm.hidden=tab!=='cadastro'; loginMsg.textContent=''; signupMsg.textContent='';
    });
  });

  function renderAdminTable(){
    var tbody=document.getElementById('adminTableBody');
    fetch('buscar.php?action=users',{credentials:'same-origin'}).then(function(r){return r.json();}).then(function(data){
      if(!data.ok) return;
      var rows=''; data.users.forEach(function(u){
        rows+='<tr><td>'+u.nome+'</td><td>'+u.email+'</td><td><span class="status-pill '+u.licenca+'">'+u.licenca+'</span></td><td>'+
          (u.licenca!=='liberada'?'<button type="button" class="liberar" data-user="'+u.id+'" data-acao="liberar">Liberar licença</button>':'')+
          (u.licenca!=='bloqueada'?'<button type="button" class="bloquear" data-user="'+u.id+'" data-acao="bloquear">Bloquear</button>':'')+'</td></tr>';
      });
      tbody.innerHTML=rows||'<tr><td colspan="4" style="color:var(--ink-soft);">Nenhum professor cadastrado ainda.</td></tr>';
      tbody.querySelectorAll('button').forEach(function(btn){btn.addEventListener('click',function(){
        var fd=new URLSearchParams(); fd.append('acao','licenca'); fd.append('user_id',btn.dataset.user); fd.append('status',btn.dataset.acao==='liberar'?'liberada':'bloqueada');
        fetch('salvar.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:fd.toString(),credentials:'same-origin'}).then(function(r){return r.json();}).then(function(d){if(d.ok){renderAdminTable();loadData();}});
      });});
    });
  }

  loadData();
  /* ---------- Assistente de acessibilidade (Bit) ---------- */

  var launcher = document.getElementById('bit-launcher');
  var bubble = document.getElementById('bit-bubble');
  var panel = document.getElementById('a11yPanel');
  var overlay = document.getElementById('overlayBg');
  var closeA11y = document.getElementById('closeA11y');

  setTimeout(function(){
    bubble.hidden = false;
    setTimeout(function(){ bubble.hidden = true; }, 7000);
  }, 1200);

  function openPanel(){
    bubble.hidden = true;
    panel.classList.add('open');
    overlay.classList.add('show');
    closeA11y.focus();
  }
  function closePanel(){
    panel.classList.remove('open');
    overlay.classList.remove('show');
    launcher.focus();
  }
  launcher.addEventListener('click', openPanel);
  bubble.addEventListener('click', openPanel);
  closeA11y.addEventListener('click', closePanel);
  overlay.addEventListener('click', closePanel);
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape' && panel.classList.contains('open')) closePanel();
  });

  var PREF_KEY = 'matrixedu_prefs_v1';
  var prefs = {};
  try{ prefs = JSON.parse(localStorage.getItem(PREF_KEY)) || {}; }catch(e){ prefs = {}; }
  var toggleFont = document.getElementById('toggleFont');
  var toggleContrast = document.getElementById('toggleContrast');
  var toggleMotion = document.getElementById('toggleMotion');
  var toggleHoverRead = document.getElementById('toggleHoverRead');
  var hoverReadActive = false;

  function applyPrefs(){
    document.body.classList.toggle('font-lg', !!prefs.font);
    document.body.classList.toggle('contrast', !!prefs.contrast);
    document.body.classList.toggle('reduce-motion', !!prefs.motion);
    toggleFont.checked = !!prefs.font;
    toggleContrast.checked = !!prefs.contrast;
    toggleMotion.checked = !!prefs.motion;
    toggleHoverRead.checked = !!prefs.hoverRead;
    hoverReadActive = !!prefs.hoverRead;
  }
  applyPrefs();

  function savePrefs(){ localStorage.setItem(PREF_KEY, JSON.stringify(prefs)); }
  toggleFont.addEventListener('change', function(){ prefs.font = toggleFont.checked; applyPrefs(); savePrefs(); });
  toggleContrast.addEventListener('change', function(){ prefs.contrast = toggleContrast.checked; applyPrefs(); savePrefs(); });
  toggleMotion.addEventListener('change', function(){ prefs.motion = toggleMotion.checked; applyPrefs(); savePrefs(); });
  toggleHoverRead.addEventListener('change', function(){ prefs.hoverRead = toggleHoverRead.checked; applyPrefs(); savePrefs(); });

  var speakBtn = document.getElementById('speakBtn');
  var stopSpeakBtn = document.getElementById('stopSpeakBtn');
  var synth = window.speechSynthesis;
  var speechText = 'Bem-vindo ao MatrixEdu. Este site organiza a grade de horários das salas do sexto ao nono ano no período da manhã, das sete às quatorze horas, e da primeira à terceira série no período da tarde, das quatorze às vinte e uma horas. Você também pode registrar a retirada e a devolução dos carrinhos de notebooks Lenovo, Positivo e dos tablets. Use o botão do Bit, no canto da tela, sempre que precisar de ajuda.';
  if(synth){
    speakBtn.addEventListener('click', function(){
      synth.cancel();
      var u = new SpeechSynthesisUtterance(speechText);
      u.lang = 'pt-BR';
      u.onend = function(){ speakBtn.style.display = 'inline-flex'; stopSpeakBtn.style.display = 'none'; };
      synth.speak(u);
      speakBtn.style.display = 'none';
      stopSpeakBtn.style.display = 'inline-flex';
    });
    stopSpeakBtn.addEventListener('click', function(){
      synth.cancel();
      speakBtn.style.display = 'inline-flex';
      stopSpeakBtn.style.display = 'none';
    });
  } else {
    speakBtn.disabled = true;
    speakBtn.textContent = 'Leitura em voz alta indisponível';
    toggleHoverRead.disabled = true;
  }

  /* ---------- Leitura ao passar o mouse ---------- */
  var HOVER_SELECTOR = 'h1,h2,h3,p,a,button,label,li,td,th,.lbl,.desc,.eyebrow,.badge,input,select,.logo-edu,.script';
  var lastHoverEl = null;
  var hoverTimer = null;

  function getReadableText(el){
    var tag = el.tagName;
    if(tag === 'INPUT'){
      var aria = el.getAttribute('aria-label');
      var val = el.value || el.placeholder || '';
      return (aria ? aria + '. ' : '') + (val ? 'Conteúdo: ' + val : 'Campo vazio.');
    }
    if(tag === 'SELECT'){
      var opt = el.options[el.selectedIndex];
      return (el.getAttribute('aria-label') || 'Seleção') + ': ' + (opt ? opt.textContent : '');
    }
    var t = (el.getAttribute('aria-label') || el.innerText || el.textContent || '').trim();
    return t.length > 300 ? t.slice(0, 300) : t;
  }

  document.addEventListener('mouseover', function(e){
    if(!hoverReadActive || !synth) return;
    if(panel.classList.contains('open') && !panel.contains(e.target)) return;
    var el = e.target.closest(HOVER_SELECTOR);
    if(!el || el === lastHoverEl) return;
    lastHoverEl = el;
    clearTimeout(hoverTimer);
    hoverTimer = setTimeout(function(){
      var text = getReadableText(el);
      if(!text) return;
      synth.cancel();
      var u = new SpeechSynthesisUtterance(text);
      u.lang = 'pt-BR';
      synth.speak(u);
      el.classList.add('hover-read-highlight');
      setTimeout(function(){ el.classList.remove('hover-read-highlight'); }, 1000);
    }, 180);
  });
})();
</script>

<!-- Boneco de Libras (VLibras) -->
<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
<script>
  new window.VLibras.Widget('https://vlibras.gov.br/app');
</script>
</body>
</html>
