<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Qr Code Pix</title>

</head>

<body>
    @include('head')

    <div>
        @if (!empty($resp))
        {{-- {{ dd($resp->original['error']->data[0]->erro) }} --}}

            @if (array_key_exists('error_message', $resp))
                Mensagem: {{ $resp['error_message'] }}
            @else
                Status: {{ $resp['status'] }}<br>
                Detalhes: {{ $resp['detail'] }}<br>
                Valor: {{ $resp['value'] }}<br>
                Data de criação: {{ $resp['data_created'] }}<br>
                Data de expiracao: {{ $resp['data_expiration'] }}<br>
                Pix Copia Cola: <input type='text' value="{{ $resp['pix_copy_paste'] }}" />
                <input type='button' class='btn btn-primary' id='{{ $resp['pix_copy_paste'] }}'
                    onclick='copyPaste(this.id)' value='Copiar'>
                <br>
                <a href="{{ $resp['ticket_url'] }}" target="_blank"><button>Pagar com Pix</button></a><br>
                QR Code: <br>
                <img src="data:image/png;base64,{{ $resp['qr_code'] }}" width='300' />
            @endif
        @endif
    </div>
    <script>
        function copyPaste(value) {
            navigator.clipboard.writeText(value).then(function() {
                alert('Copiado!')
            }, function(err) {
                console.error('Async: Could not copy text: ', err);
            });
        }
    </script>
</body>

</html>
