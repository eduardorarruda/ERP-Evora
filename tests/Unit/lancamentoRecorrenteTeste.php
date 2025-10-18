<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Helpers\LancamentoHelper;
use Carbon\Carbon;

class LancamentoRecorrenteTest extends TestCase
{
    /**
     * Testa cálculo de data de vencimento
     */
    public function testCalcularDataVencimento()
    {
        $dataBase = '2025-01-15';
        $diaVencimento = 10;
        $mesOffset = 1;

        $resultado = LancamentoHelper::calcularDataVencimento($dataBase, $diaVencimento, $mesOffset);
        
        $this->assertEquals('2025-02-10', $resultado);
    }

    /**
     * Testa ajuste para fim de mês
     */
    public function testCalcularDataVencimentoFimDeMes()
    {
        // Testando 31 de janeiro para fevereiro (28 dias)
        $dataBase = '2025-01-31';
        $diaVencimento = 31;
        $mesOffset = 1;

        $resultado = LancamentoHelper::calcularDataVencimento($dataBase, $diaVencimento, $mesOffset);
        
        $this->assertEquals('2025-02-28', $resultado);
    }

    /**
     * Testa geração de número de documento
     */
    public function testGerarNumeroDocumento()
    {
        $numeroBase = '101510';
        $sequencia = 5;

        $resultado = LancamentoHelper::gerarNumeroDocumento($numeroBase, $sequencia);
        
        $this->assertEquals('101510-5', $resultado);
    }

    /**
     * Testa geração com separador customizado
     */
    public function testGerarNumeroDocumentoComSeparadorCustomizado()
    {
        $numeroBase = '101510';
        $sequencia = 3;
        $separador = '/';

        $resultado = LancamentoHelper::gerarNumeroDocumento($numeroBase, $sequencia, $separador);
        
        $this->assertEquals('101510/3', $resultado);
    }

    /**
     * Testa validação de quantidade de meses - válido
     */
    public function testValidarQuantidadeMesesValido()
    {
        $this->assertTrue(LancamentoHelper::validarQuantidadeMeses(12));
        $this->assertTrue(LancamentoHelper::validarQuantidadeMeses(1));
        $this->assertTrue(LancamentoHelper::validarQuantidadeMeses(60));
    }

    /**
     * Testa validação de quantidade de meses - inválido
     */
    public function testValidarQuantidadeMesesInvalido()
    {
        $this->assertFalse(LancamentoHelper::validarQuantidadeMeses(0));
        $this->assertFalse(LancamentoHelper::validarQuantidadeMeses(-5));
        $this->assertFalse(LancamentoHelper::validarQuantidadeMeses(61));
        $this->assertFalse(LancamentoHelper::validarQuantidadeMeses(100));
    }

    /**
     * Testa cálculo de total
     */
    public function testCalcularTotal()
    {
        $lancamentos = [
            ['valor' => 100.00],
            ['valor' => 200.50],
            ['valor' => 150.75]
        ];

        $total = LancamentoHelper::calcularTotal($lancamentos);
        
        $this->assertEquals(451.25, $total);
    }

    /**
     * Testa validação de lançamento válido
     */
    public function testValidarLancamentoValido()
    {
        $lancamento = [
            'numero_documento' => '101510-1',
            'data_vencimento' => '2025-12-10',
            'valor' => '500.00'
        ];

        $resultado = LancamentoHelper::validarLancamento($lancamento);
        
        $this->assertTrue($resultado['valido']);
        $this->assertEmpty($resultado['erros']);
    }

    /**
     * Testa validação de lançamento inválido
     */
    public function testValidarLancamentoInvalido()
    {
        $lancamento = [
            'numero_documento' => '',
            'data_vencimento' => 'data-invalida',
            'valor' => '0'
        ];

        $resultado = LancamentoHelper::validarLancamento($lancamento);
        
        $this->assertFalse($resultado['valido']);
        $this->assertNotEmpty($resultado['erros']);
        $this->assertCount(3, $resultado['erros']);
    }

    /**
     * Testa geração de resumo
     */
    public function testGerarResumo()
    {
        $lancamentos = [
            [
                'numero_documento' => '101510-1',
                'data_vencimento' => '2025-10-10',
                'valor' => 100.00
            ],
            [
                'numero_documento' => '101510-2',
                'data_vencimento' => '2025-11-10',
                'valor' => 200.00
            ],
            [
                'numero_documento' => '101510-3',
                'data_vencimento' => '2025-12-10',
                'valor' => 300.00
            ]
        ];

        $resumo = LancamentoHelper::gerarResumo($lancamentos);
        
        $this->assertEquals(3, $resumo['quantidade']);
        $this->assertEquals(600.00, $resumo['total']);
        $this->assertEquals('2025-10-10', $resumo['primeiro_vencimento']);
        $this->assertEquals('2025-12-10', $resumo['ultimo_vencimento']);
        $this->assertEquals(200.00, $resumo['valor_medio']);
    }

    /**
     * Testa formatação de lançamentos
     */
    public function testFormatarLancamentos()
    {
        $lancamentos = [
            [
                'numero_documento' => '101510-1',
                'data_vencimento' => '2025-10-10',
                'valor' => 500.00,
                'status' => 'Pendente'
            ]
        ];

        $formatados = LancamentoHelper::formatarLancamentos($lancamentos);
        
        $this->assertEquals('101510-1', $formatados[0]['numero_documento']);
        $this->assertEquals('10/10/2025', $formatados[0]['data_vencimento']);
        $this->assertStringContainsString('500', $formatados[0]['valor']);
        $this->assertEquals('Pendente', $formatados[0]['status']);
    }

    /**
     * Testa ano bissexto
     */
    public function testCalcularDataVencimentoAnoBissexto()
    {
        // 2024 é ano bissexto
        $dataBase = '2024-01-31';
        $diaVencimento = 31;
        $mesOffset = 1;

        $resultado = LancamentoHelper::calcularDataVencimento($dataBase, $diaVencimento, $mesOffset);
        
        // Fevereiro de 2024 tem 29 dias
        $this->assertEquals('2024-02-29', $resultado);
    }

    /**
     * Testa sequência de múltiplos meses
     */
    public function testSequenciaMultiplosMeses()
    {
        $dataBase = '2025-01-15';
        $diaVencimento = 10;

        $datas = [];
        for ($i = 0; $i < 12; $i++) {
            $datas[] = LancamentoHelper::calcularDataVencimento($dataBase, $diaVencimento, $i);
        }

        $this->assertCount(12, $datas);
        $this->assertEquals('2025-01-10', $datas[0]);
        $this->assertEquals('2025-12-10', $datas[11]);
    }
}