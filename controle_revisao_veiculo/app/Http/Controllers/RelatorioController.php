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

    public function pessoasPorSexoComMediaIdade()
    {
        return Cliente::select('sexo', DB::raw('COUNT(*) as total'), DB::raw('AVG(EXTRACT(YEAR FROM age(data_nascimento))) as media_idade'))
            ->groupBy('sexo')
            ->get();
    }

    public function porPessoa()
    {
        return Veiculo::with('cliente')
            ->join('clientes', 'veiculos.fk_cliente_id_cliente', '=', 'clientes.id_cliente')
            ->orderBy('clientes.nome')
            ->get();
    }

    public function maisVeiculosPorSexo()
    {
        $result = Cliente::select('sexo', DB::raw('COUNT(veiculos.placa) as total'))
            ->join('veiculos', 'clientes.id_cliente', '=', 'veiculos.fk_cliente_id_cliente')
            ->groupBy('sexo')
            ->orderByDesc('total')
            ->get();

        // Retorna só quem tem mais veículos
        return $result->first();
    }

    public function marcasOrdenadas()
    {
        return Veiculo::select('marca', DB::raw('COUNT(*) as total'))
            ->groupBy('marca')
            ->orderByDesc('total')
            ->get();
    }

    public function marcasPorSexo()
    {
        return Veiculo::select('marca', 'clientes.sexo', DB::raw('COUNT(*) as total'))
            ->join('clientes', 'veiculos.fk_cliente_id_cliente', '=', 'clientes.id_cliente')
            ->groupBy('marca', 'clientes.sexo')
            ->orderByDesc('total')
            ->get();
    }

    // --- Métodos de relatórios de revisões ---
    public function revisoesPorPeriodo(Request $request)
    {
        $inicio = $request->query('inicio');
        $fim = $request->query('fim');
        return Revisao::whereBetween('data_inicio', [$inicio, $fim])->get();
    }

    public function marcasMaisRevisoes()
    {
        return DB::table('revisoes')
            ->join('realizas', 'revisoes.id_revisao', '=', 'realizas.fk_revisao_id_revisao')
            ->join('veiculos', 'realizas.fk_veiculo_placa', '=', 'veiculos.placa')
            ->select('veiculos.marca', DB::raw('COUNT(*) as total'))
            ->groupBy('veiculos.marca')
            ->orderByDesc('total')
            ->get();
    }

    public function pessoasMaisRevisoes()
    {
        return DB::table('revisoes')
            ->join('realizas', 'revisoes.id_revisao', '=', 'realizas.fk_revisao_id_revisao')
            ->join('veiculos', 'realizas.fk_veiculo_placa', '=', 'veiculos.placa')
            ->join('clientes', 'veiculos.fk_cliente_id_cliente', '=', 'clientes.id_cliente')
            ->select('clientes.nome', 'clientes.sobrenome', DB::raw('COUNT(*) as total'))
            ->groupBy('clientes.id_cliente', 'clientes.nome', 'clientes.sobrenome')
            ->orderByDesc('total')
            ->get();
    }

    public function mediaTempoEntreRevisoesPorPessoa($idCliente)
    {
        $datas = DB::table('revisoes')
            ->join('realizas', 'revisoes.id_revisao', '=', 'realizas.fk_revisao_id_revisao')
            ->join('veiculos', 'realizas.fk_veiculo_placa', '=', 'veiculos.placa')
            ->where('veiculos.fk_cliente_id_cliente', $idCliente)
            ->orderBy('revisoes.data_inicio')
            ->pluck('revisoes.data_inicio');

        if (count($datas) < 2) return null;

        $intervalos = [];
        for ($i = 1; $i < count($datas); $i++) {
            $dt1 = Carbon::parse($datas[$i - 1]);
            $dt2 = Carbon::parse($datas[$i]);
            $intervalos[] = $dt2->diffInDays($dt1);
        }
        return array_sum($intervalos) / count($intervalos);
    }

    public function proximaRevisaoPorPessoa($idCliente)
    {
        $datas = DB::table('revisoes')
            ->join('realizas', 'revisoes.id_revisao', '=', 'realizas.fk_revisao_id_revisao')
            ->join('veiculos', 'realizas.fk_veiculo_placa', '=', 'veiculos.placa')
            ->where('veiculos.fk_cliente_id_cliente', $idCliente)
            ->orderBy('revisoes.data_inicio')
            ->pluck('revisoes.data_inicio');

        if (count($datas) < 2) return null;

        $intervalos = [];
        for ($i = 1; $i < count($datas); $i++) {
            $dt1 = Carbon::parse($datas[$i - 1]);
            $dt2 = Carbon::parse($datas[$i]);
            $intervalos[] = $dt2->diffInDays($dt1);
        }
        $media = array_sum($intervalos) / count($intervalos);

        $ultima = Carbon::parse($datas->last());
        $proxima = $ultima->addDays(round($media));
        return $proxima->toDateString();
    }
}