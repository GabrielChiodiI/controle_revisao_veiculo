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
        return Inertia::render('revisoes/Index', [
            'revisoes' => $revisoes
        ]);
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
                $servico = Servico::create([
                    'descricao'        => $servicoData['descricao'],
                    'valor_mao_de_obra'=> $servicoData['valor_mao_de_obra'],
                ]);
                Contem::create([
                    'fk_servico_id_servico' => $servico->id_servico,
                    'fk_revisao_id_revisao' => $revisao->id_revisao,
                ]);
                if (isset($servicoData['pecas'])) {
                    foreach ($servicoData['pecas'] as $pecaData) {
                        $peca = Peca::create([
                            'descricao'  => $pecaData['descricao'],
                            'quantidade' => $pecaData['quantidade'],
                            'preco'      => $pecaData['preco'],
                        ]);
                        Precisa::create([
                            'fk_peca_codigo'        => $peca->codigo,
                            'fk_servico_id_servico' => $servico->id_servico,
                        ]);
                    }
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
