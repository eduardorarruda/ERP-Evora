<div class="row g-3">
    <div class="col-md-2">
        {!!Form::text('referencia', 'Referência')->required()
        !!}
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="inp-cliente_id" class="required">Cliente</label>
            <div class="input-group">
                <select class="form-control" name="cliente_id" id="inp-cliente_id">
                    @isset($item)
                    <option value="{{$item->cliente_id}}">{{ $item->cliente->razao_social }}</option>
                    @endif
                </select>
                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#modal-cliente">
                    <i class="bx bx-plus"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        {!!Form::select('categoria_id', 'Categoria', $categorias->pluck('nome', 'id')->all())
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('valor_integral', 'Valor')->required()
        ->attrs(['class' => 'moeda'])
        ->value(isset($item) ? __moeda($item->valor_integral) : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::date('lançamentos_recorrentes', 'Vencimento')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('tipo_pagamento', 'Tipo de pagamento', App\Models\ContaReceber::tiposPagamento())
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        {!! Form::text('quantidade_meses', 'Quantidade de Meses')
        ->attrs([
        'class' => 'form-control',
        'pattern' => '[1-9][0-9]*',
        'inputmode' => 'numeric',
        'title' => 'Por favor, insira um número inteiro maior que 0.'
        ])
        ->value(1)
        ->required()
        !!}
    </div>

    <div class="col-12">
        <hr>
    </div>

    <div class="col-md-6">
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
        <div class="col-12">
            <button type="submit" class="btn btn-primary px-5 mt-3 float-start">Gerar</button>
        </div>
    </div>


    <hr>

    <div class="col-12 mt-4">
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="tabela-contas-geradas">
                <thead class="table-dark">
                    <tr>
                        <th>Referência</th>
                        <th>Valor</th>
                        <th>Vencimento</th>
                        <th>Tipo de Pagamento</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- As linhas serão preenchidas dinamicamente via JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary px-5 float-start">Salvar</button>
    </div>

    <!-- 
    
    <hr>

    @isset($item)
    {!! __view_locais_select_edit("Local", $item->filial_id) !!}
    @else
    {!! __view_locais_select() !!}
    @endif 
    
    <hr>
     
    @if(!isset($item))
    <p class="text-danger">
        *Campo abaixo deve ser preenchido se ouver recorrência para este registro
    </p>

    <div class="col-md-2">
        {!!Form::tel('recorrencia', 'Data')
        ->attrs(['data-mask' => '00/00'])
        ->placeholder('mm/aa')
        !!}
    </div>
    @endif 

    <div class="row tbl-recorrencia d-none mt-2">
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary px-5 float-end">Salvar</button>
    </div>-->
</div>

@section('js')

<script type="text/javascript" src="/js/client.js"></script>


<script type="text/javascript">
$('.modal .select2').each(function() {
    console.log($(this))
    let id = $(this).prop('id')

    if (id == 'inp-uf') {
        $(this).select2({
            dropdownParent: $(this).parent(),
            theme: 'bootstrap4',
        });
    }

    if (id == 'inp-cidade_id' || id == 'inp-cidade_cobranca_id') {

        $(this).select2({

            minimumInputLength: 2,
            language: "pt-BR",
            placeholder: "Digite para buscar a cidade",
            width: "100%",
            theme: 'bootstrap4',
            dropdownParent: $(this).parent(),
            ajax: {
                cache: true,
                url: path_url + 'api/buscaCidades',
                dataType: "json",
                data: function(params) {
                    console.clear()
                    var query = {
                        pesquisa: params.term,
                    };
                    return query;
                },
                processResults: function(response) {
                    console.log("response", response)
                    var results = [];

                    $.each(response, function(i, v) {
                        var o = {};
                        o.id = v.id;

                        o.text = v.nome + "(" + v.uf + ")";
                        o.value = v.id;
                        results.push(o);
                    });
                    return {
                        results: results
                    };
                }
            }
        });
    }
})

$('#btn-store-cliente').click(() => {
    let valid = validaCamposModal()
    if (valid.length > 0) {
        let msg = ""
        valid.map((x) => {
            msg += x + "\n"
        })
        swal("Ops, erro no formulário", msg, "error")
    } else {
        console.log("salvando...")

        let data = {}
        $(".modal input, .modal select").each(function() {

            let indice = $(this).attr('id')
            indice = indice.substring(4, indice.length)
            data[indice] = $(this).val()
        });
        data['empresa_id'] = $('#empresa_id').val()

        console.log(data)
        $.post(path_url + 'api/cliente/store', data)
            .done((success) => {
                console.log("success", success)
                swal("Sucesso", "Cliente cadastrado!", "success")
                    .then(() => {
                        var newOption = new Option(success.razao_social, success.id, false, false);
                        $('#inp-cliente_id').append(newOption).trigger('change');
                        $('#modal-cliente').modal('hide')
                    })

            }).fail((err) => {
                console.log(err)
                swal("Ops", "Algo deu errado ao salvar cliente!", "error")
            })
    }
})

function adicionarLinhaNaTabela(referencia, valor, vencimento, tipoPagamento) {
    const tbody = document.querySelector('#tabela-contas-geradas tbody');
    const row = tbody.insertRow();

    row.insertCell(0).textContent = referencia;
    row.insertCell(1).textContent = valor;
    row.insertCell(2).textContent = vencimento;
    row.insertCell(3).textContent = tipoPagamento;
}


$('#inp-recorrencia').blur(() => {

    let data = $('#inp-recorrencia').val()
    if (data.length == 5) {
        let vencimento = $('#inp-data_vencimento').val()
        let valor = $('#inp-valor_integral').val()
        if (valor && vencimento) {
            let item = {
                data: data,
                vencimento: vencimento,
                valor: valor
            }
            $.get(path_url + 'api/conta-receber/recorrencia', item)
                .done((html) => {
                    console.log("success", html)
                    $('.tbl-recorrencia').html(html)
                    $('.tbl-recorrencia').removeClass('d-none')

                }).fail((err) => {
                    console.log(err)

                })
        } else {
            swal("Algo saiu errado", "Informe o valor e vencimento data conta base!", "warning")
        }
    } else {
        swal("Algo saiu errado", "Informe uma data válida mm/aa exemplo 12/25", "warning")
    }
})
</script>
@endsection