<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>华工通选biubiubiu</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <script src="bootstrap/js/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=no">
</head>
<body>
<nav class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="">华工通选biubiubiu</a>
        </div>
    </div>
</nav>
<div class="container">
    <div>
        <label>学号：<?= isset($_GET['account'])?$_GET['account']:'-' ?></label>
    </div>
    <div>
        <label>课程代号：<?= isset($_GET['key'])?$_GET['key']:'-' ?></label>
    </div>

    ​<iframe src="catch.php<?= '?'.http_build_query($_GET) ?>" class="col-sm-12 col-xs-12" height="800px"></iframe>
</div>
</body>
</html>
