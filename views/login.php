<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta http-equiv="Content-Type"content="application/xhtml+xml; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="resources/css/style.css">
        <script src="resources/js/jquery.js"></script>
        <script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        <script src="resources/js/jquery.validate.min.js"></script>
        <script src="resources/js/messages_ja.js"></script>
        <!-- <link rel="apple-touch-icon" sizes="57x57" href="resources/icon/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="resources/icon/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="resources/icon/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="resources/icon/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="resources/icon/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="resources/icon/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="resources/icon/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="resources/icon/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="resources/icon/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="resources/icon/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="resources/icon/icon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="resources/icon/icon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="resources/icon/icon-16x16.png">
        <link rel="manifest" href="resources/icon/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="resources/icon/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff"> -->

        <title>KPI</title>
    </head>
    <body>
        <div class="main">
            <?php 
            //show data when debug
            // print_r($_GET);
            // print_r($_POST);
            // print_r($_FILES);
            // print_r($this->data); 
            ?>
            <?php require($this->viewFile); ?>
        </div>
    </body>
</html>