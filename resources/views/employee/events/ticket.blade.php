<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Biglietto Evento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .ticket {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #333;
            border-radius: 8px;
            overflow: hidden;
        }
        .ticket-header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .ticket-body {
            padding: 20px;
        }
        .ticket-info {
            margin-bottom: 30px;
        }
        .ticket-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .ticket-info table tr td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .ticket-info table tr td:first-child {
            font-weight: bold;
            width: 30%;
        }
        .ticket-footer {
            padding: 20px;
            text-align: center;
            background-color: #f8f9fa;
            font-size: 12px;
            border-top: 1px solid #ddd;
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
        .qr-code img {
            max-width: 150px;
        }
        h1, h2, h3 {
            margin: 0;
            padding: 0;
        }
        .ticket-id {
            font-size: 18px;
            margin-top: 10px;
            font-weight: normal;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="ticket-header">
            <h1>{{ $event->title }}</h1>
            <h3 class="ticket-id">Biglietto #{{ $event->id }}-{{ $user->id }}</h3>
        </div>

        <div class="ticket-body">
            <div class="ticket-info">
                <table>
                    <tr>
                        <td>Partecipante:</td>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td>Data:</td>
                        <td>{{ $event->start_date->format('d/m/Y H:i') }} - {{ $event->end_date->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td>Luogo:</td>
                        <td>{{ $event->location }}</td>
                    </tr>
                    <tr>
                        <td>Tipo evento:</td>
                        <td>{{ ucfirst($event->type) }}</td>
                    </tr>
                    <tr>
                        <td>Stato registrazione:</td>
                        <td>
                            <strong style="color: #28a745;">Confermato</strong>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="qr-code">
                <img src="data:image/svg+xml;base64,{{ base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="150" height="150" viewBox="0 0 150 150"><rect width="150" height="150" fill="white"/><rect x="10" y="10" width="130" height="130" fill="black"/><rect x="20" y="20" width="110" height="110" fill="white"/><text x="75" y="80" text-anchor="middle" font-family="Arial" font-size="12">QR Code</text></svg>') }}" alt="QR Code">
                <p>Scansiona questo codice QR all'ingresso dell'evento</p>
            </div>

            <div class="ticket-info">
                <h3>Descrizione evento:</h3>
                <p>{{ $event->description }}</p>
            </div>
        </div>

        <div class="ticket-footer">
            <p>Questo biglietto è stato generato il {{ now()->format('d/m/Y H:i') }} e appartiene esclusivamente a {{ $user->name }}.</p>
            <p>Per qualsiasi informazione, contatta l'organizzatore dell'evento.</p>
        </div>
    </div>
</body>
</html>
