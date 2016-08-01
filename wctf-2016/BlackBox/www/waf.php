<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">    
    <link rel="stylesheet" href="/static/bootstrap.min.css">
    <script src='/static/jquery-2.2.1.min.js'></script>

    <title> Black Box </title>

    <style type="text/css">
      #output{
        margin-top: 20px;
      }
    </style>

    <script type="text/javascript">
      function test(t){
        var query = t.query.value;
        if (query){
          var data = {
            'query': query
          }
          $.get("/waf/", data, function(data){
            $("#output").removeClass("alert alert-success alert-danger");
            $("#output").html(data.msg);
            if (data.msg == "legal"){
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
              <li class="active"><a href="/waf.php">Waf</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
              <li><a href="/login.php">Login</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>

      <!-- Main component for a primary marketing message or call to action -->
      <div class="jumbotron">
        <h1>Waf Testing</h1>
        <p> Testing your SQL Injection payload </p>
        <br>
        <p>
          <form method='POST' onsubmit='test(this);return false;'>
            <div class="input-group input-group-lg">
              <input type="text" name="query" class="form-control" placeholder="' or 1=1--">
              <span class="input-group-btn">
                <input class="btn btn-default" type="submit" value="Test!">
              </span>

            </div>
          </form>
          <div id="output" class="text-center"></div>
        </p>
      </div>

    </div> <!-- /container -->


</body>

</html>