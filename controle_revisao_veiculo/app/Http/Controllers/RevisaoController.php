<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
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

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // 1. Inserir Revisao (sem data_fim)
            $revisao = Revisao::create([
                'data_inicio'   => $request->data_inicio,
                'quilometragem' => $request->quilometragem,
                // 'data_fim' não entra aqui
            ]);

            // 2. Inserir Servicos (array de servicos)
            $servicosIds = [];
            foreach ($request->servicos as $servicoData) {
                $servico = Servico::create([
                    'descricao'        => $servicoData['descricao'],
                    'valor_mao_de_obra'=> $servicoData['valor_mao_de_obra'],
                ]);
                $servicosIds[] = $servico->id_servico;

                // 3. Associar Serviço à Revisão na tabela contems
                Contem::create([
                    'fk_servico_id_servico' => $servico->id_servico,
                    'fk_revisao_id_revisao' => $revisao->id_revisao,
                ]);

                // 4. Se existirem peças para o serviço, insere e associa
                if (isset($servicoData['pecas'])) {
                    foreach ($servicoData['pecas'] as $pecaData) {
                        $peca = Peca::create([
                            'descricao'  => $pecaData['descricao'],
                            'quantidade' => $pecaData['quantidade'],
                            'preco'      => $pecaData['preco'],
                        ]);

                        // Relaciona peça ao serviço na tabela precisas
                        Precisa::create([
                            'fk_peca_codigo'        => $peca->codigo,
                            'fk_servico_id_servico' => $servico->id_servico,
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['message' => 'Revisão cadastrada com sucesso'], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function finalizar($id)
    {
        $revisao = Revisao::findOrFail($id);
        $revisao->data_fim = now();
        $revisao->save();
        return redirect()->back();
    }
}
