<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <link rel="stylesheet" type="text/css" href="bootstrap.min.css">
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
            <h1><font style="font-size: 160%">Report a malicious <a href='/upload.php'>URL</a> to admin?</font></h1>
        </div>

        <br>
        <br>
        <div class='row justify-content-center'>
            <div class="col-10 col-md-10 col-lg-10">
                <form class="card card-sm" method="POST" action="">
                    <div class="card-body row no-gutters align-items-center">
                        <div class="col">
                            <input class="form-control form-control form-control-borderless" type="text" name="url" placeholder="http://">
                        </div>

                        <div class="col-auto">
                            <button class="btn btn btn-success" type="submit">Report</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <br>
        <br>
        <div class='row justify-content-center'>
            <h3><font color='red'><?php
                error_reporting(E_ALL^E_NOTICE);

                $url = (string)$_POST['url'];
                if (strlen($url) != 0) {
                    $cmd = sprintf("python3 add_queue.py %s %s", 
                                   escapeshellarg($_SERVER['REMOTE_ADDR']), 
                                   escapeshellarg(base64_encode($url)) );
                    $msg = @exec($cmd);
                }

                echo htmlentities($msg);

            ?></font></h3>
        </div>
    </div>
</body>
</html>