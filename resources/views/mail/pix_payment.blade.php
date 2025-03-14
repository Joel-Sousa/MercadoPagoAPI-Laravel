<!DOCTYPE html>
<html>

<head>
    <style>
        .container {
            /* text-align: center; */
            background-color: #EDF2F7;
            width: 50%;
            height: 50%;
        }

        .conteudo {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif,
                'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            font-size: 15px;
            padding: 30px;
            margin: 10px 20px 20px 20px;
            background-color: #fff;

        }
    </style>
</head>

<body>
    <div class='container'>
        <br />
        <div class='conteudo'>
            <hr />
            <div>
                Ola: <b>{{ $data['name'] ?? 'Sem dados' }}.</b><br /><br />
                {{ $data['message'] ?? 'Sem dados' }}
                <hr>
                Detalhes:<br>
                Email: {{ $data['email'] ?? 'Sem dados' }}<br>
                Tipo documento: {{ $data['type_document'] ?? 'Sem dados' }}<br>
                Numero documento: {{ $data['number_document'] ?? 'Sem dados' }}<br>
                Data de criacção: {{ $data['data_created'] ?? 'Sem dados' }}<br>
                Data de expiração: {{ $data['data_expiration'] ?? 'Sem dados' }}<br>
                Copia e cola: {{ $data['pix_copy_paste'] ?? 'Sem dados' }}<br>
                Descricao: {{ $data['description'] ?? 'Sem dados' }}<br>
                Valor: {{ $data['value'] ?? 'Sem dados' }}<br>
                Status: {{ $data['status'] ?? 'Sem dados' }}<br>
                Status detalhe: {{ $data['status_detail'] ?? 'Sem dados' }}<br>
                <a href="{{ $data['ticket_url'] }}" target="_blank"><button>Pagar com Pix</button></a><br>
                QR Code: <br>
                <img src="data:image/png;base64,{{ $data['qr_code'] }}" width='300' />
            </div>
            <br />
            <hr />

            <br />
        </div>
        <br />
    </div>

</body>

</html>
