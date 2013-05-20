<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Untitled Document</title>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    </head>
    <body>
        <div>
            <form>
                <?php
                require_once('countrystate.php');
                $countrystate_helper = new CountryState();
                echo $countrystate_helper->outputJquery('countrystate-request.php');
                ?>
                <label for="country">Country: </label>
                <?php echo $countrystate_helper->outputCountrySelectHtml(); ?><br />
                <label for="state">State/Province: </label>
                <?php echo $countrystate_helper->outputStateSelectHtml(); ?><br />
            </form>
        </div>
    </body>
</html>
