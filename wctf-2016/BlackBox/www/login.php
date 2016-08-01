<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">    
    <link rel="stylesheet" href="/static/bootstrap.min.css">
    <script src='/static/jquery-2.2.1.min.js'></script>

    <title> Black Box </title>


    <style type="text/css">
      body {
          overflow:hidden;
      }

      #output{
        margin-top: 20px;
      }

      .login-container{
          position: relative;
          width: 400px;
          margin: 0px auto;
          text-align: center;
          background: #fff;
          border: 1px solid #ccc;
      }


      .form-box input{
          width: 100%;
          padding: 10px;
          text-align: center;
          height:40px;
          border: 1px solid #ccc;;
          background: #fafafa;
          transition:0.2s ease-in-out;

      }

      .form-box input:focus{
          outline: 0;
          background: #eee;
      }

      .form-box input[type="text"]{
          border-radius: 5px 5px 0 0;
          text-transform: lowercase;
      }

      .form-box input[type="password"]{
          border-radius: 0 0 5px 5px;
          border-top: 0;
      }

      .form-box button.login{
          padding: 10px 20px;
      }

    </style>

    <script type="text/javascript">

      function login(t){
        var username = t.username.value;
        var password = t.password.value;

        if ( username && password ) {
          var data = {
            'username': username, 
            'password': password
          }
          $.get("/login/", data, function(data){
            $("#output").removeClass("alert alert-success alert-danger");
            $("#output").html(data.msg);
            if (data.msg != "login failed"){
              $("#output").addClass("alert alert-success");
            } else {
              $("#output").addClass("alert alert-danger")
            }
          }, "json");
        }
      }
    </script>
</head>

<body>
    <div class="container">

      <!-- Static navbar -->
      <nav class="navbar navbar-default">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Black Box</a>
          </div>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
              <li><a href="/">Home</a></li>
              <li><a href="/news.php">News</a></li>
              <li><a href="/waf.php">Waf</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
              <li class="active"><a href="/login.php">Login</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>

      <!-- Main component for a primary marketing message or call to action -->
      <div class="jumbotron">
        <h1>Login</h1>
        <p> Do you really need to login? </p>
        <br>
        <p>
          <div id="output" class="text-center"></div>
          <div class="login-container">
            <div class="form-box">
                <form method='POST' onsubmit='login(this);return false;'>
                    <input name="username" type="text" placeholder="username" autofocus="autofocus" >
                    <input type="password" name="password" placeholder="password">
                    <button class="btn btn-default btn-block login" type="submit"> Login </button>
                </form>
            </div>
          </div>
        </p>
      </div>

    </div> <!-- /container -->


</body>

</html>