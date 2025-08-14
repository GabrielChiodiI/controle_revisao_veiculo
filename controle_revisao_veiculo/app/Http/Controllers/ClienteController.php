<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $clientes = \App\Models\Cliente::query()
            ->when($q, function ($qq) use ($q) {
                $p = "%{$q}%";
                $qq->where(function ($w) use ($p) {
                    $w->where('nome', 'ILIKE', $p)
                    ->orWhere('sobrenome', 'ILIKE', $p)
                    ->orWhereRaw("lower(coalesce(nome,'') || ' ' || coalesce(sobrenome,'')) ILIKE ?", [$p]);
                });
            })
            ->select(['id_cliente','nome','sobrenome'])
            ->orderBy('id_cliente','desc')
            ->simplePaginate(perPage: 10);

        return Inertia::render('clientes/Index', [
            'clientes' => $clientes,
            'q' => $q,
        ]);
    }

    public function show(Cliente $cliente)
    {
        return response()->json($cliente);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:50',
            'sobrenome' => 'required|string|max:50',
            'cpf' => 'required|string|max:11',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:50',
            'data_nascimento' => 'nullable|date',
            'sexo' => 'required|string|max:20',
        ]);

        \App\Models\Cliente::create($data);

        return back(303)->with('success', 'Cliente criado.');
    }

    public function update(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:50',
            'sobrenome' => 'required|string|max:50',
            'cpf' => 'required|string|max:11',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:50',
            'data_nascimento' => 'nullable|date',
            'sexo' => 'required|string|max:20',
        ]);

        $cliente->update($data);
        return back(303)->with('success', 'Cliente atualizado.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return back(303)->with('success', 'Cliente excluído.');
    }

}