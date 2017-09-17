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
<div class="container" >
    <div>
        <label>学号：<?= isset($_GET['account'])?$_GET['account']:'-' ?></label>
    </div>
    <div>
        <label>课程代号：<?= isset($_GET['key'])?$_GET['key']:'-' ?></label>
    </div>

    ​<iframe id="frame" src="catch.php<?= '?'.http_build_query($_GET) ?>" class="col-sm-12 col-xs-12" ></iframe>
</div>
</body>
<style>

    @media only screen and (min-width: 100px) and (max-width: 400px) {
        #frame{
            height: 400px;
        }
    }

    @media only screen and (min-width: 400px) and (max-width: 640px) {
        #frame{
            height: 640px;
        }
    }
    @media only screen and (min-width: 641px) and (max-width: 789px) {
        #frame{
            height: 750px;
        }
    }

    @media only screen and (min-width: 789px) {
        #frame{
            height: 800px;
        }
    }


</style>
</html>
