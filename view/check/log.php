<!DOCTYPE html>
<html>
<head>
    <title><?=$title?></title>
    <meta charset="UTF-8">
    <script src="http://<?=HOST?>/jquery-3.2.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>
<body>
<div class="row" >
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <h1><?=$title?>__共<?=count($raw)?>次<?=count($ip)?>人访问</h1>
        <h2>IP</h2>
        <table class="table table-hover">
            <tr>
                <td>编号</td>
                <td>ip</td>
                <td>数量</td>
            </tr>
            <?php
            $ip_count = 1;
            foreach($ip as $k=>$v){
                ?>
                <tr>
                    <td><?=$ip_count?></td>
                    <td><?=$k?></td>
                    <td><?=$v?></td>
                </tr>
                <?php
                $ip_count ++;
            }
            ?>
        </table>
        <h2>HOURS</h2>
        <table class="table table-hover">
            <tr>
                <td>编号</td>
                <td>hours</td>
                <td>数量</td>
            </tr>
            <?php
            $hours_count = 1;
            foreach($hours as $k=>$v){
                ?>
                <tr>
                    <td><?=$hours_count?></td>
                    <td><?=$k?></td>
                    <td><?=$v?></td>
                </tr>
                <?php
                $hours_count ++;
            }
            ?>
        </table>
        <h2>API</h2>
        <table class="table table-hover">
            <tr>
                <td>编号</td>
                <td>api</td>
                <td>数量</td>
            </tr>
            <?php
            $api_count = 1;
            foreach($api as $k=>$v){
                ?>
                <tr>
                    <td><?=$api_count?></td>
                    <td><?=$k?></td>
                    <td><?=$v?></td>
                </tr>
                <?php
                $api_count ++;
            }
            ?>
        </table>
        <h2>RAW</h2>
        <table class="table table-hover">
            <tr>
                <td>编号</td>
                <td>raw_log</td>
            </tr>
            <?php
            foreach($raw as $k=>$v){
                ?>
                <tr>
                    <td><?=$k?></td>
                    <td><?=$v?></td>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>
    <div class="col-md-2"></div>
</div>

</body>
</html>