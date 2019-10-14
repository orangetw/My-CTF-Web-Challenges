<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <link rel="stylesheet" type="text/css" href="https://bootswatch.com/4/sketchy/bootstrap.min.css">
    <style type="text/css">
        .form-control-borderless {
            border: none;
        }

        .form-control-borderless:hover, .form-control-borderless:active, .form-control-borderless:focus {
            border: none;
            outline: none;
            box-shadow: none;
        }
    </style>
</head>
<body>
    <div class='container'>
        <br>
        <br>
        <div class='row justify-content-center'>
            <h1><font style="font-size: 200%">Report <a href='/fd.php?q=bugs'>bugs</a> to admin?</font></h1>
        </div>

        <br>
        <br>
        <div class='row justify-content-center'>
            <div class="col-12 col-md-10 col-lg-12">
                <form class="card card-sm" method="POST" action="">
                    <div class="card-body row no-gutters align-items-center">
                        <div class="col">
                            <input class="form-control form-control-lg form-control-borderless" type="text" name="url" placeholder="http://">
                        </div>

                        <div class="col-auto">
                            <button class="btn btn-lg btn-success" type="submit">Report</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <br>
        <br>
        <div class='row justify-content-center'>
            <h3><font color='red'><?php

                $url = @$_POST['url'];
                $config = json_decode(file_get_contents("/bot/config.json"));
                if (strlen($url) == 0) {
                    $msg = '';
                } else if (strlen($url) < 12 || substr($url, 0, strlen($config->base)) != $config->base) {
                    $msg = "Wrong URL :(";
                } else {
                    $key = sprintf("lock_%s", $_SERVER['REMOTE_ADDR']);
                    $redis = new Redis();
                    $redis->connect('127.0.0.1', 6379);
                    $redis->auth($config->password);

                    if($redis->get($key) != null) {
                        $msg = "Too fast :(";
                    } else {
                        $redis->set($key, "ok", 10);
                        $msg = @exec("python /bot/add_queue.py " . base64_encode($url));
                    }
                    $redis->close();
                }
                echo $msg

            ?></font></h3>
        </div>
    </div>
</body>
</html>
