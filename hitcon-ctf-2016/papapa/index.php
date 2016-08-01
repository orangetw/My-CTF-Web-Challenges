<?php
    if ($_SERVER["SERVER_PORT"] != 443) {
        $redir = "Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
        header($redir);
    }
?>
<pre>
Use your force to find the secret behind this website!

If you are experienced in pentesting, you will solve it quickly :)
