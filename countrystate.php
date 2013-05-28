<?php

class CountryState {

    var $useCodesForCountry = false;
    var $useCodesForState = false;
    
    var $countrylist = null;
    var $statelist = null;

    public function outputJson($country) {
        $this->createLists();
        echo json_encode($this->statelist[$country]);
    }

    public function outputJsonAll() {
        $this->createLists();
        $output = new stdClass();
        $output->countrylist = $this->countrylist;
        $output->statelist = $this->statelist;
        echo json_encode($output);
    }

    public function outputJquery($url, $countryid = 'country', $stateid = 'state') {
        if (!$url) {
            $url = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }
        return $this->html_jquery($url, $countryid, $stateid);
    }

    public function outputJqueryHtmlWithSelectLists($url, $countryid = 'country', $stateid = 'state') {
        if (!$url) {
            $url = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }
        $this->createLists();
        return $this->html_country(true)
                . $this->html_state($stateid)
                . $this->html_jquery($url, $countryid, $stateid);
    }

    public function outputCountrySelectHtml($unintrusiveJavascript = true, $id = 'country') {
        $this->createLists();
        return $this->html_country($unintrusiveJavascript, $id);
    }

    public function outputStateSelectHtml($id = 'state') {
        return $this->html_state($id);
    }
    
    public function outputStaticStateUpdateScript($countryid = 'country', $stateid = 'state') {
        return $this->html_updateStateListOptions($countryid, $stateid);
    }

    public function outputAllStaticHtml($countryid = 'country', $stateid = 'state') {
        $this->createLists();
        return $this->html_country(false, $countryid)
                . $this->html_state($stateid)
                . $this->html_updateStateListOptions($countryid, $stateid);
    }

    public function outputText() {
        $this->createLists();
        return print_r($this->countrylist, true)
                . print_r($this->statelist, true);
    }

    private function createLists() {
        if ($this->countrylist && $this->countrylist) {
            return;
        }

        // Data from country-state-data.txt is downloaded from: http://www.commondatahub.com/live/geography/state_province_region/iso_3166_2_state_codes
        // Rows to download: All
        // Format: Delimited
        // Delimiter: Tab
        // Text qualifier: Double quotes
        // Show column headers?: CHECKED
        // Include all columns: Country Name, ISO 3166-2 Sub-division/State Code, ISO 3166-2 Subdivision/State Name, ISO 3166-2 Primary Level Name, Subdivision/State Alternate Names, ISO 3166-2 Subdivision/State Code (with *), Subdivision CDH ID, Country CDH ID, Country ISO Char 2 Code, Country ISO Char 3 Code
        $data = file_get_contents(__DIR__ . '/countrystate-data.txt');
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

        $countrylist = array();
        $statelist = array();
        $matchlist = array();
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
            for ($linenum = 1; $linenum < count($matchlist); $linenum++) {
                $match = $matchlist[$linenum];
                if (count($match) !== $colcount + 1) {
                    trigger_error('Line ' . $linenum . ' has an incorrect number of columns.', E_USER_ERROR);
                }
                $item = array();
                for ($i = 1; $i <= $colcount; $i++) {
                    $item[$columns[$i - 1]] = $match[$i];
                }
                $ccode = $this->useCodesForCountry ? strtoupper(trim($item['COUNTRY ISO CHAR 2 CODE'])) : trim($item['COUNTRY NAME']);
                if (!$statelist[$ccode]) {
                    $statelist[$ccode] = array();
                }
                $scode = $this->useCodesForState ? preg_replace('@^' . $ccode . '-@', '', strtoupper(trim($item['ISO 3166-2 SUB-DIVISION/STATE CODE']))) : trim($item['ISO 3166-2 SUBDIVISION/STATE NAME']);
                $statelist[$ccode][$scode] = preg_replace('@\s*\(see also separate entry under .*?\)@i', '', trim($item['ISO 3166-2 SUBDIVISION/STATE NAME']));
                if (!$countrylist[$ccode]) {
                    $countrylist[$ccode] = trim($item['COUNTRY NAME']);
                }
            }
        }

        asort($countrylist, SORT_STRING | SORT_FLAG_CASE);
        foreach ($countrylist as $ccode => $country) {
            $countrylist[$ccode] = array(
                'country_name' => $country,
                'country_code' => $ccode
            );
        }
        $this->countrylist = array_values($countrylist);

        foreach ($statelist as $ccode => $states) {
            asort($statelist[$ccode], SORT_STRING | SORT_FLAG_CASE);
            foreach ($statelist[$ccode] as $scode => $statename) {
                $statelist[$ccode][$scode] = array(
                    'state_name' => $statename,
                    'state_code' => $scode
                );
            }
            $statelist[$ccode] = array_values($statelist[$ccode]);
        }
        $this->statelist = $statelist;
    }

    private function html_country($uninstrusive = false, $id = 'country') {
        $output = '
<select name="' . htmlspecialchars($id) . '" id="' . htmlspecialchars($id) . '"' . ($uninstrusive ? '' : ' onchange="javascript:updateStateListOptions();"') . '>
  <option value="">[ Choose Country ]</option>' . "\n";
        foreach ($this->countrylist as $countryinfo) {
            $output .= '  <option value="' . htmlspecialchars($countryinfo['country_code']) . '">' . htmlspecialchars($countryinfo['country_name']) . '</option>' . "\n";
        }
        $output .= '
</select>
';
        return $output;
    }

    private function html_state($id = 'state') {
        return '
<select name="' . htmlspecialchars($id) . '" id="' . htmlspecialchars($id) . '">
  <option value="">[ Choose State/Province ]</option>
</select>
';
    }

    private function script_updateStateListOptions($countryid = 'country', $stateid = 'state') {
        return '
    var updateStateListOptions = function() {
        var country = document.getElementById("' . str_replace('"', '\\"', $countryid) . '");
        var state = document.getElementById("' . str_replace('"', '\\"', $stateid) . '");
        var countryVal = country.value;
        // clear state list
        state.selectedIndex = 0;
        var length = state.options.length;
        for (i = 1; i < length; i++) {
            state.remove(1);
        }
        if (country.value) {
            // activate new states
            var list = stateData[country.value];
            var listlength = list.length;
            for (i = 0; i < listlength; i++) {
                var option = document.createElement("option");
                option.value = list[i].state_code;
                option.text = list[i].state_name;
                state.add(option, null);
            }
        }
    }
';
    }

    private function script_jquery($url, $countryid = 'country', $stateid = 'state') {
        return '
    var stateData = {};
    ' .
                $this->script_updateStateListOptions() .
                '
    var updateStateList = function(callback) {
        var countrycode = $("#' . str_replace('"', '\\"', $countryid) . '").val();
        if (countrycode && !stateData[countrycode]) {
            $.ajax({
                url: "' . str_replace('"', '\\"', $url) . '",
                data: { 
                    type: "json", 
                    country: countrycode' 
                . ($this->useCodesForCountry ? ', country_values: "code"' : '') 
                . ($this->useCodesForState ? ', state_values: "code"' : '') 
                . ($countryid == 'country' ? '' : ', country_id: "' . str_replace('"', '\\"', $countryid) . '"') 
                . ($stateid == 'state' ? '' : ', state_id: "' . str_replace('"', '\\"', $stateid) . '"') 
                . ' },
                method: "GET",
                success: function (data) {
                    stateData[countrycode] = data;
                    if (countrycode == $("#' . str_replace('"', '\\"', $countryid) . '").val()) {
                        updateStateListOptions();
                    }
                    if (typeof callback == "function") {
                        callback();
                    }
                }
            });        
        } else {
            updateStateListOptions();
            if (typeof callback == "function") {
                callback();
            }
        }
    };
    $(function () {
        $("#' . str_replace('"', '\\"', $countryid) . '").change(function () {
            updateStateList();
        });
    });
    ';
    }

    private function html_jquery($url, $countryid = 'country', $stateid = 'state') {
        return '
<script type="text/javascript">
' .
                $this->script_jquery($url, $countryid, $stateid) .
                '
</script>
';
    }

    private function html_updateStateListOptions($countryid = 'country', $stateid = 'state') {
        return '
<script type="text/javascript">
' .
                $this->script_updateStateListOptions($countryid, $stateid) .
                '
    var stateData = ' . json_encode($this->statelist) . ';
</script>
';
    }

}
