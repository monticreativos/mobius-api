<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ah ah ah!</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center; /* Centrado horizontal */
            align-items: center;     /* Centrado vertical */
            font-family: 'Arial Black', Gadget, sans-serif;
            background-color: #ffffff;
            color: #000000;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center; /* Centra contenido dentro del div */
            text-align: center;
            width: 100%;
        }

        .dennis-gif {
            max-width: 300px; /* Ajusta este tamaño si quieres el gif más grande o pequeño */
            height: auto;
            margin-bottom: 20px;
        }

        .magic-word {
            font-size: 1.8rem;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            /* Animación de latido */
            animation: heartbeat 1.5s ease-in-out infinite;
        }

        .footer-link {
            margin-top: 40px;
            font-family: Arial, sans-serif;
            font-size: 12px;
            text-decoration: none;
            color: #666; /* Gris sutil */
            font-weight: normal;
            letter-spacing: 1px;
            transition: color 0.3s;
        }

        .footer-link:hover {
            color: #000;
        }

        @keyframes heartbeat {
            0% { transform: scale(1); }
            14% { transform: scale(1.05); }
            28% { transform: scale(1); }
            42% { transform: scale(1.05); }
            70% { transform: scale(1); }
        }
    </style>
</head>
<body>

    <div class="container">
        <img src="{{ asset('dennis-jurassic-park.gif') }}" alt="Dennis Nedry" class="dennis-gif">
        
        <p class="magic-word">
            Ah ah ah. You didn't say the magic word!
        </p>

        <a href="https://monticreativos.github.io/" target="_blank" class="footer-link">
            https://monticreativos.github.io/
        </a>
    </div>

</body>
</html>