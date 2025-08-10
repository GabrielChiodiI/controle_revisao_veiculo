<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Cliente;
use App\Models\Veiculo;
use App\Models\Realiza;
use App\Models\Revisao;
use App\Models\Servico;
use App\Models\Peca;
use App\Models\Contem;
use App\Models\Precisa;
use Illuminate\Support\Facades\DB;

class RevisaoController extends Controller
{
    public function index()
    {
        $revisoes = Revisao::all();

        $clientes = Cliente::select('id_cliente','nome','sobrenome')
            ->orderBy('nome')
            ->orderBy('sobrenome')
            ->get();

        return Inertia::render('revisoes/Index', [
            'revisoes' => $revisoes,
            'clientes' => $clientes,
        ]);
    }

    public function veiculosDoCliente($id)
    {
        $veiculos = \App\Models\Veiculo::where('fk_cliente_id_cliente', $id)
            ->select('placa','marca','modelo','ano')
            ->orderBy('placa')
            ->get();

        return response()->json($veiculos);
    }

    public function todasRevisoes()
    {
        $revisoes = DB::table('revisoes')
            ->join('realizas', 'revisoes.id_revisao', '=', 'realizas.fk_revisao_id_revisao')
            ->join('veiculos', 'realizas.fk_veiculo_placa', '=', 'veiculos.placa')
            ->join('clientes', 'veiculos.fk_cliente_id_cliente', '=', 'clientes.id_cliente')
            ->select(
                'revisoes.id_revisao',
                'revisoes.data_inicio',
                'revisoes.data_fim',
                'revisoes.quilometragem',
                'clientes.id_cliente',
                'clientes.nome',
                'clientes.sobrenome',
                'veiculos.placa'
            )
            ->get();

        return response()->json($revisoes);
    }

    public function searchServicos(Request $r)
    {
        $q = trim($r->get('q',''));
        return Servico::when($q, fn($w)=>$w->where('descricao','ILIKE',"%{$q}%"))
            ->orderBy('descricao')->limit(20)->get(['id_servico','descricao','valor_mao_de_obra']);
    }

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
                if ($q !== '') {
                    $w->orWhere('descricao','ILIKE',"%{$q}%");
                }
            });
        });

        return $base->orderBy('descricao')
            ->limit(20)
            ->get(['codigo','descricao','preco']);
    }

    public function pecasDoServico($id)
    {
        return Peca::select('pecas.codigo','pecas.descricao','pecas.preco')
            ->join('precisas','precisas.fk_peca_codigo','=','pecas.codigo')
            ->where('precisas.fk_servico_id_servico',$id)
            ->orderBy('descricao')->get();
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

    public function finalizar($id)
    {
        $revisao = Revisao::findOrFail($id);
        $revisao->data_fim = now();
        $revisao->save();
        return redirect()->back()->with('success', 'Revisão finalizada com sucesso');
    }
}
