<!DOCTYPE html>
<html>
  <head>
    <title>Northside Votes!</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Online polling place to select Northside College Prep's new Student Council.">
    <meta name="author" content="Eric Tendian">
    <meta charset='utf-8'>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <style type="text/css">
      body {
        padding-top: 20px;
        padding-bottom: 40px;
      }
      .fade {
        opacity: 0;
        -webkit-transition: opacity 0.15s linear;
        -moz-transition: opacity 0.15s linear;
        -o-transition: opacity 0.15s linear;
        transition: opacity 0.15s linear;
      }
      .fade.in {
        opacity: 1;
      }

      /* Custom container */
      .container-narrow {
        margin: 0 auto;
        max-width: 700px;
      }
      .container-narrow > hr {
        margin: 30px 0;
      }

      /* Main marketing message and sign up button */
      .jumbotron {
        margin: 60px 0;
        text-align: center;
      }
      .jumbotron h1 {
        font-size: 72px;
        line-height: 1;
      }
      .jumbotron .btn {
        font-size: 21px;
        padding: 14px 24px;
      }

      /* Supporting marketing content */
      .marketing {
        margin: 60px 0;
      }
      .marketing p + h4 {
        margin-top: 28px;
      }

      .form-signin {
        max-width: 300px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: #fff;
      }
      .form-signin .form-signin-heading,
      .form-signin .radio {
        margin-bottom: 10px;
      }
      .form-signin input[type="text"],
      .form-signin input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
      }

      .position {
        border-bottom: #ccc 1px solid;
        padding: 10px 0 20px 0;
      }
      .position:first-child {
        padding: 0 0 20px 0;
      }
      .position:nth-last-child(2) {
        padding: 10px 0 0 0;
        border: none;
      }
      span.position-title {
        font-variant: small-caps;
      }
    </style>
    <link href="css/bootstrap-responsive.css" rel="stylesheet">
    <link href="css/bootstrap-modal.css" rel="stylesheet" />
  </head>
  <body>
    <div class="container-narrow">
      <div class="masthead">
        <h3 class="muted">Northside Votes!</h3>
      </div>

      <hr>

      <div class="jumbotron">
        <h1>Student Council 2013-2014</h1>
        <p class="lead">Select your new Student Council by voting for who <em>you</em> think is the <em>best</em> candidate. Your vote does matter, so...</p>
        <a class="btn btn-large btn-primary" role="button" class="btn" href="#">Vote Now!</a>
      </div>
 
      <!-- Modal -->
      <div id="voteModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="voteModalLabel" aria-hidden="true">
        <div class="modal-header">
          <h3 id="voteModalLabel">Voting Booth</h3>
        </div>
        <div class="modal-body">
          <form class="form-signin" action="./login" method="POST">
            <h2 class="form-signin-heading">Please sign in</h2>
            <input type="text" class="input-block-level" placeholder="CPS Username" name="username">
            <input type="password" class="input-block-level" placeholder="CPS Password" name="password">
            <button class="btn btn-large btn-block btn-primary" type="submit">Sign In</button>
          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Cancel</button>
        </div>
      </div>

      <hr>

      <div class="row-fluid marketing">
        <div class="span6 offset3">
          <h4>Research the Candiates</h4>
          <p>Visit the Student Council website to check out the candidates and view their statements.</p>
          <a class="btn btn-large btn-block" href="http://northsideprep.org/ncphs/activities/clubs/scouncil/">The Candidates</a>
        </div>
      </div>

      <hr>

      <div class="footer">
        <p>&copy; Northside College Preparatory H.S. 2013</p>
      </div>

    </div> <!-- /container -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script>
    if (!window.jQuery) {
        document.write('<script src="js/jquery.min.js"><\/script>');
    }
    </script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootbox.min.js"></script>
    <script src="js/bootstrap-modalmanager.js"></script>
    <script src="js/bootstrap-modal.js"></script>
    <script src="js/home.js"></script>
  </body>
</html>