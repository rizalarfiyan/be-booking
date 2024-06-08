<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>Email Verifications</h1>
    <p>Hi, <?= $name ?? '' ?></p>
    <p>Click the link below to verify your email address</p>
    <a href="<?= $url ?? '' ?>">Verify Email</a>
    <p>Thank you</p>
</body>
</html>
