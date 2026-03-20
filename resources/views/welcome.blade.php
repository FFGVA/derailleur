<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dérailleur — Fast and Female Geneva</title>
    <link rel="icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/images/apple-touch-icon.png">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #f5f1e9;
            font-family: Arial, Helvetica, sans-serif;
            color: #333;
        }
        .container {
            text-align: center;
            padding: 2rem;
        }
        .logos {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2.5rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        .logo-derailleur {
            width: 120px;
            height: 120px;
        }
        .logo-ffgva {
            height: 80px;
        }
        h1 {
            font-size: 2.2rem;
            color: #80081C;
            margin-bottom: 0.5rem;
            letter-spacing: 0.02em;
        }
        .subtitle {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 2.5rem;
        }
        .btn {
            display: inline-block;
            background-color: #80081C;
            color: #fff;
            text-decoration: none;
            padding: 0.8rem 2rem;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: bold;
            transition: background-color 0.2s;
        }
        .btn:hover {
            background-color: #5e0614;
        }
        .footer {
            position: absolute;
            bottom: 1.5rem;
            font-size: 0.85rem;
            color: #999;
        }
        .footer a {
            color: #80081C;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        @media (max-width: 480px) {
            h1 { font-size: 1.6rem; }
            .logos { gap: 1.5rem; }
            .logo-derailleur { width: 90px; height: 90px; }
            .logo-ffgva { height: 60px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logos">
            <img src="/images/derailleur.png" alt="Dérailleur" class="logo-derailleur">
            <img src="/images/logo-ffgva.png" alt="Fast and Female Geneva" class="logo-ffgva">
        </div>
        <h1>Dérailleur</h1>
    </div>
    <div class="footer">
        <a href="https://www.ffgva.ch">www.ffgva.ch</a>
    </div>
</body>
</html>
