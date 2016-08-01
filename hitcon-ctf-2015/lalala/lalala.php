<!DOCTYPE html>
<html>
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
    <meta name="author" content="Orange Tsai">
    <title> lalala | upload your photo </title>
    <link rel="stylesheet" href="//bootswatch.com/cyborg/bootstrap.min.css">
    <style type="text/css">
        .hide-bullets {
            list-style:none;
            margin-left: -40px;
            margin-top:20px;
        }

        .thumbnail {
            padding: 0;
            background: none;
            border: none;
        }

        img {
            max-width: 150px !important;
            max-height: 150px !important;
        }

        small {
            font-weight: 100 !important;
        }



        .carousel-inner>.item>img, .carousel-inner>.item>a>img {
            width: 100%;
        }
    </style>
</head>
<body>

<?php 

    error_reporting(E_ALL^E_NOTICE);

    function alert($msg){
        die( '<script> alert("' . $msg . '");window.history.back(-1);</script>' );
    }

    function check_image($data){
        if ( strlen($data) == 0 ){
            alert( 'file error' );
        }

        if ( strstr($data, "\x00") == False and is_file($data) ){
            $info = getimagesize($data);
        } else {
            $info = getimagesizefromstring($data);
        }

        $width  = $info[0];
        $height = $info[1];
        $mime   = $info['mime'];

        if ( $width > 512 or $height > 512 ){
            alert( 'image too large' );
        }

        // check type
        $types = array( 'image/gif', 'image/jpg', 'image/jpeg' );
        if ( !in_array( strtolower($mime) , $types)  ){
            alert( 'content error:' . htmlentities($data) ) ;
        }

        return 1;

    }


    $DIR  = '/_www/uploads/';
    $mode = $_POST['mode'];

    if ( $mode == 'upload' ){

        $file = $_FILES['file'];
        $filename = $_SERVER['REMOTE_ADDR'] . '.jpg';

        if( check_image($file['tmp_name']) )
            move_uploaded_file($file['tmp_name'], $DIR . $filename);

    } else if ( $mode == 'url' ){

        $url = $_POST['url'];
        $filename = $_SERVER['REMOTE_ADDR'] . '.jpg';

        $allowed_ext = array('jpg', 'gif', 'png', 'jpeg');
        if ( !in_array(pathinfo($url)['extension'], $allowed_ext) ){
            alert( 'ext not allow' );
        }

        if ( substr($url, 0, 7) != 'http://' ){
            alert( 'protocol error' );
        }
        if ( stristr($url, '.php') != False ){
            alert( 'what do you do?' );
        }

        if ( stristr($url, 'file://') != False ){
            alert( 'what do you do?' );
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_ALL);
        $data = curl_exec($ch);

        $http_info = curl_getinfo($ch);
        if ( $http_info['http_code'] == 404 ){
            alert('not found');
        } else{
            if ( check_image($data) )
                file_put_contents($DIR . $filename, $data);
        }


        curl_close($ch);
    }


    $files = glob("uploads/*");
    $files = array_combine($files, array_map("filemtime", $files));
    arsort($files);
    $files = array_keys($files);

    $latest = array();
    for ($i=0; $i<10; $i++){
        $latest[$i] = $files[$i]? $files[$i]: 'unknown.jpg';
    }

?>

<div class="container">
    <div id="main_area">
        <div class="row">
            <div class="page-header center">
                <h1> lalala <small> upload your photo </small> </h1> 
            </div>

        </div>
        <div class="row">
            <div class="col-sm-3 col-md-offset-3">
                upload from file
                <form method="post" enctype="multipart/form-data">
                    <input type='hidden' name='mode' value='upload'>
                    <input type='file' name='file'  >
                    <input type='submit' value='submit'> 
                    
                </form>
            </div>

            <div class="col-sm-3">
                upload from url
            <form method="post" enctype="multipart/form-data">
                <input type='hidden' name='mode' value='url'>
                <input type='text' name='url'> <br>
                <input type='submit' value='submit'> 
                
            </form>
            </div>

        </div>
        <div class="row">
            <div class="col-sm-12" id="slider-thumbs">
                <ul class="hide-bullets">
                    <li class="col-sm-4">
                        <a class="thumbnail"><img src="<?php echo $latest[0];?>"></a>
                    </li>

                    <li class="col-sm-4">
                        <a class="thumbnail"><img src="<?php echo $latest[1];?>"></a>
                    </li>

                    <li class="col-sm-4">
                        <a class="thumbnail"><img src="<?php echo $latest[2];?>"></a>
                    </li>

                    <li class="col-sm-4">
                        <a class="thumbnail"><img src="<?php echo $latest[3];?>"></a>
                    </li>

                    <li class="col-sm-4">
                        <a class="thumbnail"><img src="<?php echo $latest[4];?>"></a>
                    </li>

                    <li class="col-sm-4">
                        <a class="thumbnail"><img src="<?php echo $latest[5];?>"></a>
                    </li>
                    <li class="col-sm-4">
                        <a class="thumbnail"><img src="<?php echo $latest[6];?>"></a>
                    </li>

                    <li class="col-sm-4">
                        <a class="thumbnail"><img src="<?php echo $latest[7];?>"></a>
                    </li>

                    <li class="col-sm-4">
                        <a class="thumbnail"><img src="<?php echo $latest[8];?>"></a>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</div>


</body>
</html>