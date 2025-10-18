<?php

namespace App\Http\Controllers;

use App\Models\Acessor;
use Illuminate\Http\Request;
use App\Models\CategoriaConta;
use App\Models\Cidade;
use App\Models\Cliente;
use App\Models\Funcionario;
use App\Models\GrupoCliente;
use App\Models\Pais;
use App\Models\ContaReceber;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LancamentoRecorrenteController extends Controller
{
    /**
     * Display the form to create recurring launches
     */
    public function index(Request $request)
    {
        $clientes = Cliente::where('empresa_id', $request->empresa_id)->get();
        $cidades = Cidade::all();
        $grupos = GrupoCliente::where('empresa_id', $request->empresa_id)->get();
        $funcionarios = Funcionario::where('empresa_id', $request->empresa_id)->get();
        $acessores = Acessor::where('empresa_id', $request->empresa_id)->get();
        $paises = Pais::all();
        $categorias = CategoriaConta::where('empresa_id', $request->empresa_id)
            ->where('tipo', 'receber')
            ->orderBy('nome')
            ->get();
        
        return view('lançamentos_recorrentes.create', compact(
            'categorias',
            'cidades',
            'paises',
            'grupos',
            'acessores',
            'funcionarios',
            'clientes'
        ));
    }

    /**
     * Store recurring launches
     */
    public function store(Request $request)
    {
        $this->_validate($request);
        
        try {
            DB::transaction(function () use ($request) {
                // Obter os dados do JSON de lançamentos gerados
                $lancamentos = json_decode($request->lancamentos_json, true);
                
                if (!$lancamentos || !is_array($lancamentos)) {
                    throw new \Exception('Nenhum lançamento foi gerado.');
                }

                // Processar cada lançamento
                foreach ($lancamentos as $lancamento) {
                    $data = [
                        'venda_id' => null,
                        'data_vencimento' => $lancamento['data_vencimento'],
                        'data_recebimento' => null,
                        'valor_integral' => __convert_value_bd($lancamento['valor']),
                        'valor_recebido' => 0,
                        'referencia' => $lancamento['numero_documento'],
                        'categoria_id' => $request->categoria_id,
                        'status' => 0, // Sempre pendente ao criar
                        'empresa_id' => $request->empresa_id,
                        'cliente_id' => $request->cliente_id,
                        'tipo_pagamento' => $request->tipo_pagamento,
                        'observacao' => $request->observacao ?? '',
                        'filial_id' => $request->filial_id == -1 ? null : $request->filial_id
                    ];
                    
                    ContaReceber::create($data);
                }
            });
            
            session()->flash("flash_sucesso", "Lançamentos recorrentes cadastrados com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_erro", "Algo deu errado: " . $e->getMessage());
            __saveLogError($e, request()->empresa_id);
        }
        
        return redirect()->route('conta-receber.index');
    }

    /**
     * Validate the request
     */
    private function _validate(Request $request)
    {
        $rules = [
            'cliente_id' => 'required',
            'referencia' => 'required',
            'valor_integral' => 'required',
            'data_vencimento' => 'required|date',
            'quantidade_meses' => 'required|integer|min:1|max:60',
            'categoria_id' => 'required',
            'tipo_pagamento' => 'required',
            'lancamentos_json' => 'required|json',
        ];
        
        $messages = [
            'referencia.required' => 'O campo referência é obrigatório.',
            'cliente_id.required' => 'O campo cliente é obrigatório.',
            'valor_integral.required' => 'O campo valor é obrigatório.',
            'data_vencimento.required' => 'O campo vencimento é obrigatório.',
            'quantidade_meses.required' => 'O campo quantidade de meses é obrigatório.',
            'quantidade_meses.min' => 'A quantidade mínima é 1 mês.',
            'quantidade_meses.max' => 'A quantidade máxima é 60 meses.',
            'categoria_id.required' => 'O campo categoria é obrigatório.',
            'tipo_pagamento.required' => 'O campo tipo de pagamento é obrigatório.',
            'lancamentos_json.required' => 'É necessário gerar os lançamentos antes de salvar.',
        ];
        
        $this->validate($request, $rules, $messages);
    }

    /**
     * Generate recurring launches preview (AJAX)
     */
    public function gerarPrevia(Request $request)
    {
        try {
            $numeroBase = $request->referencia;
            $quantidadeMeses = (int) $request->quantidade_meses;
            $dataVencimentoBase = Carbon::parse($request->data_vencimento);
            $valor = $request->valor_integral;
            $vencimentoNoMesAtual = $request->vencimento_no_mes_atual == '1';
            
            $lancamentos = [];
            
            // Definir data inicial baseado na opção escolhida
            $dataInicial = $vencimentoNoMesAtual 
                ? $dataVencimentoBase 
                : $dataVencimentoBase->copy()->addMonth();
            
            for ($i = 0; $i < $quantidadeMeses; $i++) {
                $dataVencimento = $dataInicial->copy()->addMonths($i);
                
                $lancamentos[] = [
                    'numero_documento' => $numeroBase . '-' . ($i + 1),
                    'data_vencimento' => $dataVencimento->format('Y-m-d'),
                    'valor' => $valor,
                    'status' => 'Pendente'
                ];
            }
            
            return response()->json([
                'success' => true,
                'lancamentos' => $lancamentos
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar prévia: ' . $e->getMessage()
            ], 400);
        }
    }
}