<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quote Post</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            width: 1080px;
            height: 1350px;
            margin: 0;
            overflow: hidden;
            font-family: 'Poppins', sans-serif;
            background-color: #000;
        }

        .canvas {
            position: relative;
            width: 1080px;
            height: 1350px;
            background-image: url('{{ asset('background.png') }}');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            padding: 80px;
            color: white;
        }

        .overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.75));
            z-index: 1;
        }

        .content {
            position: relative;
            z-index: 2;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .logo {
            width: 80px;
            height: 80px;
            margin-top: 40px;
            opacity: 0.9;
        }

        .quote-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            max-width: 900px;
            text-align: center;
        }

        .quote-text {
            font-family: 'Playfair Display', serif;
            font-size: 50px;
            line-height: 1.3;
            font-weight: 500;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            text-wrap: balance;
        }

        .footer {
            margin-bottom: 60px;
            font-size: 18px;
            letter-spacing: 2px;
            text-transform: uppercase;
            opacity: 0.8;
            font-weight: 500;
        }

    </style>
</head>
<body>
    <div class="canvas">
        <div class="overlay"></div>
        <div class="content">
            <img src="{{ asset('logo/Chetak-white.png') }}" alt="Logo" class="logo">

            <div class="quote-container">
                <h1 class="quote-text">
                    {{ $quote }}
                </h1>
            </div>

            <div class="footer">
                @chetak_quotes
            </div>
        </div>
    </div>
</body>
</html>
