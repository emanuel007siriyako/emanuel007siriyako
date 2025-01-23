<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e8f5e9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #333;
            box-sizing: border-box;
        }
               body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('National_Electoral_Commission_(Tanzania)_Logo (1).png') no-repeat center center fixed;
            background-size: cover;
            background-attachment: fixed;
            opacity: 0.16;
            z-index: -1;
        }

        .thank-you {
            text-align: center;
            background-color: #fff;
            padding: 50px;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 600px;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease;
        }

        .thank-you h1 {
            color: #4CAF50;
            font-size: 36px;
            margin-bottom: 20px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .thank-you p {
            font-size: 18px;
            margin-bottom: 30px;
        }

        .btn-back {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 18px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .btn-back:hover {
            background-color: #45a049;
        }

        .thank-you:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        }

        @media (max-width: 768px) {
            .thank-you {
                padding: 30px;
                width: 100%;
                max-width: 500px;
            }
            .btn-back {
                font-size: 16px;
                padding: 10px 18px;
            }
        }
    </style>
</head>
<body>
    <div class="thank-you">
        <h1>Thank You for Voting!</h1>
        <p>Your vote has been successfully recorded.</p>
        <a href="index.php" class="btn-back">Complete</a>
    </div>
</body>
</html>
