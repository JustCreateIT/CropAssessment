To get a really clean json string use these three constants like so:

<?php
$array = ['€', 'http://example.com/some/cool/page', '337'];
$bad   = json_encode($array); 
$good  = json_encode($array,  JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);

// $bad would be  ["\u20ac","http:\/\/example.com\/some\/cool\/page","337"]
// $good would be ["€","http://example.com/some/cool/page",337]
?> 