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

$countrystate_helper = new CountryState();
$countrystate_helper->useCodesForCountry = $countryValues != 'name';
$countrystate_helper->useCodesForState = $stateValues != 'name';

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
        echo $countrystate_helper->outputJquery($url);
        break;
    case 'html-country':
        header('Content-type: text/html; charset=utf-8');
        echo $countrystate_helper->outputCountrySelectHtml();
        break;
    case 'html-state':
        header('Content-type: text/html; charset=utf-8');
        echo $countrystate_helper->outputStateSelectHtml();
        break;
    case 'html-jquery':
        header('Content-type: text/html; charset=utf-8');
        echo $countrystate_helper->outputJqueryHtmlWithSelectLists($url);
        break;
    case 'html':
        header('Content-type: text/html; charset=utf-8');
        echo $countrystate_helper->outputAllStaticHtml();
        break;
    case 'text':
    default:
        header('Content-type: text/plain; charset=utf-8');
        echo $countrystate_helper->outputText();
        break;
}
