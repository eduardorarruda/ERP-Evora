<?php

namespace App\Helpers;

use Carbon\Carbon;

/**
 * Helper para funcionalidades de Lançamentos Recorrentes
 */
class LancamentoHelper
{
    /**
     * Calcula a data de vencimento ajustando para fim de mês se necessário
     *
     * @param string $dataBase Data base no formato Y-m-d
     * @param int $diaVencimento Dia desejado (1-31)
     * @param int $mesOffset Offset de meses
     * @return string Data calculada no formato Y-m-d
     */
    public static function calcularDataVencimento($dataBase, $diaVencimento, $mesOffset)
    {
        $data = Carbon::parse($dataBase);
        $data->addMonths($mesOffset);
        
        // Obtém o último dia do mês
        $ultimoDiaDoMes = $data->copy()->endOfMonth()->day;
        
        // Ajusta o dia para não ultrapassar o fim do mês
        $dia = min($diaVencimento, $ultimoDiaDoMes);
        $data->day($dia);
        
        return $data->format('Y-m-d');
    }

    /**
     * Gera número de documento sequencial
     *
     * @param string $numeroBase Número base
     * @param int $sequencia Número da sequência
     * @param string $separador Separador (padrão: -)
     * @return string Número completo
     */
    public static function gerarNumeroDocumento($numeroBase, $sequencia, $separador = '-')
    {
        return $numeroBase . $separador . $sequencia;
    }

    /**
     * Valida se a quantidade de meses está dentro do limite
     *
     * @param int $quantidade Quantidade de meses
     * @param int $minimo Mínimo permitido (padrão: 1)
     * @param int $maximo Máximo permitido (padrão: 60)
     * @return bool
     */
    public static function validarQuantidadeMeses($quantidade, $minimo = 1, $maximo = 60)
    {
        return $quantidade >= $minimo && $quantidade <= $maximo;
    }

    /**
     * Formata array de lançamentos para exibição
     *
     * @param array $lancamentos Array de lançamentos
     * @return array Array formatado
     */
    public static function formatarLancamentos($lancamentos)
    {
        return array_map(function($lancamento) {
            return [
                'numero_documento' => $lancamento['numero_documento'],
                'data_vencimento' => Carbon::parse($lancamento['data_vencimento'])->format('d/m/Y'),
                'valor' => 'R$ ' . number_format($lancamento['valor'], 2, ',', '.'),
                'status' => $lancamento['status'] ?? 'Pendente'
            ];
        }, $lancamentos);
    }

    /**
     * Calcula o total de lançamentos
     *
     * @param array $lancamentos Array de lançamentos
     * @return float Total
     */
    public static function calcularTotal($lancamentos)
    {
        return array_reduce($lancamentos, function($carry, $lancamento) {
            $valor = is_numeric($lancamento['valor']) 
                ? $lancamento['valor'] 
                : floatval(str_replace(['.', ','], ['', '.'], $lancamento['valor']));
            return $carry + $valor;
        }, 0);
    }

    /**
     * Valida estrutura de lançamento
     *
     * @param array $lancamento Dados do lançamento
     * @return array ['valido' => bool, 'erros' => array]
     */
    public static function validarLancamento($lancamento)
    {
        $erros = [];

        if (empty($lancamento['numero_documento'])) {
            $erros[] = 'Número do documento é obrigatório';
        }

        if (empty($lancamento['data_vencimento'])) {
            $erros[] = 'Data de vencimento é obrigatória';
        } elseif (!self::validarData($lancamento['data_vencimento'])) {
            $erros[] = 'Data de vencimento inválida';
        }

        if (empty($lancamento['valor']) || floatval($lancamento['valor']) <= 0) {
            $erros[] = 'Valor deve ser maior que zero';
        }

        return [
            'valido' => empty($erros),
            'erros' => $erros
        ];
    }

    /**
     * Valida formato de data
     *
     * @param string $data Data no formato Y-m-d
     * @return bool
     */
    private static function validarData($data)
    {
        try {
            $d = Carbon::parse($data);
            return $d instanceof Carbon;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Gera relatório resumido de lançamentos
     *
     * @param array $lancamentos Array de lançamentos
     * @return array Resumo
     */
    public static function gerarResumo($lancamentos)
    {
        return [
            'quantidade' => count($lancamentos),
            'total' => self::calcularTotal($lancamentos),
            'primeiro_vencimento' => !empty($lancamentos) ? $lancamentos[0]['data_vencimento'] : null,
            'ultimo_vencimento' => !empty($lancamentos) ? end($lancamentos)['data_vencimento'] : null,
            'valor_medio' => count($lancamentos) > 0 
                ? self::calcularTotal($lancamentos) / count($lancamentos) 
                : 0
        ];
    }
}