<!DOCTYPE html>
<html>
<head>
  <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
  <meta name="author" content="Orange Tsai">
  <title> Use After FLEE</title>
  <style>
    html,body{
        font-family: 'Courier New', Courier, 'Lucida Sans Typewriter', 'Lucida Typewriter', monospace;
    }


  </style>
</head>
<body>
  <div>
    Upload your SHELL and bypass the restriction :)
  </div>
  <br>
  <form method='POST' enctype='multipart/form-data'>
    <input type='file' name='name'><br>
    <input type='submit'>
  </form>
  <br><br>
</body>
</html>
<?php 
    $dir = 'sandbox/' . $_SERVER['REMOTE_ADDR'];
    if ( !file_exists($dir) )
        mkdir($dir);
    
    $file = $_FILES['name'];
    if ($file){
        $name = basename($file['name']);
        $name = $dir . '/' . $name;
        move_uploaded_file($file['tmp_name'], $name);

        echo 'Congratulations, your shell is at ' . $name; 
    }

?>