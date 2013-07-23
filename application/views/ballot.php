<?php
function num2words( $num ){
   $ZERO = "zero";
   $MINUS = "minus";
   $lowName = array(
         /* zero is shown as "" since it is never used in combined forms */
         /* 0 .. 19 */
         "", "one", "two", "three", "four", "five",
         "six", "seven", "eight", "nine", "ten",
         "eleven", "twelve", "thirteen", "fourteen", "fifteen",
         "sixteen", "seventeen", "eighteen", "nineteen");

   $tys = array(
         /* 0, 10, 20, 30 ... 90 */
         "", "", "twenty", "thirty", "forty", "fifty",
         "sixty", "seventy", "eighty", "ninety");

   $groupName = array(
         /* We only need up to a quintillion, since a long is about 9 * 10 ^ 18 */
         /* American: unit, hundred, thousand, million, billion, trillion, quadrillion, quintillion */
         "", "hundred", "thousand", "million", "billion",
         "trillion", "quadrillion", "quintillion");

   $divisor = array(
         /* How many of this group is needed to form one of the succeeding group. */
         /* American: unit, hundred, thousand, million, billion, trillion, quadrillion, quintillion */
         100, 10, 1000, 1000, 1000, 1000, 1000, 1000) ;

   $num = str_replace(",","",$num);
   $num = number_format($num,2,'.','');
   $cents = substr($num,strlen($num)-2,strlen($num)-1);
   $num = (int)$num;

   $s = "";

   if ( $num == 0 ) $s = $ZERO;
   $negative = ($num < 0 );
   if ( $negative ) $num = -$num;

   // Work least significant digit to most, right to left.
   // until high order part is all 0s.
   for ( $i=0; $num>0; $i++ ) {
       $remdr = (int)($num % $divisor[$i]);
       $num = $num / $divisor[$i];
       // check for 1100 .. 1999, 2100..2999, ... 5200..5999
       // but not 1000..1099,  2000..2099, ...
       // Special case written as fifty-nine hundred.
       // e.g. thousands digit is 1..5 and hundreds digit is 1..9
       // Only when no further higher order.
       if ( $i == 1 /* doing hundreds */ && 1 <= $num && $num <= 5 ){
           if ( $remdr > 0 ){
               $remdr += $num * 10;
               $num = 0;
           } // end if
       } // end if
       if ( $remdr == 0 ){
           continue;
       }
       $t = "";
       if ( $remdr < 20 ){
           $t = $lowName[$remdr];
       }
       else if ( $remdr < 100 ){
           $units = (int)$remdr % 10;
           $tens = (int)$remdr / 10;
           $t = $tys [$tens];
           if ( $units != 0 ){
               $t .= "-" . $lowName[$units];
           }
       }else {
           $t = $inWords($remdr);
       }
       $s = $t . " " . $groupName[$i] . " "  . $s;
       $num = (int)$num;
   } // end for
   $s = trim($s);
   if ( $negative ){
       $s = $MINUS . " " . $s;
   }

   return $s;
} // end inWords
?>
<form id="ballot" action="./ballot" method="POST">
  <?php foreach ($positions as $id=>$position) { ?>
  <div class="position" id="<?php echo $position['name']; ?>" reqvotes="<?php echo $position['reqvotes']; ?>">
    <h4>Choose your <span class="position-title"><?php echo $position['title']; ?></span>.</h4>
    <?php
    if ($position['reqvotes']>1) {
      $type = 'checkbox';
      echo '<h5 class="muted">Please choose <em>'.strtoupper(num2words($position['reqvotes'])).'</em> candidates.</h5>';
    } else $type = 'radio';
    foreach ($position['candidates'] as $candidate) {
      if ($position['name']=="presvp") $presvp = unserialize($candidate['name']);
    ?>
    <label class="<?php echo $type; ?>">
      <input type="<?php echo $type; ?>" name="p<?php echo $id; ?>" value="<?php echo $candidate['id']; ?>"> <?php if ($position['name']=="presvp") echo '<span class="muted">President:</span> '.$presvp['pres'].'&nbsp;&bull;&nbsp;<span class="muted">Vice President:</span> '.$presvp['vp']; else echo $candidate['name']; ?>
    </label>
    <?php } ?>
  </div>
  <?php } ?>
  <button class="btn btn-large btn-block btn-success" style="margin-top: 1em;" type="submit">Cast Ballot</button>
</form>

<script>
$.getScript('js/ballot.js');
</script>