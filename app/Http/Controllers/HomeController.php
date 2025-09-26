<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    // Nome alvo já normalizado (sem acentos, minúsculas e espaços normalizados)
    private string $nomeAlvoNormalizado = 'maria helen lima carlos';

    public function showForm()
    {
        return view('welcome'); // tela com input de nome
    }

    public function entrar(Request $request)
    {
        $request->validate([
            'nome' => ['required', 'string', 'max:200'],
        ]);

        $nomeDigitado = $this->normalizar($request->input('nome'));

        if ($nomeDigitado === $this->nomeAlvoNormalizado) {
            // flags de sessão
            session([
                'autorizada' => true,
                'frase_ok'   => false,
            ]);

            // Texto da carta: pode vir de .env (CARTA_TEXTO) se quiser
            $carta = env('CARTA_TEXTO', "Obrigado por ser você e por sempre me proporcionar dias incríveis. Com 2 anos e 2 meses de relacionamento, meu amor por você só aumentou. Até hoje, você continua sendo minha direção, meu tudo. Onde quer que eu vá, quero que esteja comigo.

Hoje, você é minha namorada. Amanhã, minha noiva. E, futuramente, minha esposa.

Nesses últimos dias – na verdade, meses rsrs – a gente teve alguns desentendimentos que abalaram um pouco o nosso sentimento, mas eu sei que isso é normal. Somos parecidos, e gosto de ver tudo isso como uma prova que a vida coloca no nosso caminho pra gente amadurecer e aprender um com o outro. Afinal, se fosse algo raso, o que temos já teria acabado há muito tempo.

Mas o que temos é maior do que qualquer obstáculo. Eu te amo muito, e sempre vou te amar. Você é a mulher da minha vida.

Eu te amo mais do que as estrelas no céu, mais do que os grãos de areia no mundo, mais do que o tempo e os números. Meu amor por você não beira o infinito... ele ultrapassa.

Como dizíamos desde o início: hoje e sempre.💖");

            // guarda a carta para usar depois
            session(['carta_texto' => $carta]);

            // vai para a tela da frase (view que você vai criar)
            return redirect()->route('frase.show');
        }

        return back()->withErrors(['nome' => 'Ops, só a dona do meu coração pode entrar.']);
    }

    /** Mostra a tela da frase (segundo passo) */
    public function showFrase()
    {
        if (!session('autorizada')) {
            return redirect()->route('form');
        }
        return view('frase'); // você cria essa view
    }

    /** Valida a frase “te amo hoje e sempre” e, se ok, libera a carta */
    public function frase(Request $request)
    {
        if (!session('autorizada')) {
            return redirect()->route('form');
        }

        $request->validate([
            'frase' => ['required', 'string', 'max:200'],
        ]);

        $fraseDigitada = $this->normalizar($request->input('frase'));
        $fraseAlvo     = $this->normalizar('te amo hoje e sempre');

        if ($fraseDigitada === $fraseAlvo) {
            session(['frase_ok' => true]);
            // manda pra carta com o texto salvo na sessão
            return redirect()->route('carta')
                ->with('carta', session('carta_texto'))
                ->with('auto_play_audio', true);
        }

       return back()->withErrors(['frase' => 'Errou 🤭 agora me deve um lanche 🍔']);

    }

    public function carta(Request $request)
    {
        // exige nome válido e frase correta
        if (!session('autorizada') || !session('frase_ok')) {
            return redirect()->route('form');
        }

        // Monta a lista de fotos automaticamente a partir de public/fotos
        $dir = public_path('fotos');
        $fotos = [];

        if (File::isDirectory($dir)) {
            foreach (File::files($dir) as $file) {
                $ext = strtolower($file->getExtension());
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                    $fotos[] = asset('fotos/' . $file->getFilename());
                }
            }
        }

        if (!empty($fotos)) {
            shuffle($fotos);
        }

        $autoPlayAudio = session()->pull('auto_play_audio', false);

        return view('carta', [
            'carta' => session('carta', session('carta_texto', 'Texto da carta aqui...')),
            'fotos' => $fotos,
            'autoPlayAudio' => $autoPlayAudio,
        ]);
    }

    private function normalizar(string $s): string
    {
        $s = mb_strtolower($s, 'UTF-8');

        // remover acentos comuns PT-BR
        $map = [
            'á'=>'a','à'=>'a','ã'=>'a','â'=>'a','ä'=>'a',
            'é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
            'í'=>'i','ì'=>'i','î'=>'i','ï'=>'i',
            'ó'=>'o','ò'=>'o','õ'=>'o','ô'=>'o','ö'=>'o',
            'ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u',
            'ç'=>'c',
        ];
        $s = strtr($s, $map);

        // normaliza múltiplos espaços
        $s = preg_replace('/\s+/', ' ', trim($s));

        return $s;
    }
}
