<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Boleto bancario</title>

    <link rel='stylesheet' href='https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css'>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>

    <style>
        .tabela {
            margin-left: 100px;
            margin-right: 100px;
        }
    </style>

    <style>
        #form-checkout {
            display: flex;
            flex-direction: column;
            max-width: 600px;
        }

        .container {
            height: 18px;
            display: inline-block;
            border: 1px solid rgb(118, 118, 118);
            border-radius: 2px;
            padding: 1px 2px;
        }

        .contain {
            display: flex;
        }

        .boleto {
            width: '50%';
        }

        .tabela {
            width: '50%';
        }
    </style>

    <script src="https://sdk.mercadopago.com/js/v2"></script>
</head>

<body>
    @include('head')

    <div class='contain'>
        <div class='boleto'>
            <h3>Boleto</h3>
            <form id="form-checkout" action="/bank-ticket" method="POST">
                @csrf
                <div>
                    <div>
                        <input id="form-checkout__payerFirstName" name="payerFirstName" type="text"
                            placeholder='Nome'>
                    </div>
                    <div>
                        <input id="form-checkout__payerLastName" name="payerLastName" type="text"
                            placeholder='Sobrenome' >
                    </div>
                    <div>
                        <input id="form-checkout__email" name="email" type="text" placeholder='E-Mail' >
                    </div>
                    <div>
                        <select id="form-checkout__identificationType" name="identificationType"
                            placeholder='Tipo de documento' type="text"></select>
                    </div>
                    <div>
                        <input id="form-checkout__identificationNumber" name="identificationNumber" type="text"
                            placeholder='Numero do documento' >
                    </div>
                    <div>
                        <input id="form-checkout__zipCode" name="zipCode" type="text" placeholder='Cep' maxlength='9' >
                    </div>
                    <div>
                        <input id="form-checkout__streetName" name="streetName" type="text" placeholder='Rua'>
                    </div>
                    <div>
                        <input id="form-checkout__streetNumber" name="streetNumber" type="text" placeholder='Numero' >
                    </div>
                    <div>
                        <input id="form-checkout__neighborhood" name="neighborhood" type="text" placeholder='Bairro'>
                    </div>
                    <div>
                        <input id="form-checkout__city" name="city" type="text" placeholder='Cidade'>
                    </div>
                    <div>
                        <input id="form-checkout__federalUnit" name="federalUnit" type="text" placeholder='Estado'>
                    </div>
                </div>
                <div>
                    <div>
                        <input type="number" name="transactionAmount" id="transactionAmount" placeholder='Valor'
                            >
                        <input type="text" name="description" id="description" >
                        <br>
                        <button type="submit">Pagar</button>
                    </div>
                </div>
            </form>

        </div>

        <div class='tabela'>
            <h3> Logs do pagamento do boleto!</h3>
            <table id="tabelaLogs" class="hover" style="width: 100%">
                <thead>
                    <tr>
                        <th>Id Pagamento</th>
                        <th>Descricao</th>
                        <th>Nome</th>
                        <th>Sobrenome</th>
                        <th>Email</th>
                        <th>Tipo de documento</th>
                        <th>Numero do documento</th>
                        <th>Status</th>
                        <th>Valor Compra</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($bankTicket))
                        @foreach ($bankTicket->all() as $e)
                            <tr>
                                <td>{{ $e->id_payment }}</td>
                                <td>{{ $e->description }}</td>
                                <td>{{ $e->name }}</td>
                                <td>{{ $e->last_name }}</td>
                                <td>{{ $e->email }}</td>
                                <td>{{ $e->type_document }}</td>
                                <td>{{ $e->number_document }}</td>
                                <td>{{ $e->status }}</td>
                                <td>{{ $e->value }}</td>
                            </tr>
                        @endforeach
                    @endif

                </tbody>
                <tfoot>
                </tfoot>
            </table>
        </div>
    </div>

    <script>
        const mp = new MercadoPago("{{ env('MERCADO_PAGO_PUBLIC_KEY') }}");

        (async function getIdentificationTypes() {
            try {
                const identificationTypes = await mp.getIdentificationTypes();
                const identificationTypeElement = document.getElementById('form-checkout__identificationType');

                createSelectOptions(identificationTypeElement, identificationTypes);
            } catch (e) {
                return console.error('Error getting identificationTypes: ', e);
            }
        })();

        function createSelectOptions(elem, options, labelsAndKeys = {
            label: "name",
            value: "id"
        }) {
            const {
                label,
                value
            } = labelsAndKeys;

            elem.options.length = 0;

            const tempOptions = document.createDocumentFragment();

            options.forEach(option => {
                const optValue = option[value];
                const optLabel = option[label];

                const opt = document.createElement('option');
                opt.value = optValue;
                opt.textContent = optLabel;

                tempOptions.appendChild(opt);
            });

            elem.appendChild(tempOptions);
        }
    </script>

    <script>
        document.getElementById('form-checkout__zipCode').addEventListener('blur', function() {
            let cep = this.value.replace(/\D/g, ''); // Remove caracteres não numéricos

            if (cep.length === 8) {
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.erro) {
                            document.getElementById('form-checkout__streetName').value = data.logradouro || '';
                            document.getElementById('form-checkout__neighborhood').value = data.bairro || '';
                            document.getElementById('form-checkout__city').value = data.localidade || '';
                            document.getElementById('form-checkout__federalUnit').value = data.uf || '';
                        } else {
                            alert('CEP não encontrado!');
                        }
                    })
                    .catch(error => console.error('Erro ao buscar CEP:', error));
            } else {
                alert('CEP inválido! Digite 8 números.');
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#tabelaLogs').DataTable();
        });
    </script>

    @if (!empty($resp))
        @if (array_key_exists('error_message', $resp))
            Mensagem: {{ $resp['error_message'] }}
        @else
            <script>
                window.onload = function() {
        // {{-- {{ dd($resp->original['error']->data[0]->erro) }} --}}
                    window.open("{{ url($resp['external_resource_url']) }}", '_blank');
                };
            </script>
        @endif
    @endif
</body>

</html>
