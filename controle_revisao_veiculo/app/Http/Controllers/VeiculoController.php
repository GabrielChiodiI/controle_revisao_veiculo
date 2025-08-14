<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Veiculo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VeiculoController extends Controller
{
    public function indexByCliente(Cliente $cliente)
    {
        $veiculos = $cliente->veiculos()->orderBy('placa')->get();
        return Inertia::render('clientes/Veiculos', [
            'cliente' => $cliente,
            'veiculos' => $veiculos,
        ]);
    }

    public function storeForCliente(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'placa' => 'required|string|max:15',
            'marca' => 'required|string|max:20',
            'modelo' => 'required|string|max:20',
            'ano' => 'required|integer',
        ]);

        $data['fk_cliente_id_cliente'] = $cliente->id_cliente;
        Veiculo::create($data);

        
        return back(303)->with('success','Veículo adicionado.');
    }

    public function update(Request $request, \App\Models\Cliente $cliente, \App\Models\Veiculo $veiculo)
    {
        $data = $request->validate([
            'placa'  => 'required|string|max:15',
            'marca'  => 'required|string|max:20',
            'modelo' => 'required|string|max:20',
            'ano'    => 'required|integer',
        ]);

        // garantir vínculo correto
        $data['fk_cliente_id_cliente'] = $cliente->id_cliente;

        // se editar placa, atualize a PK (string)
        $veiculo->fill($data);
        $veiculo->save();

        return back(303)->with('success','Veículo atualizado.');
    }

    public function destroy(\App\Models\Cliente $cliente, \App\Models\Veiculo $veiculo)
    {
        // (opcional) validar que pertence ao cliente:
        if ($veiculo->fk_cliente_id_cliente !== $cliente->id_cliente) {
            abort(404);
        }
        $veiculo->delete();
        return back(303)->with('success','Veículo excluído.');
    }

}
