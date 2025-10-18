/**
 * Gerenciamento de Lançamentos Recorrentes
 */

$(function () {
    initLancamentoRecorrente();
});

function initLancamentoRecorrente() {
    // Evento do botão Gerar
    $("#btn-gerar").click(function () {
        gerarPrevia();
    });

    // Evento do botão Salvar
    $("#btn-salvar").click(function (e) {
        e.preventDefault();
        salvarLancamentos();
    });

    // Evento do botão Exportar
    $("#btn-exportar").click(function () {
        exportarJSON();
    });

    // Aplicar máscara de moeda
    $(".moeda").mask("#.##0,00", { reverse: true });
}

/**
 * Gera a prévia dos lançamentos recorrentes
 */
function gerarPrevia() {
    // Validar campos obrigatórios
    if (!validarCampos()) {
        return;
    }

    // Coletar dados do formulário
    const dados = {
        referencia: $("#inp-referencia").val(),
        data_movimento: $("#inp-data_movimento").val(),
        dia_vencimento: $("#inp-dia_vencimento").val(),
        valor_integral: $("#inp-valor_integral").val(),
        quantidade_meses: $("#inp-quantidade_meses").val(),
        vencimento_no_mes_atual: $(
            'input[name="vencimento_no_mes_atual"]:checked',
        ).val(),
        _token: $('input[name="_token"]').val(),
    };

    // Exibir loading
    Swal.fire({
        title: "Gerando lançamentos...",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });

    // Fazer requisição AJAX
    $.ajax({
        url: path_url + "lancamentoRecorrente/gerar-previa",
        method: "POST",
        data: dados,
        success: function (response) {
            Swal.close();

            if (response.success) {
                renderizarTabela(response.lancamentos);
                $("#btn-salvar").prop("disabled", false);
                $("#btn-exportar").prop("disabled", false);

                Swal.fire({
                    icon: "success",
                    title: "Sucesso!",
                    text: "Lançamentos gerados com sucesso!",
                    timer: 2000,
                    showConfirmButton: false,
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Erro",
                    text: response.message || "Erro ao gerar lançamentos",
                });
            }
        },
        error: function (xhr) {
            Swal.close();
            let mensagem = "Erro ao gerar lançamentos";

            if (xhr.responseJSON && xhr.responseJSON.message) {
                mensagem = xhr.responseJSON.message;
            }

            Swal.fire({
                icon: "error",
                title: "Erro",
                text: mensagem,
            });
        },
    });
}

/**
 * Valida os campos obrigatórios
 */
function validarCampos() {
    const camposObrigatorios = [
        { id: "#inp-referencia", nome: "Número Documento" },
        { id: "#inp-data_movimento", nome: "Data Movimento" },
        { id: "#inp-dia_vencimento", nome: "Dia Vencimento" },
        { id: "#inp-valor_integral", nome: "Valor" },
        { id: "#inp-quantidade_meses", nome: "Quantidade de Meses" },
        { id: "#inp-cliente_id", nome: "Cliente" },
        { id: "#inp-tipo_pagamento", nome: "Forma de Pagamento" },
        { id: "#inp-categoria_id", nome: "Categoria" },
    ];

    for (let campo of camposObrigatorios) {
        if (!$(campo.id).val()) {
            Swal.fire({
                icon: "warning",
                title: "Campo obrigatório",
                text: `O campo "${campo.nome}" é obrigatório!`,
            });
            $(campo.id).focus();
            return false;
        }
    }

    // Validar dia do vencimento
    const diaVencimento = parseInt($("#inp-dia_vencimento").val());
    if (diaVencimento < 1 || diaVencimento > 31) {
        Swal.fire({
            icon: "warning",
            title: "Valor inválido",
            text: "O dia de vencimento deve estar entre 1 e 31!",
        });
        $("#inp-dia_vencimento").focus();
        return false;
    }

    // Validar quantidade de meses
    const qtdMeses = parseInt($("#inp-quantidade_meses").val());
    if (qtdMeses < 1 || qtdMeses > 60) {
        Swal.fire({
            icon: "warning",
            title: "Valor inválido",
            text: "A quantidade de meses deve estar entre 1 e 60!",
        });
        $("#inp-quantidade_meses").focus();
        return false;
    }

    return true;
}

/**
 * Renderiza a tabela com os lançamentos gerados
 */
function renderizarTabela(lancamentos) {
    let html = `
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th style="width: 15%">Num Doc</th>
                        <th style="width: 20%">Forma de Pagamento</th>
                        <th style="width: 20%">Movimento</th>
                        <th style="width: 20%">Vencimento</th>
                        <th style="width: 15%">Valor</th>
                        <th style="width: 10%" class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody id="tbody-lancamentos">
    `;

    const formaPagamento = $("#inp-tipo_pagamento option:selected").text();

    lancamentos.forEach((lanc, index) => {
        html += `
            <tr data-index="${index}">
                <td>
                    <input type="text" 
                           class="form-control form-control-sm campo-editavel" 
                           data-field="numero_documento" 
                           value="${lanc.numero_documento}">
                </td>
                <td>${formaPagamento}</td>
                <td>
                    <input type="date" 
                           class="form-control form-control-sm campo-editavel" 
                           data-field="data_movimento" 
                           value="${lanc.data_vencimento}">
                </td>
                <td>
                    <input type="date" 
                           class="form-control form-control-sm campo-editavel" 
                           data-field="data_vencimento" 
                           value="${lanc.data_vencimento}">
                </td>
                <td>
                    <input type="text" 
                           class="form-control form-control-sm campo-editavel moeda-grid" 
                           data-field="valor" 
                           value="${lanc.valor}">
                </td>
                <td class="text-center">
                    <button type="button" 
                            class="btn btn-danger btn-sm btn-remover" 
                            data-index="${index}"
                            title="Remover lançamento">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
        <div class="alert alert-info mt-3">
            <i class="bx bx-info-circle"></i> 
            <strong>Total de lançamentos:</strong> ${lancamentos.length}
        </div>
    `;

    $("#tabela-container").html(html);

    // Aplicar máscara de moeda nos campos editáveis
    $(".moeda-grid").mask("#.##0,00", { reverse: true });

    // Evento para atualizar dados ao editar
    $(".campo-editavel").on("change", function () {
        atualizarLancamento($(this));
    });

    // Evento para remover linha
    $(".btn-remover").on("click", function () {
        removerLancamento($(this).data("index"));
    });

    // Armazenar dados iniciais
    window.lancamentosGerados = lancamentos;
}

/**
 * Atualiza os dados de um lançamento ao editar
 */
function atualizarLancamento($campo) {
    const index = $campo.closest("tr").data("index");
    const field = $campo.data("field");
    const valor = $campo.val();

    if (window.lancamentosGerados && window.lancamentosGerados[index]) {
        window.lancamentosGerados[index][field] = valor;
    }
}

/**
 * Remove um lançamento da lista
 */
function removerLancamento(index) {
    Swal.fire({
        title: "Confirmar remoção",
        text: "Deseja realmente remover este lançamento?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sim, remover!",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            // Remover do array
            if (window.lancamentosGerados) {
                window.lancamentosGerados.splice(index, 1);

                // Re-renderizar tabela
                renderizarTabela(window.lancamentosGerados);

                // Desabilitar botões se não houver mais lançamentos
                if (window.lancamentosGerados.length === 0) {
                    $("#btn-salvar").prop("disabled", true);
                    $("#btn-exportar").prop("disabled", true);
                }

                Swal.fire({
                    icon: "success",
                    title: "Removido!",
                    text: "Lançamento removido com sucesso.",
                    timer: 2000,
                    showConfirmButton: false,
                });
            }
        }
    });
}

/**
 * Salva os lançamentos no banco de dados
 */
function salvarLancamentos() {
    if (!window.lancamentosGerados || window.lancamentosGerados.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Atenção",
            text: "Não há lançamentos para salvar!",
        });
        return;
    }

    // Coletar dados atualizados da tabela
    const lancamentosAtualizados = [];
    $("#tbody-lancamentos tr").each(function () {
        const index = $(this).data("index");
        const lancamento = {
            numero_documento: $(this)
                .find('[data-field="numero_documento"]')
                .val(),
            data_movimento: $(this).find('[data-field="data_movimento"]').val(),
            data_vencimento: $(this)
                .find('[data-field="data_vencimento"]')
                .val(),
            valor: $(this).find('[data-field="valor"]').val(),
        };
        lancamentosAtualizados.push(lancamento);
    });

    // Atualizar campo hidden com JSON
    $("#lancamentos_json").val(JSON.stringify(lancamentosAtualizados));

    // Confirmar antes de salvar
    Swal.fire({
        title: "Confirmar salvamento",
        text: `Deseja salvar ${lancamentosAtualizados.length} lançamento(s)?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sim, salvar!",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            // Submeter formulário
            $("#form-lancamento-recorrente").submit();
        }
    });
}

/**
 * Exporta os lançamentos como JSON
 */
function exportarJSON() {
    if (!window.lancamentosGerados || window.lancamentosGerados.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Atenção",
            text: "Não há lançamentos para exportar!",
        });
        return;
    }

    // Coletar dados atualizados
    const lancamentosAtualizados = [];
    $("#tbody-lancamentos tr").each(function () {
        const lancamento = {
            numero_documento: $(this)
                .find('[data-field="numero_documento"]')
                .val(),
            data_movimento: $(this).find('[data-field="data_movimento"]').val(),
            data_vencimento: $(this)
                .find('[data-field="data_vencimento"]')
                .val(),
            valor: $(this).find('[data-field="valor"]').val(),
        };
        lancamentosAtualizados.push(lancamento);
    });

    // Criar arquivo JSON para download
    const dataStr = JSON.stringify(lancamentosAtualizados, null, 2);
    const dataBlob = new Blob([dataStr], { type: "application/json" });

    const url = window.URL.createObjectURL(dataBlob);
    const link = document.createElement("a");
    link.href = url;
    link.download = `lancamentos_recorrentes_${Date.now()}.json`;

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    window.URL.revokeObjectURL(url);

    Swal.fire({
        icon: "success",
        title: "Exportado!",
        text: "Arquivo JSON baixado com sucesso.",
        timer: 2000,
        showConfirmButton: false,
    });
}
