<?php
header('Content-type: text/plain; charset=utf-8');
$results = array_merge($_GET, $_POST);
?>
Form results:
<?php
print_r($results);
?>

Everything from Google:
<?php
print_r(json_decode($results['testeverything']));
