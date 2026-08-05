<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="theme-color" content="#111827">

    <title>Sem conexão - Meu Gestor</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            padding: 24px;

            display: flex;
            align-items: center;
            justify-content: center;

            font-family: Arial, Helvetica, sans-serif;
            color: #e5e7eb;
            background: #111827;
        }

        .offline-card {
            width: 100%;
            max-width: 430px;
            padding: 32px;

            text-align: center;

            border: 1px solid #374151;
            border-radius: 20px;
            background: #1f2937;

            box-shadow: 0 20px 50px rgba(0, 0, 0, .25);
        }

        .offline-card img {
            width: 96px;
            height: 96px;
            margin-bottom: 16px;
            object-fit: contain;
        }

        .offline-card h1 {
            margin: 0 0 12px;
            font-size: 26px;
        }

        .offline-card p {
            margin: 0 0 24px;
            line-height: 1.6;
            color: #cbd5e1;
        }

        .offline-card button {
            width: 100%;
            padding: 13px 20px;

            border: 0;
            border-radius: 12px;

            font-size: 16px;
            font-weight: 600;

            color: #ffffff;
            background: #24b153;

            cursor: pointer;
        }

        .offline-card button:hover {
            filter: brightness(.95);
        }
    </style>
</head>

<body>

    <main class="offline-card">
        <img src="{{ asset('icons/icon-192.png') }}" alt="Meu Gestor">

        <h1>Sem conexão</h1>

        <p>
            Não foi possível conectar ao servidor do Meu Gestor.
            Verifique sua conexão com a internet e tente novamente.
        </p>

        <button type="button" onclick="window.location.reload()">
            Tentar novamente
        </button>
    </main>

</body>

</html>
