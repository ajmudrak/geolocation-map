<?php
header('Content-type: text/plain; charset=utf-8');
$results = $_REQUEST;
?>
Form results:
<?php
print_r($results);
?>

Everything from Google:
<?php
print_r(json_decode($results['testeverything']));
