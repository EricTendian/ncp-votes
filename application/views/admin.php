<!DOCTYPE html>
<html>
  <head>
    <title>Northside Votes!</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Online polling place to select Northside College Prep's new Student Council.">
    <meta name="author" content="Eric Tendian">
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <style type="text/css">
      body {
        padding-top: 20px;
        padding-bottom: 40px;
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

      ul {
        list-style: none;
        margin: 0;
      }

      ul li {
        border-bottom: 1px solid #ccc;
      }

      ul li:last-child {
        border-bottom: none;
      }
    </style>
    <link href="css/bootstrap-responsive.css" rel="stylesheet">
  </head>
  <body>
    <div class="container">
      <div class="masthead">
        <h3 class="muted">Northside Votes!</h3>
      </div>

      <hr>

      <div class="jumbotron">
        <h1>Election Results</h1>
        <h3>Voter Turnout: <?php echo round(($totals['overall']/794)*100, 1); ?>%</h3>
        <h4>Freshmen: <?php echo round(($totals[1]/267)*100, 1); ?>%&nbsp;&bull;&nbsp;Sophomores: <?php echo round(($totals[2]/271)*100, 1); ?>%&nbsp;&bull;&nbsp;Juniors: <?php echo round(($totals[3]/261)*100, 1); ?>%<span style="display: none">&nbsp;&bull;&nbsp;Seniors: <?php echo round(($totals[4]/270)*100, 1); ?>%</span></h4>
        <!--<pre style="text-align: left"><?php print_r($totals); ?></pre>-->
      </div>

      <hr>

    <?php foreach ($results as $id=>$position) { ?>
      <?php if (intval($id)==1 || intval($id) % 4 === 1): ?><div class="row"><?php endif;?>
        <div class="span3">
          <h4><?php echo $position['title']; ?></h4>
          <ul>
          <?php if (count($position['candidates'])>0) { foreach ($position['candidates'] as $cid=>$candidate) { ?>
            <li>
              <h5><?php if ($position['name']=="presvp") { $presvp = unserialize($candidate['name']); echo '<span class="muted">President:</span> '.$presvp['pres'].'<br /><span class="muted">Vice President:</span> '.$presvp['vp']; } else echo $candidate['name']; ?></h5>
              <div class="progress">
                <?php
                  if (!isset($candidate['votes'])) $candidate['votes'] = 0;
                  if (intval($position['year'])>0 && $totals[intval($position['year'])]>0) $turnout = (intval($candidate['votes'])/$totals[intval($position['year'])])*100;
                  else if ($totals['overall']>0 && intval($position['year'])===0) $turnout = (intval($candidate['votes'])/$totals['overall'])*100;
                  else $turnout = 0;
                ?>
                <div class="bar" style="width: <?php echo $turnout; ?>%"><?php echo $candidate['votes']; ?> votes</div>
              </div>
            </li>
          <?php } } else echo '<li><h5>No candidates found.</h5></li>'; ?>
          </ul>
        </div>
      <?php if (intval($id) % 4 === 0): ?></div><hr><?php endif;?>
    <?php } ?>

      <div class="footer">
        <p>&copy; Northside College Preparatory H.S. 2013</p>
      </div>

    </div> <!-- /container -->
    <script src="http://code.jquery.com/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootbox.min.js"></script>
  </body>
</html>