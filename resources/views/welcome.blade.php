<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Entre com seu nome</title>
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

  <style>
    /* Tema romântico (rosa) */
    :root {
      --bg:#3d0d1c;      /* fundo da tela */
      --panel:#5e0a2e;   /* fundo do card */
      --text:#ffe4ec;    /* cor do texto */
      --muted:#ffd6e2;   /* textos suaves */
      --accent:#ff8fb2;  /* botão e detalhes */
    }

    *{ box-sizing: border-box; }
    body { 
      display:flex; align-items:center; justify-content:center; 
      min-height:100vh; 
      font-family:system-ui, -apple-system, Segoe UI, Roboto, sans-serif; 
      background: radial-gradient(1200px 600px at 50% -10%, #7a1330 0%, var(--bg) 60%); 
      color:var(--text);
      margin:0;
      overflow:hidden; /* corações sobem “de fora” da tela */
    }

    /* container invisível dos corações */
    .hearts-bg{
      position: fixed; inset: 0; pointer-events: none; z-index: 0; overflow: hidden;
    }

    .card { 
      position: relative; z-index: 2;
      background:var(--panel); 
      padding:2rem; 
      border-radius:1rem; 
      width:clamp(300px, 90vw, 420px); 
      box-shadow:0 10px 30px rgba(0,0,0,.45);
      border:1px solid rgba(255,255,255,.08);
    }

    h1{ margin:0 0 1rem 0; color:var(--text);}
    p{ margin:.2rem 0 1rem 0; color:var(--muted);}

    label{ font-size:.95rem; color:var(--muted);}
    input{ 
      width:100%; margin-top:.5rem; padding:.9rem 1rem; 
      background:#2a0f1d; 
      color:var(--text); 
      border:1px solid #82364d; 
      border-radius:.75rem; outline:none;
    }
    input:focus{ border-color:var(--accent); }

    button{ 
      margin-top:1rem; width:100%; padding:.9rem 1rem; 
      background:var(--accent); color:#fff; 
      border:none; border-radius:.75rem; 
      cursor:pointer; font-weight:600;
      transition: transform .15s ease, filter .2s ease;
    }
    button:hover{ filter: brightness(1.1); transform: translateY(-1px); }

    .error{ margin-top:.75rem; color:#fca5a5; font-size:.9rem;}

    /* animação no CSS (mais confiável que injetar via JS) */
    @keyframes floatUp {
      0%   { transform: translateY(0) translateX(0) rotate(0deg); opacity: 0; }
      10%  { opacity: 1; }
      100% { transform: translateY(-120vh) translateX(var(--drift, 0px)) rotate(12deg); opacity: 0; }
    }
  </style>
</head>
<body>
  <!-- BG de corações flutuando -->
  <div class="hearts-bg" id="hearts-bg"></div>

  <div class="card">
    <h1>Oi, amor 💖</h1>
    <p>Escreva seu nome completo para entrar.</p>

    @if ($errors->any())
      <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('entrar') }}">
      @csrf
      <label>Insira seu nome</label>
      <input name="nome" placeholder="Ex: Maria Hélen Lima Carlos (a única né 🤷‍♂️)" autocomplete="off"/>
      <button type="submit">Entrar</button>
    </form>
  </div>

  <!-- Definições SVG usadas nos corações -->
  <svg width="0" height="0" style="position:absolute">
    <defs>
      <!-- Gradiente rosado suave -->
      <radialGradient id="heartGrad" cx="50%" cy="35%" r="70%">
        <stop offset="0%" stop-color="#ffd1df"/>
        <stop offset="60%" stop-color="#ff9cb6"/>
        <stop offset="100%" stop-color="#ff6b95"/>
      </radialGradient>
    </defs>
  </svg>

  <script>
    const area = document.getElementById('hearts-bg');

    // Path do coração (coordenadas normalizadas ~100x92)
    const HEART_D = "M50,90 C40,80,10,60,10,35 C10,20,22,8,35,8 C43,8,50,13,50,20 C50,13,57,8,65,8 C78,8,90,20,90,35 C90,60,60,80,50,90 Z";

    function spawnHeart() {
      const w = 60 + Math.random() * 70;    // 60px ~ 130px
      const h = w * 0.92;
      const x = Math.random() * (window.innerWidth - w);
      const dur = 10 + Math.random() * 10;  // 10s ~ 20s
      const drift = (Math.random() * 160 - 80); // leve deriva lateral
      const rot = (Math.random() * 20 - 10);    // rotação sutil

      // SVG container
      const svg = document.createElementNS('http://www.w3.org/2000/svg','svg');
      svg.setAttribute('viewBox','0 0 100 92');
      svg.style.position = 'absolute';
      svg.style.width = w + 'px';
      svg.style.height = h + 'px';
      svg.style.left = x + 'px';
      svg.style.bottom = '-140px';
      svg.style.filter = 'drop-shadow(0 10px 18px rgba(0,0,0,.35))';
      svg.style.animation = `floatUp ${dur}s linear forwards`;
      svg.style.setProperty('--drift', drift + 'px');

      // Path do coração preenchido com o gradiente
      const path = document.createElementNS('http://www.w3.org/2000/svg','path');
      path.setAttribute('d', HEART_D);
      path.setAttribute('fill','url(#heartGrad)');
      path.setAttribute('transform', `rotate(${rot},50,46)`);

      svg.appendChild(path);
      area.appendChild(svg);

      // remove ao fim da animação
      svg.addEventListener('animationend', () => svg.remove());
    }

    // Spawns iniciais e contínuos
    for (let i=0; i<12; i++) setTimeout(spawnHeart, i*250);
    setInterval(spawnHeart, 1000);
  </script>
</body>
</html>
