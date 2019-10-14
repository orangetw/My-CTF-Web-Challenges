<%@ Page Language="C#" %>

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
            <h1><font style="font-size: 200%">Buggy .Net</font></h1>
        </div>

        <div class='row justify-content-center'>
            <i> Here is the source for you: <a href='Default.txt'>Default.txt</a></i>
        </div>

        <br>
        <div class='row justify-content-center'>
            <div class="col-12 col-md-10 col-lg-12">
                <form class="card card-sm" method="POST" action="">
                    <div class="card-body row no-gutters align-items-center">
                        <div class="col">
                            <input class="form-control form-control-lg form-control-borderless" type="text" name="filename" placeholder="filename...">
                        </div>

                        <div class="col-auto">
                            <button class="btn btn-lg btn-success" type="submit">Send</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <br>
        <br>
        <div class='row justify-content-center'>
            <h3><font color='red'><% 

    bool isBad = false;
    try {
        if ( Request.Form["filename"] != null ) {
            isBad = Request.Form["filename"].Contains("..") == true;
        }
    } catch (Exception ex) {
        
    } 

    try {
        if (!isBad) {
            Response.Write(System.IO.File.ReadAllText(@"C:\inetpub\wwwroot\" + Request.Form["filename"]));
        }
    } catch (Exception ex) {

    }
%></font></h3>
        </div>
    </div>
</body>
</html>
