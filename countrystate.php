<?php

// Rows to download: All
// Format: Delimited
// Delimiter: Tab
// Text qualifier: Double quotes
// Show column headers?: CHECKED
// Include all columns: Country Name, ISO 3166-2 Sub-division/State Code, ISO 3166-2 Subdivision/State Name, ISO 3166-2 Primary Level Name, Subdivision/State Alternate Names, ISO 3166-2 Subdivision/State Code (with *), Subdivision CDH ID, Country CDH ID, Country ISO Char 2 Code, Country ISO Char 3 Code
$data = file_get_contents('countrystate-data.txt');
$data = mb_convert_encoding($data, 'UTF-8', mb_detect_encoding($data, 'UTF-8, ISO-8859-1', true));
$textqualifier = '"';
$columns = array(
    'COUNTRY NAME',
    'ISO 3166-2 SUB-DIVISION/STATE CODE',
    'ISO 3166-2 SUBDIVISION/STATE NAME',
    'ISO 3166-2 PRIMARY LEVEL NAME',
    'SUBDIVISION/STATE ALTERNATE NAMES',
    'ISO 3166-2 SUBDIVISION/STATE CODE (WITH *)',
    'SUBDIVISION CDH ID',
    'COUNTRY CDH ID',
    'COUNTRY ISO CHAR 2 CODE',
    'COUNTRY ISO CHAR 3 CODE'
);
$colcount = count($columns);
$separator = '\t';
// '([^"\t]*|(?:"[^"]*")+)\t'
$regexcolumn = '([^' . $textqualifier . $separator . ']*|(?:' . $textqualifier . '[^' . $textqualifier . ']*' . $textqualifier . ')+)' . $separator;
$regexline = str_repeat($regexcolumn, $colcount);
$regexline = '@^' . substr($regexline, 0, strlen($regexline) - strlen($separator)) . '$@m';

$datalist = array();
$countrylist = array();
$statelist = array();
if (preg_match_all($regexline, $data, $matchlist, PREG_SET_ORDER)) {
    $header = $matchlist[0];
    if (count($header) !== $colcount + 1) {
        trigger_error('Unexpected header in Country/State data file.  Incorrect number of columns (' . count($header) - 1 . ').', E_USER_ERROR);
    }
    for ($i = 1; $i <= count($header); $i++) {
        if ($header[$i] !== $columns[$i - 1]) {
            trigger_error('Unexpected header in Country/State data file.  Column names do not match.', E_USER_ERROR);
        }
    }
    $linenum = 0;
    for ($linenum = 1; $linenum < count($matchlist); $linenum++) {
        $match = $matchlist[$linenum];
        $linenum++;
//        if (count($match) !== $colcount + 1) {
//            trigger_error('Line ' . $linenum . ' has an incorrect number of columns.', E_USER_WARNING);
//        }
        $item = array();
        for ($i = 1; $i <= $colcount; $i++) {
            $item[$columns[$i - 1]] = $match[$i];
        }
        $ccode = trim($item['COUNTRY ISO CHAR 2 CODE']);
        if (!$statelist[$ccode]) {
            $statelist[$ccode] = array();
        }
        if ($ccode == 'BA') {
            $xx++;
        }
        $scode = trim($item['ISO 3166-2 SUB-DIVISION/STATE CODE']);
        $statelist[$ccode][$scode] = $item['ISO 3166-2 SUBDIVISION/STATE NAME'];
        $countrylist[$ccode] = $item['COUNTRY NAME'];

        $datalist[] = $item;
    }
}

$type = $_REQUEST['type'];
switch ($type) {
    case 'json':
        header('Content-type: application/json; charset=utf-8');
        $output = new stdClass();
        $output->countrylist = $countrylist;
        $output->statelist = $statelist;
        echo json_encode($output);
        break;
    case 'script':
        break;
    case 'html':
        break;
    case 'text':
    default:
        header('Content-type: text/plain; charset=utf-8');
        $statecount = 0;
        foreach ( $statelist as $x => $st ) {
            $statecount = $statecount + count($st);
        }
        echo "Test:$xx\nLines: $linenum\nStates: " . $statecount . "\nCountries: " . count($countrylist) . "\n";
        print_r($countrylist);
        print_r($statelist);
        break;
}
