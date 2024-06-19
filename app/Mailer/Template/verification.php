<!DOCTYPE html>
<html lang="en" style="height: 100%;">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Email Verification</title>
    <style>
        body, html {
            font-family: Arial, sans-serif;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            max-width: 85%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .left-side,
        .right-side {
            flex: 1;
        }

        .right-side {
            text-align: left;
            padding: 0 40px;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        img {
            max-width: 100%;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
            text-align: justify;
        }

        a.button {
            display: inline-block;
            margin-top: 20px;
            padding: 15px 25px;
            color: #fff;
            background-color: #FF9900;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        a.button:hover {
            background-color: #e68a00;
        }

        @media screen and (max-width: 600px) {
            .container {
                flex-direction: column;
                text-align: center;
            }

            .right-side {
                padding: 0;
            }
            
        }
    </style>
</head>

<body style="height: 100%;">
    <div class="container">
        <div class="left-side">
            <img src="<?= $image ?? '' ?>" alt="Mail Confirmed">
        </div>
        <div class="right-side">
            <h1>Verify Your Email</h1>
            <p>Hello, <?= $name ?? '' ?></p>
            <p>Welcome to our book rental service! To ensure the security of your account, please verify your email address. An email has been sent to <?= $email ?? '' ?> with a verification link. Please click on the link to verify your account and start exploring our wide collection of books. If you haven't received the email, please check your spam folder. If you still encounter issues, feel free to contact our support team.</p>
            <p>Thank you for choosing our service!</p>
            <a href="<?= $url ?? '' ?>" class="button">Verify Email</a>
        </div>
    </div>
</body>

</html>
