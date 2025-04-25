<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificato di Partecipazione</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        .certificate {
            width: 800px;
            height: 600px;
            margin: 20px auto;
            background-color: #fff;
            border: 2px solid #333;
            padding: 40px;
            position: relative;
            background-image: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuXzEiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiIHdpZHRoPSIxMCIgaGVpZ2h0PSIxMCIgcGF0dGVyblRyYW5zZm9ybT0icm90YXRlKDQ1KSI+PHBhdGggZD0iTSAtNSwwIEwgNSwwIiBzdHJva2U9IiNlMGUwZTAiIHN0cm9rZS13aWR0aD0iMC41Ii8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI3BhdHRlcm5fMSkiLz48L3N2Zz4=");
            background-repeat: repeat;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .border {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 2px solid #007bff;
            pointer-events: none;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 150px;
        }
        h1 {
            font-size: 28px;
            color: #007bff;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .subtitle {
            font-size: 18px;
            color: #444;
            margin-top: 5px;
        }
        .content {
            text-align: center;
            margin-bottom: 30px;
        }
        .certificate-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
            color: #333;
        }
        .recipient-name {
            font-size: 30px;
            font-weight: bold;
            color: #007bff;
            margin: 20px 0;
            font-family: 'Times New Roman', serif;
        }
        .certificate-text {
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 30px;
        }
        .event-details {
            margin: 20px 0;
            font-size: 16px;
        }
        .signature {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }
        .signature-item {
            text-align: center;
            width: 45%;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-bottom: 5px;
            width: 80%;
            display: inline-block;
        }
        .signature-name {
            font-weight: bold;
        }
        .signature-title {
            font-size: 14px;
            color: #666;
        }
        .certificate-footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
        .seal {
            position: absolute;
            bottom: 80px;
            right: 50px;
            width: 100px;
            height: 100px;
            background-image: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMDAgMTAwIj48Y2lyY2xlIGN4PSI1MCIgY3k9IjUwIiByPSI0NSIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjMDA3YmZmIiBzdHJva2Utd2lkdGg9IjIiLz48Y2lyY2xlIGN4PSI1MCIgY3k9IjUwIiByPSI0MCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjMDA3YmZmIiBzdHJva2Utd2lkdGg9IjEiLz48dGV4dCB4PSI1MCIgeT0iNTAiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGRvbWluYW50LWJhc2VsaW5lPSJtaWRkbGUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMCIgZmlsbD0iIzAwN2JmZiI+T2ZmaWNpYWw8L3RleHQ+PHRleHQgeD0iNTAiIHk9IjYyIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iOCIgZmlsbD0iIzAwN2JmZiI+Q2VydGlmaWNhdGU8L3RleHQ+PC9zdmc+");
            background-repeat: no-repeat;
            transform: rotate(-15deg);
            opacity: 0.7;
        }
        .certificate-id {
            position: absolute;
            bottom: 20px;
            left: 20px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="border"></div>

        <div class="header">
            <div class="logo">
                <!-- Logo come base64 o testo -->
                <div style="font-size: 24px; font-weight: bold; color: #007bff;">ONBOARDING PLATFORM</div>
            </div>
            <h1>Certificato di Partecipazione</h1>
            <div class="subtitle">Si certifica che</div>
        </div>

        <div class="content">
            <div class="recipient-name">{{ $user->name }}</div>

            <div class="certificate-text">
                ha partecipato con successo all'evento formativo
            </div>

            <div class="certificate-title">
                "{{ $event->title }}"
            </div>

            <div class="event-details">
                <p>Tenutosi il {{ $event->start_date->format('d/m/Y') }} presso {{ $event->location }}</p>
                <p>Durata: {{ $event->start_date->diffInHours($event->end_date) }} ore</p>
            </div>
        </div>

        <div class="signature">
            <div class="signature-item">
                <div class="signature-line"></div>
                <div class="signature-name">Organizzatore dell'Evento</div>
                <div class="signature-title">ONBOARDING PLATFORM</div>
            </div>

            <div class="signature-item">
                <div class="signature-line"></div>
                <div class="signature-name">Responsabile della Formazione</div>
                <div class="signature-title">Dipartimento Risorse Umane</div>
            </div>
        </div>

        <div class="certificate-footer">
            Questo certificato attesta la partecipazione all'evento formativo sopra indicato.
            Non rappresenta un titolo di studio o una qualifica professionale.
        </div>

        <div class="seal"></div>

        <div class="certificate-id">
            ID Certificato: {{ md5($event->id . $user->id . 'certificate') }}
        </div>
    </div>
</body>
</html>
