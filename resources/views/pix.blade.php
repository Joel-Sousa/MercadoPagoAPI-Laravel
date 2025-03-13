<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Pix</title>

    <link rel='stylesheet' href='https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css'>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>

    <style>
        .tabela {
            margin-left: 10px;
            margin-right: 10px;
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
            gap: 10px;
        }

        .pix {
            width: '50%';
            display: inline-block;
        }

        .table {
            width: '50%';
        }
    </style>

    <script src="https://sdk.mercadopago.com/js/v2"></script>
</head>

<body>
    @include('head')

    <div class='contain'>
        <div class='pix'>
            <h3>Pix</h3>
            <form id="form-checkout" method="POST" action="/pix">
                @csrf
                <div>
                    <div>
                        <input id="form-checkout__payerFirstName" name="payerFirstName" type="text" placeholder='Nome completo' value='toot'>
                    </div>
                    <div>
                        <input id="form-checkout__email" name="email" type="text" placeholder='E-mail' value='dev4pk@gmail.com' >
                    </div>
                    <div>
                        <select id="form-checkout__identificationType" name="identificationType" placeholder='Tipo de documento'
                            type="text"></select>
                    </div>
                    <div>
                        <input id="form-checkout__identificationNumber" name="identificationNumber" type="text" placeholder='Numero do documento'  value='12345678909'>
                    </div>
                </div>
                <div>
                    <div>
                        <input type="text" name="transactionAmount" id="transactionAmount" value='100'>
                        <input type="text" name="description" id="description" placeholder='Descrição' value='tst descricao' >
                        <br>
                        <button type="submit">Pagar</button>
                    </div>
                </div>
            </form>
        </div>
        <div class='table'>
            <h3> Logs de pagamentos!</h3>
            <table id="tabelaLogs" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Id Pagamento</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Tipo do documento</th>
                        <th>Numero do documento</th>
                        <th>Status</th>
                        <th>Status Detalhe</th>
                        <th>Data</th>
                        <th>Valor da compra</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($pix))
                        @foreach ($pix->all() as $e)
                            <tr>
                                <td>{{ $e->id_payment }}</td>
                                <td>{{ $e->name }}</td>
                                <td>{{ $e->email }}</td>
                                <td>{{ $e->type_document }}</td>
                                <td>{{ $e->number_document }}</td>
                                <td>{{ $e->status }}</td>
                                <td>{{ $e->status_detail }}</td>
                                <td>{{ $e->created_at->format('d/m/Y H:i:s') }}</td>
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
        $(document).ready(function() {
            $('#tabelaLogs').DataTable();
        });
    </script>
    
</body>

</html>
