<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Cartao de credito</title>

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
            gap: 10px;
        }

        .card {
            width: '50%';
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
        <div class='card'>
            <h3>Cartao de credito</h3>
            <form id="form-checkout" method="POST">
                @csrf
                <div id="form-checkout__cardNumber" class="container"></div>
                <div id="form-checkout__expirationDate" class="container"></div>
                <div id="form-checkout__securityCode" class="container"></div>
                <input type="text" id="form-checkout__cardholderName" />

                <select id="form-checkout__issuer"></select>
                <select id="form-checkout__installments"></select>
                <select id="form-checkout__identificationType"></select>
                <input type="text" id="form-checkout__identificationNumber" placeholder='Numero do documento'/>
                <input type="email" id="form-checkout__cardholderEmail" placeholder='E-mail' />

                <input type="text" id="form-product-description" name='description' placeholder='Descricao' />
                <input type="number" id="form-checkout__amount" name='name' placeholder='Valor' value='10' />

                <button type="submit" id="form-checkout__submit">Pagar</button>
                <progress class="progress-bar">Carregando...</progress>
            </form>
        </div>

        <div class='table'>
            <h3> Logs do pagamento do cartao de credito!</h3>
            <table id="tabelaLogs" class="hover" style="width: 100%">
                <thead>
                    <tr>
                        <th>Id Pagamento</th>
                        <th>Descricao</th>
                        <th>Tipo de documento</th>
                        <th>Numero do documento</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Status Detalhe</th>
                        <th>Valor Compra</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($creditCard))
                        @foreach ($creditCard->all() as $e)
                            <tr>
                                <td>{{ $e->id_payment }}</td>
                                <td>{{ $e->description }}</td>
                                <td>{{ $e->type_document }}</td>
                                <td>{{ $e->number_document }}</td>
                                <td>{{ $e->email }}</td>
                                <td>{{ $e->status }}</td>
                                <td>{{ $e->status_detail }}</td>
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

        

        const cardForm = mp.cardForm({
            amount: document.getElementById('form-checkout__amount').value,
            iframe: true,
            form: {
                id: "form-checkout",
                cardNumber: {
                    id: "form-checkout__cardNumber",
                    placeholder: "Número do cartão",
                },
                expirationDate: {
                    id: "form-checkout__expirationDate",
                    placeholder: "MM/YY",
                },
                securityCode: {
                    id: "form-checkout__securityCode",
                    placeholder: "Código de segurança",
                },
                cardholderName: {
                    id: "form-checkout__cardholderName",
                    placeholder: "Titular do cartão",
                },
                issuer: {
                    id: "form-checkout__issuer",
                    placeholder: "Banco emissor",
                },
                installments: {
                    id: "form-checkout__installments",
                    placeholder: "Parcelas",
                },
                identificationType: {
                    id: "form-checkout__identificationType",
                    placeholder: "Tipo de documento",
                },
                identificationNumber: {
                    id: "form-checkout__identificationNumber",
                    placeholder: "Número do documento",
                },
                cardholderEmail: {
                    id: "form-checkout__cardholderEmail",
                    placeholder: "E-mail",
                },
            },
            callbacks: {
                onFormMounted: error => {
                    if (error) return console.warn("Form Mounted handling error: ", error);
                    console.log("Form mounted");
                },
                onSubmit: event => {
                    event.preventDefault();

                    const {
                        paymentMethodId: payment_method_id,
                        issuerId: issuer_id,
                        cardholderEmail: email,
                        amount,
                        token,
                        installments,
                        identificationNumber,
                        identificationType,
                    } = cardForm.getCardFormData();

                    fetch("/credit-card", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                            },
                            body: JSON.stringify({
                                token,
                                issuer_id,
                                payment_method_id,
                                transaction_amount: Number(amount),
                                installments: Number(installments),
                                description: document.getElementById('form-product-description')
                                    .value,
                                payer: {
                                    email,
                                    identification: {
                                        type: identificationType,
                                        number: identificationNumber,
                                    },
                                },
                            }),
                        })
                        .then(response => {
                            return response.json();
                        })
                        .then(result => {
                            // console.log(" result:", JSON.parse(result);
                            console.log(" result:", result);

                            if (!result.hasOwnProperty("error_message")) {

                                const resp =
                                    `Status do pagamento: ${result.status} \n Detalhe do pagamento: ${result.status_detail}`;
                                alert(resp);

                                location.reload();
                            } else {
                                alert(result.error_message)
                                location.reload();
                            }
                        });
                },
                onFetching: (resource) => {
                    console.log("Fetching resource: ", resource);

                    // Animate progress bar
                    const progressBar = document.querySelector(".progress-bar");
                    progressBar.removeAttribute("value");

                    return () => {
                        progressBar.setAttribute("value", "0");
                    };
                },
                onCardTokenReceived: (errorData, token) => {
                    console.log("token:", token);
                    console.log("errorData:", errorData);

                    let err = '';

                    errorData?.forEach((e, i) => {

                        let message = e.message + '\n';
                        err += message;
                    });
                    if (errorData)
                        alert(err);

                },
                onValidityChange: (error, field) => {
                    console.log("field:", field);
                    console.log("error:", error);

                }
            },
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#tabelaLogs').DataTable();
        });
    </script>

</body>

</html>
