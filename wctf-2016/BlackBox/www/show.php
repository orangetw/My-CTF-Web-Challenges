<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">    
    <link rel="stylesheet" href="/static/bootstrap.min.css">
    <script src='/static/jquery-2.2.1.min.js'></script>

    <title> Black Box </title>

    <script type="text/javascript">
      
      function getParameterByName(name, url) {
          if (!url) url = window.location.href;
          name = name.replace(/[\[\]]/g, "\\$&");
          var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
              results = regex.exec(url);
          if (!results) return null;
          if (!results[2]) return '';
          return decodeURIComponent(results[2].replace(/\+/g, " "));
      }

      $.getJSON("/news/?id="+getParameterByName("id"), function(data){
        $(".jumbotron").append($("<h1>").text(data.title));
        $(".jumbotron").append($("<br>"));
        $(".jumbotron").append($("<p>").text(data.content));
      });

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
              <li class="active"><a href="/news.php">News</a></li>
              <li><a href="/waf.php">Waf</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
              <li><a href="/login.php">Login</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>

      <!-- Main component for a primary marketing message or call to action -->
      <div class="jumbotron">
      </div>

    </div> <!-- /container -->


</body>

</html>