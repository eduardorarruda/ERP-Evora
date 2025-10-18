@extends('default.layout',['title' => 'Lançamento Financeiro Recorrente'])

@section('css')
<link rel="stylesheet" href="{{ asset('css/lancamento-recorrente.css') }}">
@endsection

@section('content')
<div class="page-content">
    <div class="card border-top border-0 border-4 border-primary">
        <div class="card-body p-5">
            <!-- Header com botão Gerar -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 text-primary fw-bold">Lançamento Financeiro Recorrente</h4>
                <button type="button" class="btn btn-primary" id="btn-gerar">
                    <i class="bx bx-refresh"></i> Gerar
                </button>
            </div>

            <!-- Formulário -->
            {!!Form::open()
            ->post()
            ->route('lancamento-recorrente.store')
            ->attrs(['id' => 'form-lancamento-recorrente'])!!}

            <input type="hidden" name="lancamentos_json" id="lancamentos_json" value="">

            <div class="pl-lg-4">
                @include('lançamentos_recorrentes._forms')
            </div>
            {!!Form::close()!!}
        </div>
    </div>
</div>

@include('modals._client', ['not_submit' => true])
@endsection

@section('js')
<script src="{{ asset('js/lancamento-recorrente.js') }}"></script>

<script type="text/javascript" src="/js/client.js"></script>

<script type="text/javascript">
// Configuração dos modais
$('.modal .select2').each(function() {
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
                    return {
                        pesquisa: params.term,
                    };
                },
                processResults: function(response) {
                    var results = [];
                    $.each(response, function(i, v) {
                        results.push({
                            id: v.id,
                            text: v.nome + "(" + v.uf + ")",
                            value: v.id
                        });
                    });
                    return {
                        results: results
                    };
                }
            }
        });
    }
})

// Cadastro de cliente via modal
$('#btn-store-cliente').click(() => {
    let valid = validaCamposModal()
    if (valid.length > 0) {
        let msg = ""
        valid.map((x) => {
            msg += x + "\n"
        })
        swal("Ops, erro no formulário", msg, "error")
    } else {
        let data = {}
        $(".modal input, .modal select").each(function() {
            let indice = $(this).attr('id')
            indice = indice.substring(4, indice.length)
            data[indice] = $(this).val()
        });
        data['empresa_id'] = $('#empresa_id').val()

        $.post(path_url + 'api/cliente/store', data)
            .done((success) => {
                swal("Sucesso", "Cliente cadastrado!", "success")
                    .then(() => {
                        var newOption = new Option(success.razao_social, success.id, false, false);
                        $('#inp-cliente_id').append(newOption).trigger('change');
                        $('#modal-cliente').modal('hide')
                    })
            }).fail((err) => {
                swal("Ops", "Algo deu errado ao salvar cliente!", "error")
            })
    }
})
</script>
@endsection