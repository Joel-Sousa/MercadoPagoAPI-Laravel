<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Home</title>

</head>

<body>
    @include('head')

    <h3> Meios de pagamentos atraves do mercado pago!</h3>
    <h3> Verificar Status do pagamento!</h3>
    <form action='/status-payment/' method='GET'>
        <input type='number' name='idPayment' placeholder='Id do pagamento' required value='' />
        <button type='submit'>Consultar</button>
    </form>

    <br>
    @if (!empty($resp))

        @if ($resp['method_payment'] == 'master' || $resp['method_payment'] == 'visa')
            Id: {{ $resp['id'] }}<br>
            Status: {{ $resp['status'] }}<br>
            Detalhes: {{ $resp['detail'] }}<br>
            Tipo: {{ $resp['method_payment'] }}<br>
            Valor da compra: {{ $resp['transaction_amount'] }}<br>
            Data da compra: {{ $resp['data_approved'] }}<br>
        @elseif ($resp['method_payment'] == 'pix')
            Pix Copia Cola: <input type='text' value="{{ $resp['pix_copy_paste'] }}" />
            <input type='button' class='btn btn-primary' id='{{ $resp['pix_copy_paste'] }}' onclick='copyValue(this.id)'
                value='Copiar'>
            <br>
            <a href="{{ $resp['ticket_url'] }}" target="_blank"><button>Pagar com Pix</button></a><br>
            QR Code: <br>
            <img src="data:image/png;base64,{{ $resp['qr_code'] }}" width='300' />
        @elseif ($resp['method_payment'] == 'bolbradesco')
        <script>
            window.onload = function() {
                window.open("{{ url($resp['external_resource_url']) }}", '_blank');
            };
        </script>
        @endif
    @elseif (!empty($response_fields))
        Id Nao encontrado!<br>
        Mensagem: {{ $response_fields['error_message'] }}
    @endif


    <h4>Documentação da Api-Mercado pago:
        <a href='https://www.mercadopago.com.br/developers/pt' target='_blank'>
            https://www.mercadopago.com.br/developers/pt
        </a>
    </h4>

    <script>
        function copyValue(value) {
            navigator.clipboard.writeText(value).then(function() {
                alert('Copiado!')
            }, function(err) {
                console.error('Async: Could not copy text: ', err);
            });
        }
    </script>
</body>

</html>
