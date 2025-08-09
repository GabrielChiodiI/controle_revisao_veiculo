<?php

namespace App\Http\Controllers;

use App\Models\Revisao;
use App\Models\Veiculo;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;

class RelatorioController extends Controller
{
    public function index()
    {
        return Inertia::render('relatorios/Index');
    }

    // --- Métodos de relatórios de veículos ---
    public function todosVeiculos()
    {
        $dados = Veiculo::select('marca', 'modelo', DB::raw('COUNT(*) as quantidade'))
            ->groupBy('marca', 'modelo')
            ->orderByDesc('quantidade')
            ->get();

        // Renomeia os campos para "Marca", "Modelo", "Quantidade"
        $dadosFormatado = $dados->map(function($item) {
            return [
                'Marca' => $item->marca,
                'Modelo' => $item->modelo,
                'Quantidade' => $item->quantidade,
            ];
        });

        return response()->json($dadosFormatado);
    }

    public function todosVeiculosPorPessoa()
    {
        $dados = \DB::table('veiculos as v')
            ->join('clientes as c', 'v.fk_cliente_id_cliente', '=', 'c.id_cliente')
            ->select(
                \DB::raw("CONCAT(c.nome, ' ', c.sobrenome) as Pessoa"),
                \DB::raw("CONCAT(v.marca, ' ', v.modelo) as Veiculo")
            )
            ->orderByRaw("CONCAT(c.nome, ' ', c.sobrenome)")
            ->get();

        // Converte as chaves para Maiúscula
        $dadosFormatado = $dados->map(function ($item) {
            return [
                'Pessoa' => $item->pessoa,
                'Veiculo' => $item->veiculo,
            ];
        });

        return response()->json($dadosFormatado);
    }

    public function maisVeiculosPorSexo()
    {
        // Subquery para contar veículos por sexo
        $sub = \DB::table('clientes as c')
            ->join('veiculos as v', 'c.id_cliente', '=', 'v.fk_cliente_id_cliente')
            ->select('c.sexo', \DB::raw('COUNT(v.placa) as quantidade'))
            ->groupBy('c.sexo');

        // Descobre o maior total
        $maior = \DB::table(\DB::raw("({$sub->toSql()}) as totais"))
            ->mergeBindings($sub)
            ->selectRaw('MAX(quantidade) as max_quantidade')
            ->first()->max_quantidade;

        // Pega todos que empataram no maior total
        $dados = \DB::table('clientes as c')
            ->join('veiculos as v', 'c.id_cliente', '=', 'v.fk_cliente_id_cliente')
            ->select('c.sexo as sexo', \DB::raw('COUNT(v.placa) as quantidade'))
            ->groupBy('c.sexo')
            ->havingRaw('COUNT(v.placa) = ?', [$maior])
            ->get();

        $dadosFormatado = $dados->map(function ($item) {
            return [
                'Sexo' => $item->sexo,
                'Quantidade' => $item->quantidade,
            ];
        });

        return response()->json($dadosFormatado);
    }
    
    public function marcasPorNumeroVeiculos()
    {
        $dados = Veiculo::select('marca', DB::raw('COUNT(*) as quantidade'))
            ->groupBy('marca')
            ->orderByDesc('quantidade')
            ->get();

        // Formata as chaves como "Marca" e "Quantidade"
        $dadosFormatado = $dados->map(function($item) {
            return [
                'Marca' => $item->marca,
                'Quantidade' => $item->quantidade,
            ];
        });

        return response()->json($dadosFormatado);
    }

    public function marcasQuantidadePorSexo()
    {
        $dados = \DB::table('veiculos as v')
            ->join('clientes as c', 'v.fk_cliente_id_cliente', '=', 'c.id_cliente')
            ->select(
                \DB::raw('c.sexo as sexo'),
                \DB::raw('v.marca as marca'),
                \DB::raw('COUNT(*) as quantidade')
            )
            ->groupBy('c.sexo', 'v.marca')
            ->orderBy('c.sexo')
            ->orderByRaw('COUNT(*) DESC')
            ->get();

        $dadosFormatado = $dados->map(function($item) {
            return [
                'Sexo' => $item->sexo,
                'Marca' => $item->marca,
                'Quantidade' => $item->quantidade,
            ];
        });

        return response()->json($dadosFormatado);
    }

    // --- Métodos de relatórios de pessoas ---
    public function todasPessoas()
    {
        $dados = \DB::table('clientes')
            ->select(\DB::raw("nome || ' ' || sobrenome as Pessoas"))
            ->get();

        $dadosFormatado = $dados->map(function($item) {
            return [
                'Pessoas' => $item->pessoas,
            ];
        });

        return response()->json($dadosFormatado);
    }

    public function pessoasPorSexoComMediaIdade()
    {
        $dados = Cliente::select(
                'sexo',
                DB::raw('COUNT(*) as total'),
                DB::raw('AVG(EXTRACT(YEAR FROM age(data_nascimento))) as media_idade')
            )
            ->groupBy('sexo')
            ->get();

        // Formata as chaves e a média com 2 casas decimais
        $dadosFormatado = $dados->map(function($item) {
            return [
                'Sexo' => $item->sexo,
                'Total' => $item->total,
                'Média de Idade' => number_format($item->media_idade, 2, ',', ''),
            ];
        });

        return response()->json($dadosFormatado);
    }

    // --- Métodos de relatórios de revisões ---
    public function revisoesPorPeriodo(Request $request)
    {
        $inicio = $request->query('inicio');
        $fim = $request->query('fim');

        $dados = \DB::table('revisoes')
            ->join('realizas', 'revisoes.id_revisao', '=', 'realizas.fk_revisao_id_revisao')
            ->join('veiculos', 'realizas.fk_veiculo_placa', '=', 'veiculos.placa')
            ->join('clientes', 'veiculos.fk_cliente_id_cliente', '=', 'clientes.id_cliente')
            ->whereBetween('revisoes.data_inicio', [$inicio, $fim])
            ->selectRaw("
                CONCAT(TO_CHAR(revisoes.data_inicio, 'DD/MM/YYYY'), ' - ', TO_CHAR(revisoes.data_fim, 'DD/MM/YYYY')) as periodo,
                revisoes.id_revisao,
                CONCAT(clientes.nome, ' ', clientes.sobrenome) as cliente,
                CONCAT(veiculos.marca, ' ', veiculos.modelo) as veiculo
            ")
            ->get();

        $dadosFormatado = $dados->map(function($item) {
            return [
                'Início/Fim Revisão' => $item->periodo,
                'ID de Revisão' => $item->id_revisao,
                'Cliente' => $item->cliente,
                'Veículo' => $item->veiculo,
            ];
        });

        return response()->json($dadosFormatado);
    }

    public function marcasComMaisRevisoes()
    {
        $dados = DB::table('revisoes')
            ->join('realizas', 'revisoes.id_revisao', '=', 'realizas.fk_revisao_id_revisao')
            ->join('veiculos', 'realizas.fk_veiculo_placa', '=', 'veiculos.placa')
            ->select('veiculos.marca', DB::raw('COUNT(*) as total'))
            ->groupBy('veiculos.marca')
            ->orderByDesc('total')
            ->get();

        $dadosFormatado = $dados->map(function($item) {
            return [
                'Marca' => $item->marca,
                'Total' => $item->total,
            ];
        });

        return response()->json($dadosFormatado);
    }

    public function pessoasComMaisRevisoes()
    {
        $dados = DB::table('revisoes')
            ->join('realizas', 'revisoes.id_revisao', '=', 'realizas.fk_revisao_id_revisao')
            ->join('veiculos', 'realizas.fk_veiculo_placa', '=', 'veiculos.placa')
            ->join('clientes', 'veiculos.fk_cliente_id_cliente', '=', 'clientes.id_cliente')
            ->select(
                DB::raw("clientes.nome || ' ' || clientes.sobrenome as Pessoa"),
                DB::raw('COUNT(*) as Quantidade')
            )
            ->groupBy('clientes.id_cliente', 'clientes.nome', 'clientes.sobrenome')
            ->orderByRaw('COUNT(*) DESC')
            ->get();

        $dadosFormatado = $dados->map(function($item) {
            return [
                'Pessoa' => $item->pessoa,
                'Quantidade' => $item->quantidade,
            ];
        });

        return response()->json($dadosFormatado);
    }

    public function mediaTempoEntreRevisoesPorPessoa()
    {
        $sql = "
            WITH revisoes_ordenadas AS (
                SELECT
                    c.id_cliente,
                    c.nome || ' ' || c.sobrenome AS cliente,
                    r.data_inicio,
                    r.data_fim,
                    ROW_NUMBER() OVER (PARTITION BY c.id_cliente ORDER BY r.data_inicio) AS rn
                FROM revisoes r
                JOIN realizas s ON r.id_revisao = s.fk_revisao_id_revisao
                JOIN veiculos v ON s.fk_veiculo_placa = v.placa
                JOIN clientes c ON v.fk_cliente_id_cliente = c.id_cliente
                WHERE r.data_fim IS NOT NULL
            ),
            pares_revisao AS (
                SELECT
                    r1.id_cliente,
                    r1.cliente,
                    (r2.data_inicio::date - r1.data_fim::date) AS dias
                FROM revisoes_ordenadas r1
                JOIN revisoes_ordenadas r2
                    ON r1.id_cliente = r2.id_cliente AND r2.rn = r1.rn + 1
            ),
            clientes_media AS (
                SELECT
                    c.id_cliente,
                    c.nome || ' ' || c.sobrenome AS cliente,
                    COALESCE(ROUND(AVG(p.dias)::numeric, 2), 0) AS \"MediaTempoDias\"
                FROM clientes c
                LEFT JOIN pares_revisao p ON c.id_cliente = p.id_cliente
                GROUP BY c.id_cliente, cliente
            )
            SELECT cliente AS \"Cliente\", \"MediaTempoDias\"
            FROM clientes_media
            ORDER BY cliente;
        ";

        $dados = \DB::select($sql);

        $dadosFormatado = collect($dados)->map(function($item) {
            return [
                'Cliente' => $item->Cliente,
                'Media de Tempo em Dias' => $item->MediaTempoDias,
            ];
        });

        return response()->json($dadosFormatado);
    }

    public function proximaRevisaoPorPessoa()
    {
        $sql = " 
            WITH revisoes_ordenadas AS (
                SELECT
                    c.id_cliente,
                    c.nome || ' ' || c.sobrenome AS cliente,
                    r.data_inicio,
                    r.data_fim,
                    ROW_NUMBER() OVER (PARTITION BY c.id_cliente ORDER BY r.data_inicio) AS rn,
                    COUNT(*) OVER (PARTITION BY c.id_cliente) AS total_revisoes
                FROM revisoes r
                JOIN realizas s ON r.id_revisao = s.fk_revisao_id_revisao
                JOIN veiculos v ON s.fk_veiculo_placa = v.placa
                JOIN clientes c ON v.fk_cliente_id_cliente = c.id_cliente
                WHERE r.data_fim IS NOT NULL
            ),
            pares_revisao AS (
                SELECT
                    r1.id_cliente,
                    r1.cliente,
                    (r2.data_inicio::date - r1.data_fim::date) AS dias
                FROM revisoes_ordenadas r1
                JOIN revisoes_ordenadas r2
                    ON r1.id_cliente = r2.id_cliente AND r2.rn = r1.rn + 1
            ),
            medias_ultima_revisao AS (
                SELECT
                    ro.id_cliente,
                    ro.cliente,
                    MAX(ro.data_fim::date) AS ultima_data_fim,
                    COALESCE(ROUND(AVG(pr.dias)::numeric), 0) AS media_dias,
                    MAX(ro.total_revisoes) AS total_revisoes
                FROM revisoes_ordenadas ro
                LEFT JOIN pares_revisao pr ON ro.id_cliente = pr.id_cliente
                GROUP BY ro.id_cliente, ro.cliente
            )
            SELECT
                cliente AS \"Cliente\",
                CASE 
                    WHEN total_revisoes < 2 THEN NULL
                    ELSE (ultima_data_fim + (media_dias || ' days')::interval)::date
                END AS \"ProximaRevisao\"
            FROM medias_ultima_revisao
            ORDER BY cliente;
        ";

        $dados = \DB::select($sql);

        $dadosFormatado = collect($dados)->map(function($item) {
            return [
                'Cliente' => $item->Cliente,
                'Proxima Revisão' => $item->ProximaRevisao === null 
                    ? 'Apenas uma revisão realizada!' 
                    : \Carbon\Carbon::parse($item->ProximaRevisao)->format('d/m/Y'),
            ];
        });
        
        return response()->json($dadosFormatado);
    }
}