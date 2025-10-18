@extends('default.layout',['title' => 'Lançamento Financeiro Recorrente'])
@section('content')
<div class="page-content">
    <div class="card border-top border-0 border-4 border-primary">
        <div class="card-body p-5">
            <!-- Botão Gerar no topo direito -->
            <div class="page-breadcrumb d-sm-flex align-items-center mb-3">
                <div class="ms-auto">
                    <button type="button" class="btn btn-primary" id="btn-gerar-lancamentos">
                        <i class="bx bx-save"></i> Gerar
                    </button>
                </div>
            </div>

            <!-- Título da página -->
            <div class="card-title d-flex align-items-center mb-4">
                <div
                    style="width: 40px; height: 40px; background-color: #0d6efd; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                    <i class="bx bx-file text-white" style="font-size: 20px;"></i>
                </div>
                <h5 class="mb-0">Lançamento Financeiro Recorrente</h5>
            </div>

            <!-- Formulário -->
            <form method="POST" action="{{ route('lancamento-recorrente.store') }}" id="form-lancamento">
                @csrf

                <!-- Campo oculto para armazenar o JSON dos lançamentos -->
                <input type="hidden" name="lancamentos_json" id="lancamentos_json">

                <div class="row g-3">
                    <!-- Tipo (Read-only) -->
                    <div class="col-md-2">
                        <label class="form-label">Tipo</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="Crédito" readonly
                                style="background-color: #e9ecef;">
                            <span class="input-group-text" style="background-color: #e9ecef; border-left: 0;">
                                <i class="bx bx-chevron-right"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Número Documento -->
                    <div class="col-md-2">
                        <label class="form-label">Número Documento*</label>
                        <input type="text" name="numero_documento" id="numero_documento" class="form-control"
                            placeholder="101510" required>
                    </div>

                    <!-- Data Movimento -->
                    <div class="col-md-2">
                        <label class="form-label">Data Movimento*</label>
                        <input type="date" name="data_movimento" id="data_movimento" class="form-control" required>
                    </div>

                    <!-- Dia Vencimento -->
                    <div class="col-md-2">
                        <label class="form-label">Dia Vencimento*</label>
                        <input type="number" name="dia_vencimento" id="dia_vencimento" class="form-control"
                            placeholder="10" min="1" max="31" required>
                    </div>

                    <!-- Valor -->
                    <div class="col-md-2">
                        <label class="form-label">Valor*</label>
                        <input type="text" name="valor_integral" id="valor_integral" class="form-control moeda"
                            placeholder="500.000,00" required>
                    </div>

                    <!-- Quantidade de Meses -->
                    <div class="col-md-2">
                        <label class="form-label">Quantidade de Meses*</label>
                        <input type="number" name="quantidade_meses" id="quantidade_meses" class="form-control"
                            value="5" placeholder="5" min="1" required>
                    </div>

                    <!-- Cliente -->
                    <div class="col-md-6">
                        <label for="inp-cliente_id" class="form-label required">Cliente</label>
                        <div class="input-group">
                            <select class="form-control select2" name="cliente_id" id="inp-cliente_id" required>
                                <option value="">Selecione um cliente</option>
                            </select>
                            <button class="btn btn-danger" type="button" id="btn-limpar-cliente">
                                <i class="bx bx-trash"></i>
                            </button>
                            <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                                data-bs-target="#modal-cliente">
                                <i class="bx bx-search"></i>
                            </button>
                            <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                                data-bs-target="#modal-cliente">
                                <i class="bx bx-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6"></div>

                    <!-- Forma de Pagamento -->
                    <div class="col-md-3">
                        <label class="form-label">Forma de Pagamento</label>
                        <select class="form-select" name="tipo_pagamento" id="tipo_pagamento">
                            <option value="boleto">BOLETO BANCÁRIO</option>
                            <option value="dinheiro">DINHEIRO</option>
                            <option value="pix">PIX</option>
                            <option value="cartao_credito">CARTÃO DE CRÉDITO</option>
                            <option value="cartao_debito">CARTÃO DE DÉBITO</option>
                        </select>
                    </div>

                    <!-- Conta Gerencial (Campo vazio) 
                    <div class="col-md-3">
                        <label class="form-label">Conta Gerencial</label>
                        <div class="input-group">
                            <input type="text" name="conta_gerencial" class="form-control" readonly>
                            <span class="input-group-text" style="border-left: 0;">
                                <i class="bx bx-chevron-right"></i>
                            </span>
                        </div>
                    </div> -->

                    <!-- Centro de Custo (Campo vazio) 
                    <div class="col-md-3">
                        <label class="form-label">Centro de Custo</label>
                        <div class="input-group">
                            <input type="text" name="centro_custo" class="form-control" readonly>
                            <span class="input-group-text" style="border-left: 0;">
                                <i class="bx bx-chevron-right"></i>
                            </span>
                        </div>
                    </div>-->

                    <!-- Vendedor (Campo vazio) 
                    <div class="col-md-3">
                        <label class="form-label">Vendedor</label>
                        <div class="input-group">
                            <input type="text" name="vendedor" class="form-control" readonly>
                            <span class="input-group-text" style="border-left: 0;">
                                <i class="bx bx-chevron-right"></i>
                            </span>
                        </div>
                    </div>-->

                    <!-- Data de Vencimento Inicial no Mês Atual -->
                    <div class="col-12 mt-4">
                        <label class="form-label">Data de Vencimento Inicial no Mês Atual?</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="vencimento_no_mes_atual"
                                    id="vencimento_sim" value="1" checked>
                                <label class="form-check-label" for="vencimento_sim">Sim</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="vencimento_no_mes_atual"
                                    id="vencimento_nao" value="0">
                                <label class="form-check-label" for="vencimento_nao">Não</label>
                            </div>
                        </div>
                    </div>

                    <!-- Botão Incluir -->
                    <div class="col-12 mt-3">
                        <button type="button" class="btn btn-primary" id="btn-incluir">
                            <i class="bx bx-plus-circle"></i> Incluir
                        </button>
                        <button type="button" class="btn btn-secondary ms-2" id="btn-limpar-tabela">
                            <i class="bx bx-eraser"></i> Limpar Tabela
                        </button>
                    </div>
                </div>
            </form>

            <!-- Área do JSON Gerado (Debug) -->
            <div class="row mt-4" id="json-preview" style="display: none;">
                <div class="col-12">
                    <div class="alert alert-info">
                        <h6><i class="bx bx-code-alt"></i> JSON Gerado (Preview):</h6>
                        <pre id="json-content"
                            style="max-height: 200px; overflow-y: auto; background: #f8f9fa; padding: 10px; border-radius: 5px;"></pre>
                        <button type="button" class="btn btn-sm btn-secondary mt-2" id="btn-copiar-json">
                            <i class="bx bx-copy"></i> Copiar JSON
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabela de Lançamentos Gerados (Editável) -->
            <div class="row mt-4" id="area-tabela" style="display: none;">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0"><i class="bx bx-table"></i> Lançamentos Gerados (Editável)</h6>
                        <span class="badge bg-primary" id="total-lancamentos">0 lançamentos</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tabela-lancamentos">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 15%;">Num Doc</th>
                                    <th style="width: 20%;">Forma de Pagamento</th>
                                    <th style="width: 15%;">Movimento</th>
                                    <th style="width: 15%;">Vencimento</th>
                                    <th style="width: 15%;">Valor</th>
                                    <th style="width: 10%;">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-lancamentos">
                                <!-- As linhas serão geradas dinamicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Botão de deletar na parte inferior -->
            <div class="row mt-3" id="area-acoes" style="display: none;">
                <div class="col-12 text-end">
                    <button type="button" class="btn btn-danger" id="btn-deletar-selecionados">
                        <i class="bx bx-trash"></i> Deletar Selecionados
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@include('modals._client', ['not_submit' => true])

@endsection

@section('js')
<script type="text/javascript">
// Array global para armazenar os lançamentos
let lancamentosData = [];

$(document).ready(function() {
    // Inicializa Select2 para cliente
    $('#inp-cliente_id').select2({
        minimumInputLength: 2,
        language: "pt-BR",
        placeholder: "Digite para buscar o cliente",
        width: "100%",
        theme: 'bootstrap4',
        ajax: {
            cache: true,
            url: path_url + 'api/buscaClientes',
            dataType: "json",
            data: function(params) {
                return {
                    pesquisa: params.term,
                };
            },
            processResults: function(response) {
                var results = [];
                $.each(response, function(i, v) {
                    var o = {};
                    o.id = v.id;
                    o.text = v.razao_social + " - Código: " + v.id;
                    o.value = v.id;
                    results.push(o);
                });
                return {
                    results: results
                };
            }
        }
    });

    // Aplica máscara de moeda
    $('.moeda').mask('#.##0,00', {
        reverse: true
    });

    // Botão Limpar Cliente
    $('#btn-limpar-cliente').click(function() {
        $('#inp-cliente_id').val(null).trigger('change');
    });

    // Botão Incluir - Gera o JSON e popula a tabela
    $('#btn-incluir').click(function() {
        gerarLancamentos();
    });

    // Botão Limpar Tabela
    $('#btn-limpar-tabela').click(function() {
        if (confirm('Deseja realmente limpar todos os lançamentos gerados?')) {
            limparTabela();
        }
    });

    // Botão Gerar - Submete o formulário
    $('#btn-gerar-lancamentos').click(function() {
        if (lancamentosData.length === 0) {
            swal("Atenção", "Clique em 'Incluir' primeiro para gerar os lançamentos!", "warning");
            return;
        }

        // Valida se o cliente foi selecionado
        if (!$('#inp-cliente_id').val()) {
            swal("Atenção", "Selecione um cliente antes de gerar!", "warning");
            return;
        }

        // Atualiza o JSON com os dados da tabela (caso tenha sido editado)
        atualizarJsonDaTabela();

        // Armazena o JSON no campo oculto
        $('#lancamentos_json').val(JSON.stringify(lancamentosData));

        // Submete o formulário
        swal({
            title: "Confirmar Geração",
            text: `Serão gerados ${lancamentosData.length} lançamentos. Confirmar?`,
            icon: "info",
            buttons: ["Cancelar", "Confirmar"],
            dangerMode: false,
        }).then((confirmar) => {
            if (confirmar) {
                $('#form-lancamento').submit();
            }
        });
    });

    // Botão Copiar JSON
    $('#btn-copiar-json').click(function() {
        const jsonText = $('#json-content').text();
        navigator.clipboard.writeText(jsonText).then(() => {
            swal("Sucesso", "JSON copiado para a área de transferência!", "success");
        });
    });

    // Botão Deletar Selecionados
    $('#btn-deletar-selecionados').click(function() {
        deletarSelecionados();
    });

    /**
     * Função para gerar os lançamentos e criar o JSON
     */
    function gerarLancamentos() {
        // Coleta os dados do formulário
        const numeroDoc = $('#numero_documento').val().trim();
        const dataMovimento = $('#data_movimento').val();
        const diaVencimento = parseInt($('#dia_vencimento').val());
        const valor = $('#valor_integral').val();
        const quantidadeMeses = parseInt($('#quantidade_meses').val());
        const vencimentoMesAtual = $('input[name="vencimento_no_mes_atual"]:checked').val();
        const tipoPagamento = $('#tipo_pagamento option:selected').text();

        // Validação básica
        if (!numeroDoc || !dataMovimento || !diaVencimento || !valor || !quantidadeMeses) {
            swal("Atenção", "Preencha todos os campos obrigatórios!", "warning");
            return;
        }

        if (quantidadeMeses < 1 || quantidadeMeses > 120) {
            swal("Atenção", "A quantidade de meses deve estar entre 1 e 120!", "warning");
            return;
        }

        // Limpa o array de lançamentos
        lancamentosData = [];

        // Data base para cálculo
        let dataBase = new Date(dataMovimento + 'T00:00:00');

        // Se não for para iniciar no mês atual, avança um mês
        if (vencimentoMesAtual === '0') {
            dataBase.setMonth(dataBase.getMonth() + 1);
        }

        // Gera os lançamentos
        for (let i = 0; i < quantidadeMeses; i++) {
            // Calcula a data de vencimento
            let dataVenc = new Date(dataBase);
            dataVenc.setMonth(dataBase.getMonth() + i);
            dataVenc.setDate(diaVencimento);

            // Ajusta se o dia não existir no mês (ex: 31 de fevereiro)
            if (dataVenc.getDate() !== diaVencimento) {
                dataVenc.setDate(0); // Vai para o último dia do mês anterior
            }

            // Cria o objeto de lançamento
            const lancamento = {
                id: i + 1,
                numero_documento: `${numeroDoc}/${i + 1}`,
                forma_pagamento: tipoPagamento,
                data_movimento: dataMovimento,
                data_vencimento: formatarDataISO(dataVenc),
                valor: valor,
                selecionado: false
            };

            lancamentosData.push(lancamento);
        }

        // Exibe o JSON gerado
        exibirJSON();

        // Popula a tabela
        popularTabela();

        // Exibe as áreas
        $('#json-preview').fadeIn();
        $('#area-tabela').fadeIn();
        $('#area-acoes').fadeIn();

        // Mensagem de sucesso
        swal("Sucesso", `${quantidadeMeses} lançamentos gerados com sucesso!`, "success");
    }

    /**
     * Função para exibir o JSON gerado
     */
    function exibirJSON() {
        const jsonFormatado = JSON.stringify({
            quantidade_meses: lancamentosData.length,
            lancamentos: lancamentosData
        }, null, 2);

        $('#json-content').text(jsonFormatado);
    }

    /**
     * Função para popular a tabela com os lançamentos
     */
    function popularTabela() {
        const tbody = $('#tbody-lancamentos');
        tbody.empty();

        lancamentosData.forEach((lancamento, index) => {
            const row = criarLinhaEditavel(lancamento, index);
            tbody.append(row);
        });

        // Atualiza o contador
        $('#total-lancamentos').text(`${lancamentosData.length} lançamentos`);

        // Reaplica máscara nas células editáveis de valor
        $('.valor-editavel').mask('#.##0,00', {
            reverse: true
        });
    }

    /**
     * Função para criar uma linha editável da tabela
     */
    function criarLinhaEditavel(lancamento, index) {
        const rowClass = index % 2 === 0 ? 'table-light' : '';

        const row = `
            <tr class="${rowClass}" data-index="${index}">
                <td class="text-center">
                    <input type="checkbox" class="form-check-input checkbox-lancamento" data-index="${index}">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" value="${lancamento.numero_documento}" data-field="numero_documento" data-index="${index}">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" value="${lancamento.forma_pagamento}" data-field="forma_pagamento" data-index="${index}">
                </td>
                <td>
                    <input type="date" class="form-control form-control-sm" value="${lancamento.data_movimento}" data-field="data_movimento" data-index="${index}">
                </td>
                <td>
                    <input type="date" class="form-control form-control-sm" value="${lancamento.data_vencimento}" data-field="data_vencimento" data-index="${index}">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm valor-editavel" value="${lancamento.valor}" data-field="valor" data-index="${index}">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger btn-deletar-linha" data-index="${index}">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        return row;
    }

    /**
     * Atualiza o JSON com os dados editados da tabela
     */
    function atualizarJsonDaTabela() {
        $('#tbody-lancamentos tr').each(function(index) {
            const row = $(this);
            const dataIndex = row.data('index');

            if (lancamentosData[dataIndex]) {
                lancamentosData[dataIndex].numero_documento = row.find(
                    '[data-field="numero_documento"]').val();
                lancamentosData[dataIndex].forma_pagamento = row.find('[data-field="forma_pagamento"]')
                    .val();
                lancamentosData[dataIndex].data_movimento = row.find('[data-field="data_movimento"]')
                    .val();
                lancamentosData[dataIndex].data_vencimento = row.find('[data-field="data_vencimento"]')
                    .val();
                lancamentosData[dataIndex].valor = row.find('[data-field="valor"]').val();
            }
        });

        // Atualiza o preview do JSON
        exibirJSON();
    }

    /**
     * Event listener para edição inline (atualiza o JSON em tempo real)
     */
    $(document).on('change', '#tbody-lancamentos input', function() {
        const index = $(this).data('index');
        const field = $(this).data('field');
        const value = $(this).val();

        if (lancamentosData[index]) {
            lancamentosData[index][field] = value;
            exibirJSON();
        }
    });

    /**
     * Event listener para deletar linha individual
     */
    $(document).on('click', '.btn-deletar-linha', function() {
        const index = $(this).data('index');

        swal({
            title: "Confirmar Exclusão",
            text: "Deseja realmente deletar este lançamento?",
            icon: "warning",
            buttons: ["Cancelar", "Deletar"],
            dangerMode: true,
        }).then((confirmar) => {
            if (confirmar) {
                // Remove do array
                lancamentosData.splice(index, 1);

                // Recalcula os IDs
                lancamentosData.forEach((item, i) => {
                    item.id = i + 1;
                });

                // Atualiza a tabela
                popularTabela();
                exibirJSON();

                swal("Deletado!", "Lançamento removido com sucesso!", "success");
            }
        });
    });

    /**
     * Função para deletar lançamentos selecionados
     */
    function deletarSelecionados() {
        const selecionados = [];

        $('.checkbox-lancamento:checked').each(function() {
            selecionados.push($(this).data('index'));
        });

        if (selecionados.length === 0) {
            swal("Atenção", "Selecione ao menos um lançamento para deletar!", "warning");
            return;
        }

        swal({
            title: "Confirmar Exclusão",
            text: `Deseja realmente deletar ${selecionados.length} lançamento(s)?`,
            icon: "warning",
            buttons: ["Cancelar", "Deletar"],
            dangerMode: true,
        }).then((confirmar) => {
            if (confirmar) {
                // Remove do array (do maior para o menor para não afetar os índices)
                selecionados.sort((a, b) => b - a).forEach(index => {
                    lancamentosData.splice(index, 1);
                });

                // Recalcula os IDs
                lancamentosData.forEach((item, i) => {
                    item.id = i + 1;
                });

                // Atualiza a tabela
                popularTabela();
                exibirJSON();

                swal("Deletado!", `${selecionados.length} lançamento(s) removido(s) com sucesso!`,
                    "success");
            }
        });
    }

    /**
     * Função para limpar a tabela
     */
    function limparTabela() {
        lancamentosData = [];
        $('#tbody-lancamentos').empty();
        $('#json-preview').fadeOut();
        $('#area-tabela').fadeOut();
        $('#area-acoes').fadeOut();
        $('#json-content').text('');
        $('#total-lancamentos').text('0 lançamentos');
    }

    /**
     * Função auxiliar para formatar data no formato ISO (YYYY-MM-DD)
     */
    function formatarDataISO(data) {
        const ano = data.getFullYear();
        const mes = String(data.getMonth() + 1).padStart(2, '0');
        const dia = String(data.getDate()).padStart(2, '0');
        return `${ano}-${mes}-${dia}`;
    }

    /**
     * Função auxiliar para formatar data no formato BR (DD/MM/YYYY)
     */
    function formatarDataBR(dataISO) {
        const [ano, mes, dia] = dataISO.split('-');
        return `${dia}/${mes}/${ano}`;
    }
});
</script>

<style>
/* Estilos personalizados para a tabela editável */
#tabela-lancamentos tbody tr {
    transition: background-color 0.2s;
}

#tabela-lancamentos tbody tr:hover {
    background-color: #f0f8ff !important;
}

#tabela-lancamentos input.form-control-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    border-radius: 0.2rem;
}

#tabela-lancamentos input.form-control-sm:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.badge {
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
}

pre#json-content {
    font-size: 0.85rem;
    color: #333;
}

.btn-deletar-linha {
    padding: 0.25rem 0.5rem;
}

/* Destaque para linhas selecionadas */
#tabela-lancamentos tbody tr:has(.checkbox-lancamento:checked) {
    background-color: #fff3cd !important;
}
</style>
@endsection