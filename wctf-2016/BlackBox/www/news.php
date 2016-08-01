<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">    
    <link rel="stylesheet" href="/static/bootstrap.min.css">
    <script src='/static/jquery-2.2.1.min.js'></script>

    <title> Black Box </title>

    <script type="text/javascript">
      
      $.getJSON("/news/", function(data){
        for(index in data){
          var record = data[index];
          var tr = $("<tr>");
          tr.append( $("<td>").text(record.id) );

          var a = $("<a>").text(record.title);
          a.attr("href", "/show.php?id="+record.id);
          tr.append( $("<td>").append(a) );
          tr.append( $("<td>").text(record.hits) );

          $("#news").append(tr);
        }
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
        <h1>News</h1>
        <p> What happened today? </p>
        <br>
        <p>
          <table class="table table-striped table-condensed table-hover" id="news">
            <tr>
              <th width=10%> # </th>
              <th width=80%> TITLE </th>
              <th width=10%> HITS </th>
            </tr>
          </table>
        </p>
      </div>

    </div> <!-- /container -->


</body>

</html>