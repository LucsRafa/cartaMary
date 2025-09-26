<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Para você 💌</title>
  <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=1">
  <link rel="alternate icon" href="/favicon.ico">
  <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap" rel="stylesheet">

  <style>
    :root { --bg:#3d0d1c; --panel:#5e0a2e; --text:#ffe4ec; --muted:#ffd6e2; --accent:#ff8fb2; --paper:#fff8fb; --paper-line:#ffd3e2; --paper-shadow: rgba(0,0,0,.25); }
    *{box-sizing:border-box}
    body{
      margin:0;
      background: radial-gradient(1200px 600px at 50% -10%, #7a1330 0%, var(--bg) 60%);
      color:var(--text);
      min-height:100vh;
      overflow-y:auto; overflow-x:hidden;
      scroll-behavior:smooth;
      font-family:system-ui,sans-serif;
    }
    .wrap{ position:relative; min-height:100vh; display:grid; place-items:center; padding:24px;}
    .carta{
      position:relative; z-index:2; max-width:760px; background:var(--panel);
      border:1px solid rgba(255,255,255,.09); padding:20px; border-radius:18px; box-shadow:0 15px 60px rgba(0,0,0,.45);
    }
    .paper{
      position:relative;
      background:repeating-linear-gradient(to bottom, var(--paper) 0 30px, var(--paper-line) 34px 36px);
      border-radius:14px; padding:26px 26px 22px 34px; box-shadow:0 12px 24px var(--paper-shadow); border:1px solid rgba(0,0,0,.06);
    }
    .paper:before{ content:""; position:absolute; left:18px; top:12px; bottom:12px; width:2px; background:#ff9cb6; opacity:.6; border-radius:2px; }
    h1{ margin:0 0 .75rem 0; font-size:1.6rem; color:var(--panel) }
    .texto-carta{
      white-space: pre-line; font-family:'Dancing Script',cursive; color:#3d0d1c;
      font-size:1.55rem; line-height:2.2rem; text-shadow:0 1px 0 rgba(255,255,255,.35); text-align:left;
    }
    .assinatura{ margin-top:1rem; color:#5e0a2e; font-weight:700; text-align:right; padding-right:.4rem; font-family:'Dancing Script',cursive; font-size:1.4rem; }

    .hearts{ position:absolute; inset:0; overflow:hidden; z-index:1; pointer-events:none; }
    @keyframes floatUp {
      0% { transform: translateY(100vh) translateX(0) rotate(0deg); opacity:0; }
      10%{ opacity:1; }
      100%{ transform: translateY(-120vh) translateX(var(--drift,0px)) rotate(12deg); opacity:0; }
    }
    .heart{ position:absolute; width:var(--w,120px); height:var(--h,110px); left:var(--x,50%); bottom:-140px; animation:floatUp var(--dur,12s) linear forwards; filter:drop-shadow(0 10px 18px rgba(0,0,0,.35)); }

    .sussurro{ position:absolute; bottom:24px; width:100%; text-align:center; color:var(--accent); opacity:.9; font-size:.95rem; z-index:2; animation:pulse 3s ease-in-out infinite; }
    @keyframes pulse{0%,100%{opacity:.55}50%{opacity:.95}}

    .audio-toggle{
      position:fixed; right:16px; bottom:16px; z-index:5;
      background:var(--accent); color:#fff; border:none;
      padding:.7rem 1rem; border-radius:999px; font-weight:600;
      box-shadow:0 8px 20px rgba(0,0,0,.35); cursor:pointer;
    }
    .audio-toggle:hover{ filter:brightness(1.1); transform:translateY(-1px); }
    .audio-toggle.muted{ opacity:.85; }

    #skipType{ margin-top:.75rem; background:var(--accent); color:#fff; border:none; border-radius:.6rem; padding:.5rem .8rem; cursor:pointer; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="carta">
      <div class="paper">
        <h1>Para você, meu amor 💌</h1>
        <div id="typewriter" class="texto-carta"></div>
        <button id="skipType">Pular animação</button>
        <div class="assinatura">Com amor, <strong>Lucas</strong></div>
      </div>
    </div>

    <div class="hearts" id="hearts"></div>
    <div class="sussurro">Leia devagar e aproveite… 💞</div>
  </div>

  <!-- Botão de áudio -->
  <button id="audioBtn" class="audio-toggle muted" aria-pressed="false">🔇 Ligar som</button>

  <!-- Áudio com múltiplas fontes -->
  <audio id="bgAudio" preload="auto" loop playsinline webkit-playsinline muted>
    <source src="{{ asset('audio/ambiente.ogg') }}" type="audio/ogg">
    <source src="{{ asset('audio/ambiente.m4a') }}" type="audio/mp4">
    <source src="{{ asset('audio/ambiente.mp3') }}" type="audio/mpeg">
  </audio>

  <!-- Clip de coração -->
  <svg width="0" height="0" style="position:absolute">
    <defs>
      <clipPath id="heartClip" clipPathUnits="objectBoundingBox">
        <path d="M0.5,0.9 C0.4,0.8,0.1,0.6,0.1,0.35 C0.1,0.2,0.22,0.08,0.35,0.08 C0.43,0.08,0.5,0.13,0.5,0.2 C0.5,0.13,0.57,0.08,0.65,0.08 C0.78,0.08,0.9,0.2,0.9,0.35 C0.9,0.6,0.6,0.8,0.5,0.9Z"/>
      </clipPath>
    </defs>
  </svg>

  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
  <script>
  (() => {
    'use strict';

    const wantsAutoPlay = @json($autoPlayAudio ?? false);

    // ===== Corações =====
    const fotos = @json($fotos);
    const area  = document.getElementById('hearts');

    function spawnHeart(){
      if(!Array.isArray(fotos) || !fotos.length || !area) return;
      const w=90+Math.random()*80, h=w*0.92;
      const x=Math.random()*(window.innerWidth-w);
      const dur=10+Math.random()*8, drift=(Math.random()*160-80);
      const foto=fotos[Math.floor(Math.random()*fotos.length)];

      const svg=document.createElementNS('http://www.w3.org/2000/svg','svg');
      svg.setAttribute('viewBox', `0 0 ${w} ${h}`);
      svg.classList.add('heart');
      svg.style.setProperty('--w', `${w}px`);
      svg.style.setProperty('--h', `${h}px`);
      svg.style.setProperty('--x', `${x}px`);
      svg.style.setProperty('--dur', `${dur}s`);
      svg.style.setProperty('--drift', `${drift}px`);

      const image=document.createElementNS('http://www.w3.org/2000/svg','image');
      image.setAttributeNS('http://www.w3.org/1999/xlink','href', foto);
      image.setAttribute('width', w);
      image.setAttribute('height', h);
      image.setAttribute('preserveAspectRatio','xMidYMid slice');
      image.setAttribute('clip-path','url(#heartClip)');

      svg.appendChild(image);
      area.appendChild(svg);
      svg.addEventListener('animationend', ()=>svg.remove());
    }

    // ===== Áudio: autoplay silencioso + desbloqueio na 1ª interação + botão =====
    const audio    = document.getElementById('bgAudio');
    const audioBtn = document.getElementById('audioBtn');

    function setBtn(playing){
      if(!audioBtn) return;
      if(playing){
        audioBtn.textContent='🔊 Desligar som';
        audioBtn.classList.remove('muted');
        audioBtn.setAttribute('aria-pressed','true');
      } else {
        audioBtn.textContent='🔇 Ligar som';
        audioBtn.classList.add('muted');
        audioBtn.setAttribute('aria-pressed','false');
      }
    }

    async function playWith(volume=0.35, options={}){
      const { showControls = true } = options;
      try{
        audio.volume = volume;
        audio.muted  = false;
        await audio.play();
        setBtn(true);
        return true;
      }catch(e){
        console.warn('[AUDIO] play falhou:', e?.name || e);
        if(showControls){
          // fallback: mostra controles nativos
          audio.setAttribute('controls','controls');
        }
        setBtn(false);
        return false;
      }
    }

    function armUnlockOnce(){
      let armed = true;
      const handler = async (evt) => {
        if(!armed) return;
        if(audioBtn && evt?.target && audioBtn.contains(evt.target) && evt.type !== 'keydown'){
          return;
        }
        armed = false;
        const ok = await playWith(0.35);
        if(ok){
          detach();
        } else {
          armed = true;
        }
      };
      function detach(){
        window.removeEventListener('pointerdown', handler);
        window.removeEventListener('keydown',     handler);
        window.removeEventListener('touchstart',  handler);
      }
      window.addEventListener('pointerdown', handler, { passive:true });
      window.addEventListener('keydown',     handler);
      window.addEventListener('touchstart',  handler, { passive:true });
    }

    if(audioBtn){
      audioBtn.addEventListener('click', async ()=>{
        if(audio.paused){ await playWith(); }
        else { audio.pause(); setBtn(false); }
      });
    }

    // Logs úteis
    if(audio){
      audio.addEventListener('error',()=>{
        const code=audio.error?.code, map={1:'ABORTED',2:'NETWORK',3:'DECODE',4:'SRC_NOT_SUPPORTED'};
        console.warn('[AUDIO ERROR]', map[code]||code, audio.error);
      });
      audio.addEventListener('loadedmetadata', ()=>console.log('[AUDIO] metadata OK, duration=', audio.duration));
      audio.addEventListener('canplay', ()=>console.log('[AUDIO] canplay'));
    }

    // ===== Typewriter =====
    const fullLetter = @json($carta);
    const typeEl  = document.getElementById('typewriter');
    const skipBtn = document.getElementById('skipType');
    const SPEED=22, PUNCT_PAUSE=180; let skipped=false;

    async function typeWriterAll(text){
      if(!typeEl) return;
      text = String(text ?? '').replace(/\r\n/g,'\n');
      for(let i=0;i<text.length;i++){
        if(skipped){ typeEl.textContent=text; return; }
        typeEl.textContent += text[i];
        let delay = SPEED;
        if(/[\.!\?…]/.test(text[i])) delay += PUNCT_PAUSE;
        if(text[i]==='\n') delay += 60;
        await new Promise(r=>setTimeout(r,delay));
      }
    }
    if(skipBtn){
      skipBtn.addEventListener('click',()=>{
        skipped=true; if(typeEl) typeEl.textContent = fullLetter ?? ''; skipBtn.style.display='none';
      });
    }

    // ===== Init =====
    window.addEventListener('load', async () => {
      if(audio){
        try{
          audio.muted = true;
          await audio.play();
          console.log('[AUDIO] autoplay silencioso OK');
        }catch(e){
          console.warn('[AUDIO] autoplay silencioso bloqueado:', e?.name || e);
        }

        let autoPlayed = false;
        if(wantsAutoPlay){
          autoPlayed = await playWith(0.35, { showControls:false });
        }
        if(!autoPlayed){
          armUnlockOnce();
        }
        setBtn(!audio.paused);
      }

      // 2) corações + confetes
      for(let i=0;i<12;i++) setTimeout(spawnHeart,i*300);
      setInterval(spawnHeart,300);
      confetti({particleCount:80,angle:60,spread:55,origin:{x:0},colors:['#ff8fb2','#ffe4ec','#ffd6e2']});
      confetti({particleCount:80,angle:120,spread:55,origin:{x:1},colors:['#ff8fb2','#ffe4ec','#ffd6e2']});
      setTimeout(()=>{confetti({particleCount:160,spread:70,startVelocity:35,origin:{x:0.5,y:0.6}})},500);

      // 3) escreve a carta
      await typeWriterAll(fullLetter);
      if(skipBtn) skipBtn.style.display='none';
    });

    document.addEventListener('visibilitychange', async ()=>{
      if(document.visibilityState==='visible' && audio && audio.paused){
        // se voltar pro tab e ainda estiver pausado, tenta de novo (silencioso → gesto libera)
        try{ audio.muted = true; await audio.play(); }catch(_){}
      }
    });

  })();
  </script>
</body>
</html>
