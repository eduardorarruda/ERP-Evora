/**
 * Componente de Tabela Editável
 * Componente reutilizável para criar tabelas com edição inline
 *
 * @author Sistema de Gestão Financeira
 * @version 1.0.0
 */

class TabelaEditavel {
    /**
     * Construtor
     *
     * @param {Object} config Configurações da tabela
     * @param {string} config.containerId ID do container
     * @param {Array} config.colunas Definição das colunas
     * @param {Array} config.dados Dados iniciais
     * @param {Function} config.onChange Callback quando dados mudam
     * @param {Function} config.onRemove Callback quando linha é removida
     */
    constructor(config) {
        this.containerId = config.containerId;
        this.colunas = config.colunas || [];
        this.dados = config.dados || [];
        this.onChange = config.onChange || (() => {});
        this.onRemove = config.onRemove || (() => {});
        this.showTotal = config.showTotal !== false;
        this.totalColumn = config.totalColumn || null;
    }

    /**
     * Renderiza a tabela completa
     */
    render() {
        const container = document.getElementById(this.containerId);
        if (!container) {
            console.error(`Container ${this.containerId} não encontrado`);
            return;
        }

        if (this.dados.length === 0) {
            container.innerHTML = this._renderEmpty();
            return;
        }

        container.innerHTML = this._buildTable();
        this._bindEvents();
    }

    /**
     * Constrói HTML da tabela
     */
    _buildTable() {
        return `
            <div class="table-responsive">
                <table class="table table-bordered table-hover tabela-editavel">
                    <thead class="table-light">
                        ${this._buildHeader()}
                    </thead>
                    <tbody>
                        ${this._buildBody()}
                    </tbody>
                    ${this.showTotal ? this._buildFooter() : ""}
                </table>
            </div>
        `;
    }

    /**
     * Constrói cabeçalho da tabela
     */
    _buildHeader() {
        const headers = this.colunas
            .map(
                (col) =>
                    `<th width="${col.width || "auto"}" class="text-center">${
                        col.label
                    }</th>`,
            )
            .join("");

        return `<tr>${headers}<th width="5%" class="text-center">Ações</th></tr>`;
    }

    /**
     * Constrói corpo da tabela
     */
    _buildBody() {
        return this.dados
            .map((item, index) => this._buildRow(item, index))
            .join("");
    }

    /**
     * Constrói uma linha da tabela
     */
    _buildRow(item, index) {
        const cells = this.colunas
            .map((col) => `<td>${this._buildCell(col, item, index)}</td>`)
            .join("");

        return `
            <tr data-index="${index}">
                ${cells}
                <td class="text-center">
                    <button type="button" 
                        class="btn btn-sm btn-danger btn-remover-linha" 
                        data-index="${index}"
                        title="Remover">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    /**
     * Constrói uma célula baseado no tipo
     */
    _buildCell(coluna, item, index) {
        const value = item[coluna.field] || "";
        const dataAttr = `data-field="${coluna.field}" data-index="${index}"`;

        switch (coluna.type) {
            case "text":
                return `<input type="text" class="form-control form-control-sm editable-cell" 
                    value="${value}" ${dataAttr}>`;

            case "number":
                return `<input type="number" class="form-control form-control-sm editable-cell" 
                    value="${value}" ${dataAttr} 
                    min="${coluna.min || 0}" 
                    max="${coluna.max || ""}">`;

            case "date":
                return `<input type="date" class="form-control form-control-sm editable-cell" 
                    value="${value}" ${dataAttr}>`;

            case "money":
                return `<input type="text" class="form-control form-control-sm moeda editable-cell" 
                    value="${value}" ${dataAttr}>`;

            case "select":
                return this._buildSelect(coluna, value, index);

            case "badge":
                return `<span class="badge bg-${
                    coluna.badgeColor || "secondary"
                }">${value}</span>`;

            case "readonly":
                return `<span>${this._formatValue(value, coluna)}</span>`;

            default:
                return value;
        }
    }

    /**
     * Constrói um select
     */
    _buildSelect(coluna, value, index) {
        const options = (coluna.options || [])
            .map(
                (opt) =>
                    `<option value="${opt.value}" ${
                        opt.value === value ? "selected" : ""
                    }>
                ${opt.label}
            </option>`,
            )
            .join("");

        return `
            <select class="form-select form-select-sm editable-cell" 
                data-field="${coluna.field}" data-index="${index}">
                ${options}
            </select>
        `;
    }

    /**
     * Formata valor para exibição
     */
    _formatValue(value, coluna) {
        if (coluna.format) {
            return coluna.format(value);
        }
        return value;
    }

    /**
     * Constrói rodapé com totais
     */
    _buildFooter() {
        if (!this.totalColumn) return "";

        const total = this._calculateTotal();
        const colspan = this.colunas.findIndex(
            (col) => col.field === this.totalColumn,
        );

        return `
            <tfoot class="table-light">
                <tr>
                    <td colspan="${colspan}" class="text-end"><strong>Total:</strong></td>
                    <td><strong>${this._formatMoney(total)}</strong></td>
                    <td colspan="${this.colunas.length - colspan}"></td>
                </tr>
            </tfoot>
        `;
    }

    /**
     * Calcula total
     */
    _calculateTotal() {
        return this.dados.reduce((sum, item) => {
            const value = this._parseMoney(item[this.totalColumn]);
            return sum + value;
        }, 0);
    }

    /**
     * Renderiza estado vazio
     */
    _renderEmpty() {
        return '<p class="text-muted text-center py-4">Nenhum registro encontrado</p>';
    }

    /**
     * Vincula eventos
     */
    _bindEvents() {
        // Evento de edição
        $(".editable-cell").on("change blur", (e) => {
            const $input = $(e.target);
            const index = parseInt($input.data("index"));
            const field = $input.data("field");
            const value = $input.val();

            this.updateCell(index, field, value);
        });

        // Evento de remoção
        $(".btn-remover-linha").on("click", (e) => {
            const index = parseInt($(e.currentTarget).data("index"));
            this.removeRow(index);
        });

        // Aplicar máscaras
        $(".moeda").mask("#.##0,00", { reverse: true });
    }

    /**
     * Atualiza uma célula
     */
    updateCell(index, field, value) {
        if (this.dados[index]) {
            this.dados[index][field] = value;
            this.onChange(this.dados);
        }
    }

    /**
     * Remove uma linha
     */
    removeRow(index) {
        const that = this;

        swal({
            title: "Tem certeza?",
            text: "Deseja remover este registro?",
            icon: "warning",
            buttons: ["Cancelar", "Sim, remover"],
            dangerMode: true,
        }).then((confirmar) => {
            if (confirmar) {
                that.dados.splice(index, 1);
                that.onRemove(index, that.dados);
                that.render();
            }
        });
    }

    /**
     * Adiciona uma linha
     */
    addRow(item) {
        this.dados.push(item);
        this.render();
        this.onChange(this.dados);
    }

    /**
     * Obtém dados atuais
     */
    getDados() {
        return this.dados;
    }

    /**
     * Define novos dados
     */
    setDados(dados) {
        this.dados = dados;
        this.render();
    }

    /**
     * Limpa a tabela
     */
    clear() {
        this.dados = [];
        this.render();
    }

    /**
     * Utilitários de formatação
     */
    _formatMoney(value) {
        return (
            "R$ " +
            parseFloat(value).toLocaleString("pt-BR", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            })
        );
    }

    _parseMoney(value) {
        if (typeof value === "number") return value;
        return parseFloat(value.replace(/[^\d,-]/g, "").replace(",", ".")) || 0;
    }
}

// Exportar para uso global
window.TabelaEditavel = TabelaEditavel;
