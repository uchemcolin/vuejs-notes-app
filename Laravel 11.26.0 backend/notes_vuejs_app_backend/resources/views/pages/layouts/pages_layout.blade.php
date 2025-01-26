<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Reset Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 700px;
            width: 100%;
            animation: fadeIn 1s ease-in-out;
        }

        h1 {
            font-size: 2.75rem;
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
        }

        p {
            font-size: 1.125rem;
            color: #555;
            margin-bottom: 30px;
        }

        .cta-button {
            display: inline-block;
            background-color: #6c63ff;
            color: white;
            padding: 16px 30px;
            font-size: 1.25rem;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .cta-button:hover {
            background-color: #5750e1;
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .cta-button:focus {
            outline: none;
        }

        .footer {
            margin-top: 40px;
            font-size: 0.875rem;
            color: #aaa;
        }

        /* Animation */
        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(-50px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 768px) {
            h1 {
                font-size: 2.25rem;
            }

            p {
                font-size: 1rem;
            }

            .cta-button {
                font-size: 1rem;
                padding: 12px 24px;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.75rem;
            }

            p {
                font-size: 0.875rem;
                margin-bottom: 20px;
            }

            .cta-button {
                font-size: 1rem;
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>

    @yield("content")
    
</body>
</html>



