<div class="row g-3">
    <!-- Tipo -->
    <div class="col-md-2">
        <label for="inp-tipo" class="form-label required">Tipo</label>
        <select class="form-select" name="tipo" id="inp-tipo" required>
            <option value="credito">Crédito</option>
            <option value="debito">Débito</option>
        </select>
    </div>

    <!-- Número Documento -->
    <div class="col-md-2">
        {!!Form::text('referencia', 'Número Documento')
        ->attrs(['id' => 'inp-referencia'])
        ->required()
        !!}
    </div>

    <!-- Data Movimento -->
    <div class="col-md-2">
        {!!Form::date('data_movimento', 'Data Movimento')
        ->attrs(['id' => 'inp-data_movimento'])
        ->value(date('Y-m-d'))
        ->required()
        !!}
    </div>

    <!-- Dia Vencimento -->
    <div class="col-md-2">
        {!!Form::tel('dia_vencimento', 'Dia Vencimento')
        ->attrs([
        'id' => 'inp-dia_vencimento',
        'type' => 'number',
        'min' => '1',
        'max' => '31',
        'placeholder' => '10',
        'pattern' => '[0-9]*',
        'inputmode' => 'numeric'
        ])
        ->required()
        !!}
    </div>

    <!-- Valor -->
    <div class="col-md-2">
        {!!Form::tel('valor_integral', 'Valor')
        ->attrs([
        'class' => 'moeda form-control',
        'id' => 'inp-valor_integral'
        ])
        ->required()
        !!}
    </div>

    <!-- Quantidade de Meses -->
    <div class="col-md-2">
        {!!Form::tel('quantidade_meses', 'Quantidade de Meses')
        ->attrs([
        'id' => 'inp-quantidade_meses',
        'type' => 'number',
        'min' => '1',
        'max' => '60',
        'value' => '5',
        'pattern' => '[0-9]*',
        'inputmode' => 'numeric'
        ])
        ->required()
        !!}
    </div>

    <!-- Cliente -->
    <div class="col-md-6">
        <label for="inp-cliente_id" class="form-label required">Cliente</label>
        <div class="input-group">
            <select class="form-control select2" name="cliente_id" id="inp-cliente_id" required>
                <option value="">Selecione...</option>
                @foreach($clientes as $cliente)
                <option value="{{ $cliente->id }}">{{ $cliente->razao_social }}</option>
                @endforeach
            </select>
            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#modal-cliente">
                <i class="bx bx-plus"></i>
            </button>
        </div>
    </div>

    <!-- Fornecedor (opcional, dependendo do tipo) -->
    <div class="col-md-6" id="fornecedor-container" style="display: none;">
        <label for="inp-fornecedor_id" class="form-label">Fornecedor</label>
        <div class="input-group">
            <select class="form-control" name="fornecedor_id" id="inp-fornecedor_id">
                <option value="">Selecione...</option>
            </select>
            <button class="btn btn-primary" type="button">
                <i class="bx bx-plus"></i>
            </button>
        </div>
    </div>

    <!-- Forma de Pagamento -->
    <div class="col-md-3">
        {!!Form::select('tipo_pagamento', 'Forma de Pagamento',
        App\Models\ContaReceber::tiposPagamento())
        ->attrs(['class' => 'form-select', 'id' => 'inp-tipo_pagamento'])
        ->required()
        !!}
    </div>

    <!-- Conta Gerencial 
    <div class="col-md-3">
        <label for="inp-conta_gerencial" class="form-label">Conta Gerencial</label>
        <select class="form-select" name="conta_gerencial" id="inp-conta_gerencial">
            <option value="">Selecione...</option>
        </select>
    </div>-->

    <!-- Centro de Custo 
    <div class="col-md-3">
        <label for="inp-centro_custo" class="form-label">Centro de Custo</label>
        <select class="form-select" name="centro_custo" id="inp-centro_custo">
            <option value="">Selecione...</option>
        </select>
    </div>-->

    <!-- Vendedor 
    <div class="col-md-3">
        <label for="inp-vendedor" class="form-label">Vendedor</label>
        <select class="form-select" name="vendedor" id="inp-vendedor">
            <option value="">Selecione...</option>
        </select>
    </div>-->

    <!-- Categoria -->
    <div class="col-md-12">
        {!!Form::select('categoria_id', 'Categoria',
        $categorias->pluck('nome', 'id')->all())
        ->attrs(['class' => 'form-select', 'id' => 'inp-categoria_id'])
        ->required()
        !!}
    </div>

    <!-- Separador -->
    <div class="col-12">
        <hr class="my-4">
    </div>

    <!-- Data de Vencimento Inicial no Mês Atual? -->
    <div class="col-md-12">
        <label class="form-label">Data de Vencimento Inicial no Mês Atual?</label>
        <div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="vencimento_no_mes_atual" id="vencimento_sim"
                    value="1" checked>
                <label class="form-check-label" for="vencimento_sim">Sim</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="vencimento_no_mes_atual" id="vencimento_nao"
                    value="0">
                <label class="form-check-label" for="vencimento_nao">Não</label>
            </div>
        </div>
    </div>

    <!-- Botão Incluir (Temporariamente oculto, substituído pelo Gerar no header) -->
    <div class="col-12 d-none">
        <button type="button" class="btn btn-secondary" id="btn-incluir">
            <i class="bx bx-plus"></i> Incluir
        </button>
    </div>

    <!-- Separador antes da tabela -->
    <div class="col-12">
        <hr class="my-4">
    </div>

    <!-- Grid/Tabela de Lançamentos Gerados -->
    <div class="col-12">
        <div id="tabela-container">
            <!-- A tabela será inserida aqui pelo JavaScript -->
        </div>
    </div>

    <!-- Botão Salvar -->
    <div class="col-12">
        <div class="d-flex justify-content-start gap-2 mt-3">
            <button type="submit" class="btn btn-primary px-5" id="btn-salvar" disabled>
                <i class="bx bx-save"></i> Salvar
            </button>
            <button type="button" class="btn btn-success px-4" id="btn-exportar" disabled>
                <i class="bx bx-download"></i> Exportar JSON
            </button>
            <a href="{{ route('conta-receber.index') }}" class="btn btn-light px-4">
                <i class="bx bx-arrow-back"></i> Cancelar
            </a>
        </div>
    </div>
</div>