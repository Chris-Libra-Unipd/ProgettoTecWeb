<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>Logout</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        html,body{height:100%;margin:0;font-family:Arial,Helvetica,sans-serif}
        .center{height:100%;display:flex;align-items:center;justify-content:center}
        button{padding:12px 24px;font-size:16px;cursor:pointer}
    </style>
</head>
<body>
    <div class="center">
        <form method="post" action="">
            <button type="submit">Logout</button>
        </form>
    </div>
</body>
</html>