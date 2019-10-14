<?php
    $q = isset($_GET['q'])? $_GET['q']: '';
    $q = str_replace(array("\r", "\n", "/", "\\", "<", "."), "", $q);

    if( substr_count($q, "'") > 1) $q = str_replace("'", "", $q);
    if( substr_count($q, '"') > 1) $q = str_replace('"', "", $q);
    $host = $q . ".orange.ctf";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <script type="text/javascript">
        if (window.top == window.self) {
            window.self.location.href = "https://<?=$host;?>/oauth/authorize?client_id=1&scope=read&redirect_uri=https://twitter.com/orange_8361";
        } else {
            var data = JSON.stringify({
                message: 'CTF.API.remote',
                data: {
                    location: "https://<?=$host;?>/oauth/authorize?client_id=1&scope=read&redirect_uri=https://twitter.com/orange_8361"
                }
            });
            window.parent.postMessage(
                data, 
                "https://<?=$host;?>"
            );
        }
    </script>
</head>
<body>
</body>
</html>