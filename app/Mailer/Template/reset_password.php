<!DOCTYPE html>
<html lang="en" style="height: 100%;">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password</title>
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
                flex-direction: column-reverse;
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
        <div class="right-side">
            <h1>Change Your Password</h1>
            <p>Hello, <?= $name ?? '' ?></p>
            <p>We have received a request to reset the password for your account. To complete the process, please click the link below to change your password. If you did not request a password reset, please ignore this email or contact our support team if you have any concerns.</p>
            <p>Thank you for your attention to this matter!</p>
            <a href="<?= $url ?? '' ?>" class="button">Change Password</a>
        </div>
        <div class="left-side">
            <img src="<?= $image ?? '' ?>" alt="Reset Password">
        </div>
    </div>
</body>

</html>
