<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Models\{Cliente, Veiculo, Realiza, Revisao, Servico, Peca, Contem, Precisa};

class RevisaoController extends Controller
{
    // Só renderiza a página. Sem queries.
    public function index(Request $request)
    {
        return Inertia::render('revisoes/Index', [
            'q' => (string) $request->get('q', '')
        ]);
    }

    // Sugestões de clientes (auto-complete) – leve e com unaccent
    public function clientes(Request $r)
    {
        $q = trim($r->string('q')->toString());

        return \App\Models\Cliente::query()
            ->when($q !== '', function ($w) use ($q) {
                $p = "%{$q}%";
                $w->where(function ($s) use ($p) {
                    $s->where('nome', 'ILIKE', $p)
                    ->orWhere('sobrenome', 'ILIKE', $p)
                    ->orWhereRaw("LOWER(COALESCE(nome,'') || ' ' || COALESCE(sobrenome,'')) ILIKE ?", [$p]);
                });
            })
            ->orderBy('nome')->orderBy('sobrenome')
            ->limit(20)
            ->get(['id_cliente','nome','sobrenome']);
    }

    // Veículos de um cliente (já existia)
    public function veiculosDoCliente($id)
    {
        return Veiculo::where('fk_cliente_id_cliente', $id)
            ->select('placa','marca','modelo','ano')
            ->orderBy('placa')
            ->get();
    }

    // Lista paginada de revisões (status = andamento|finalizadas)
   public function lista(Request $r)
    {
        $status  = trim($r->string('status')->toString()); // andamento|finalizadas|''(todas)
        $q       = trim($r->string('q')->toString());
        $perPage = max(1, min(50, (int) $r->get('per_page', 10)));

        $rows = \DB::table('revisoes as r')
            ->join('realizas as rl', 'rl.fk_revisao_id_revisao', '=', 'r.id_revisao')
            ->join('veiculos as v', 'v.placa', '=', 'rl.fk_veiculo_placa')
            ->join('clientes as c', 'c.id_cliente', '=', 'v.fk_cliente_id_cliente')
            ->when($status === 'andamento',   fn($w) => $w->whereNull('r.data_fim'))
            ->when($status === 'finalizadas', fn($w) => $w->whereNotNull('r.data_fim'))
            ->when($q !== '', function ($w) use ($q) {
                $p = "%{$q}%";
                $w->where(function ($s) use ($p) {
                    $s->where('c.nome', 'ILIKE', $p)
                    ->orWhere('c.sobrenome', 'ILIKE', $p)
                    ->orWhere('v.placa', 'ILIKE', $p)
                    ->orWhereRaw("LOWER(COALESCE(c.nome,'') || ' ' || COALESCE(c.sobrenome,'')) ILIKE ?", [$p]);
                });
            })
            ->select(
                'r.id_revisao',
                \DB::raw("TO_CHAR(r.data_inicio, 'DD/MM/YYYY HH24:MI') as data_inicio"),
                \DB::raw("TO_CHAR(r.data_fim, 'DD/MM/YYYY HH24:MI') as data_fim"),
                'r.quilometragem',
                'c.id_cliente','c.nome','c.sobrenome',
                'v.placa'
            )
            ->orderByDesc('r.id_revisao')
            ->simplePaginate($perPage);

        return response()->json($rows);
    }

    // (mantido) busca serviços
    public function searchServicos(Request $r)
    {
        $q = trim($r->get('q',''));
        return Servico::when($q, fn($w)=>$w->where('descricao','ILIKE',"%{$q}%"))
            ->orderBy('descricao')->limit(20)->get(['id_servico','descricao','valor_mao_de_obra']);
    }

    // (mantido) busca peças
    public function searchPecas(Request $r)
    {
        $q = trim($r->get('q',''));
        $servicoId = $r->get('servico_id');

        $base = Peca::query();
        $base->when($servicoId || $q !== '', function ($qq) use ($servicoId, $q) {
            $qq->where(function ($w) use ($servicoId, $q) {
                if ($servicoId) {
                    $w->orWhereIn('codigo', function($s) use ($servicoId){
                        $s->select('fk_peca_codigo')->from('precisas')
                          ->where('fk_servico_id_servico', $servicoId);
                    });
                }
                if ($q !== '') $w->orWhere('descricao','ILIKE',"%{$q}%");
            });
        });

        return $base->orderBy('descricao')->limit(20)->get(['codigo','descricao','preco']);
    }


    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // 1. Selecionar Cliente
            $cliente = Cliente::findOrFail($request->id_cliente);

            // 2. Cadastrar (ou recuperar) Veículo
            $veiculo = Veiculo::firstOrCreate(
                ['placa' => $request->placa],
                [
                    'marca' => $request->marca,
                    'modelo' => $request->modelo,
                    'ano' => $request->ano,
                    'fk_cliente_id_cliente' => $cliente->id_cliente
                ]
            );

            // 3. Inserir Revisão
            $revisao = Revisao::create([
                'data_inicio'   => $request->data_inicio,
                'quilometragem' => $request->quilometragem,
            ]);

            // 4. Associar veículo à revisão (realizas)
            Realiza::create([
                'fk_veiculo_placa' => $veiculo->placa,
                'fk_revisao_id_revisao' => $revisao->id_revisao
            ]);

            // 5. Serviços e peças
            foreach ($request->servicos as $servicoData) {
                // usa existente se veio id_servico; senão cria
                if (!empty($servicoData['id_servico'])) {
                    $servico = Servico::findOrFail($servicoData['id_servico']);
                    // atualiza valor se usuário editou
                    if (isset($servicoData['valor_mao_de_obra'])) {
                        $servico->valor_mao_de_obra = $servicoData['valor_mao_de_obra'];
                        $servico->save();
                    }
                    // atualiza descrição se alterada (opcional)
                    if (!empty($servicoData['descricao']) && $servico->descricao !== $servicoData['descricao']) {
                        $servico->descricao = $servicoData['descricao'];
                        $servico->save();
                    }
                } else {
                    $servico = Servico::create([
                        'descricao'         => $servicoData['descricao'],
                        'valor_mao_de_obra' => $servicoData['valor_mao_de_obra'] ?? 0,
                    ]);
                }

                // vincula revisão-serviço
                Contem::create([
                    'fk_servico_id_servico' => $servico->id_servico,
                    'fk_revisao_id_revisao' => $revisao->id_revisao,
                ]);

                // peças
                foreach ($servicoData['pecas'] ?? [] as $pecaData) {
                    if (!empty($pecaData['codigo'])) {
                        $peca = Peca::findOrFail($pecaData['codigo']);
                        // preço/descrição podem vir editados — aplica se quiser refletir no catálogo
                        if (isset($pecaData['preco']))       { $peca->preco = $pecaData['preco']; }
                        if (!empty($pecaData['descricao']))  { $peca->descricao = $pecaData['descricao']; }
                        if (isset($pecaData['quantidade']))  { $peca->quantidade = $pecaData['quantidade']; }
                        $peca->save();
                    } else {
                        // cria peça "avulsa" com qtd/preço do formulário
                        $peca = Peca::create([
                            'descricao'  => $pecaData['descricao'],
                            'quantidade' => $pecaData['quantidade'] ?? 1,
                            'preco'      => $pecaData['preco'] ?? 0,
                        ]);
                    }

                    // vincula serviço-peça
                    Precisa::firstOrCreate([
                        'fk_peca_codigo'        => $peca->codigo,
                        'fk_servico_id_servico' => $servico->id_servico,
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Revisão cadastrada com sucesso');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function detalhes($id)
    {
        $rev = \DB::table('revisoes as r')
            ->join('realizas as rl', 'rl.fk_revisao_id_revisao', '=', 'r.id_revisao')
            ->join('veiculos as v', 'v.placa', '=', 'rl.fk_veiculo_placa')
            ->join('clientes as c', 'c.id_cliente', '=', 'v.fk_cliente_id_cliente')
            ->where('r.id_revisao', $id)
            ->select('r.id_revisao','r.data_inicio','r.data_fim','r.quilometragem',
                    'v.placa','v.marca','v.modelo','v.ano',
                    'c.id_cliente','c.nome','c.sobrenome')
            ->first();

        if (!$rev) return response()->json(['message' => 'Revisão não encontrada'], 404);

        $servicos = \DB::table('contems as ct')
            ->join('servicos as s', 's.id_servico', '=', 'ct.fk_servico_id_servico')
            ->where('ct.fk_revisao_id_revisao', $id)
            ->select('s.id_servico','s.descricao','s.valor_mao_de_obra')
            ->get();

        $ids = $servicos->pluck('id_servico')->all();
        $pecasPorServico = [];
        if (!empty($ids)) {
            $rows = \DB::table('precisas as pr')
                ->join('pecas as p', 'p.codigo', '=', 'pr.fk_peca_codigo')
                ->whereIn('pr.fk_servico_id_servico', $ids)
                ->select('pr.fk_servico_id_servico as sid','p.codigo','p.descricao','p.preco','p.quantidade')
                ->get();

            foreach ($rows as $r) {
                $pecasPorServico[$r->sid][] = (object)[
                    'codigo'=>$r->codigo,'descricao'=>$r->descricao,
                    'preco'=>$r->preco,'quantidade'=>$r->quantidade
                ];
            }
            // garante array vazio para serviços sem peças
            foreach ($ids as $sid) $pecasPorServico[$sid] = $pecasPorServico[$sid] ?? [];
        }

        return response()->json([
            'revisao'  => $rev,
            'servicos' => $servicos,
            'pecas'    => $pecasPorServico,
        ]);
    }


    public function finalizar(Request $request, $id)
    {
        $revisao = Revisao::findOrFail($id);

        // sempre a hora do clique (servidor é a fonte da verdade)
        $revisao->fill(['data_fim' => now()]);
        $revisao->save();

        return back(303)->with('success', 'Revisão finalizada com sucesso');
    }
}
