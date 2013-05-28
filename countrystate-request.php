<?php

$referer = $_SERVER['HTTP_REFERER'];
if (!$referer) {
    die('Access denied.');
}
$referer_url = parse_url($referer);
if ($referer_url['host'] != $_SERVER['HTTP_HOST']) {
    die('Access denied.');
}

require_once('countrystate.php');

error_reporting(E_ERROR | E_USER_ERROR);

$type = $_REQUEST['type'];
$country = $_REQUEST['country'];
$countryValues = $_REQUEST['country_values'];
$stateValues = $_REQUEST['state_values'];
$countryid = $_REQUEST['country_id'];
$stateid = $_REQUEST['state_id'];

$countrystate_helper = new CountryState();
$countrystate_helper->useCodesForCountry = $countryValues == 'code';
$countrystate_helper->useCodesForState = $stateValues == 'code';
$countryid = $countryid ? $countryid : 'country';
$stateid = $stateid ? $stateid : 'state';

$url = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

switch ($type) {
    case 'json':
        header('Content-type: application/json; charset=utf-8');
        echo $countrystate_helper->outputJson($country);
        break;
    case 'json-all':
        header('Content-type: application/json; charset=utf-8');
        echo $countrystate_helper->outputJsonAll();
        break;
    case 'jquery':
        header('Content-type: text/html; charset=utf-8');
        echo $countrystate_helper->outputJquery($url, $countryid, $stateid);
        break;
    case 'html-country':
        header('Content-type: text/html; charset=utf-8');
        echo $countrystate_helper->outputCountrySelectHtml(true, $countryid);
        break;
    case 'html-state':
        header('Content-type: text/html; charset=utf-8');
        echo $countrystate_helper->outputStateSelectHtml($stateid);
        break;
    case 'html-jquery':
        header('Content-type: text/html; charset=utf-8');
        echo $countrystate_helper->outputJqueryHtmlWithSelectLists($url, $countryid, $stateid);
        break;
    case 'html-static':
        header('Content-type: text/html; charset=utf-8');
        echo $countrystate_helper->outputStaticStateUpdateScript($countryid, $stateid);
        break;
    case 'html':
        header('Content-type: text/html; charset=utf-8');
        echo $countrystate_helper->outputAllStaticHtml($countryid, $stateid);
        break;
    case 'text':
    default:
        header('Content-type: text/plain; charset=utf-8');
        echo $countrystate_helper->outputText();
        break;
}
