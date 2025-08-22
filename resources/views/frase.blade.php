<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nossa frase 💞</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg?v=1') }}">
    <style>
        /* Tema romântico (rosa) */
        :root {
            --bg: #3d0d1c;
            /* fundo da tela */
            --panel: #5e0a2e;
            /* fundo do card */
            --text: #ffe4ec;
            /* cor do texto */
            --muted: #ffd6e2;
            /* textos suaves */
            --accent: #ff8fb2;
            /* botão e detalhes */
        }

        * {
            box-sizing: border-box;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            overflow: hidden;
            font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            background: radial-gradient(1200px 600px at 50% -10%, #7a1330 0%, var(--bg) 60%);
            color: var(--text);
        }

        /* BG de corações discretos */
        .hearts-bg {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
            opacity: .25;
        }

        @keyframes floatUp {
            0% {
                transform: translateY(0) translateX(0) rotate(0deg);
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            100% {
                transform: translateY(-120vh) translateX(var(--drift, 0px)) rotate(12deg);
                opacity: 0;
            }
        }

        .card {
            position: relative;
            z-index: 2;
            background: var(--panel);
            padding: 2rem;
            border-radius: 1rem;
            width: clamp(300px, 90vw, 480px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, .45);
            border: 1px solid rgba(255, 255, 255, .08);
            text-align: left;
        }

        h1 {
            margin: 0 0 .25rem 0;
            color: var(--text);
            font-size: 1.4rem;
        }

        p.hint {
            margin: .25rem 0 1.25rem 0;
            color: var(--muted);
            font-size: .95rem;
        }

        label {
            display: block;
            font-size: .95rem;
            color: var(--muted);
        }

        input {
            width: 100%;
            margin-top: .5rem;
            padding: .9rem 1rem;
            background: #2a0f1d;
            color: var(--text);
            border: 1px solid #82364d;
            border-radius: .75rem;
            outline: none;
        }

        input:focus {
            border-color: var(--accent);
        }

        button {
            margin-top: 1rem;
            width: 100%;
            padding: .9rem 1rem;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: .75rem;
            cursor: pointer;
            font-weight: 600;
            transition: transform .15s ease, filter .2s ease;
        }

        button:hover {
            filter: brightness(1.08);
            transform: translateY(-1px);
        }

        .error {
            margin-top: .75rem;
            color: #fca5a5;
            font-size: .9rem;
        }

        .backtip {
            margin-top: .75rem;
            color: var(--muted);
            font-size: .8rem;
            opacity: .9;
        }
    </style>
</head>

<body>
    <!-- Corações suaves no fundo -->
    <div class="hearts-bg" id="hearts-bg"></div>

    <div class="card">
        <h1>💌 Só mais um detalhe…</h1>
        <p class="hint">Qual é a nossa frase juntos?</p>
        @if ($errors->any())
        <div class="error">{{ $errors->first() }}</div>
        <script>
            window.addEventListener('load', () => {
                alert("{{ $errors->first() }}");
            });
        </script>
        @endif


        <form method="POST" action="{{ route('frase') }}">
            @csrf
            <label for="frase">Digite a frase</label>
            <input id="frase" name="frase" placeholder="Aviso!: Se erra 1 vez me deve um lanche" autocomplete="off" autofocus />
            <button type="submit">Confirmar</button>
        </form>

        <div class="backtip">Dica: pode ser com maiúsculas ou minúsculas 👀</div>
    </div>

    <!-- Corações SVG do fundo -->
    <script>
        const area = document.getElementById('hearts-bg');

        // Path do coração (100x92)
        const HEART_D = "M50,90 C40,80,10,60,10,35 C10,20,22,8,35,8 C43,8,50,13,50,20 C50,13,57,8,65,8 C78,8,90,20,90,35 C90,60,60,80,50,90 Z";

        function spawnHeartBg() {
            const w = 40 + Math.random() * 50; // 40~90px
            const h = w * 0.92;
            const x = Math.random() * (window.innerWidth - w);
            const dur = 12 + Math.random() * 12; // 12~24s
            const drift = (Math.random() * 140 - 70);
            const rot = (Math.random() * 20 - 10);

            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('viewBox', '0 0 100 92');
            svg.style.position = 'absolute';
            svg.style.width = w + 'px';
            svg.style.height = h + 'px';
            svg.style.left = x + 'px';
            svg.style.bottom = '-140px';
            svg.style.filter = 'drop-shadow(0 10px 18px rgba(0,0,0,.25))';
            svg.style.animation = `floatUp ${dur}s linear forwards`;
            svg.style.setProperty('--drift', drift + 'px');

            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('d', HEART_D);
            path.setAttribute('fill', '#ff9cb6');
            path.setAttribute('transform', `rotate(${rot},50,46)`);

            svg.appendChild(path);
            area.appendChild(svg);
            svg.addEventListener('animationend', () => svg.remove());
        }

        // Dispara alguns de começo e segue suave
        for (let i = 0; i < 10; i++) setTimeout(spawnHeartBg, i * 250);
        setInterval(spawnHeartBg, 1200);

        // Enviar ao apertar Enter (já funciona via submit padrão, mas garantimos UX):
        const input = document.getElementById('frase');
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.target.form?.submit();
            }
        });
    </script>
</body>

</html>