<?php

function writeHead() {
    echo '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="./css/test.css" />
   <!-- 
     <script type="text/javascript">
    if (window.location.protocol == "http:") {
        var restOfUrl = window.location.href.substr(5);
        window.location = "https:" + restOfUrl;
    }
</script>-->
    <script>
        function toggleClass(element,class_name){
            var regex_1 = new RegExp(\'(?:^|\\\s)\'+class_name+\'(?!\\\S)\');
            if ( element.className.match(regex_1) ){
                 var regex_2 = new RegExp(\'(?:^|\\\s)\'+class_name+\'(?!\\\S)\',\'g\');
                 element.className = element.className.replace(regex_2, \'\' );
            } else {
                element.className += " "+class_name;
            }
        }
        
    </script>
</head>';
}


function formatDate($date_txt, $locale='fr_FR') {
$ts = new DateTime($date_txt);
$formatter = new IntlDateFormatter($locale, IntlDateFormatter::LONG, IntlDateFormatter::LONG);

$formatter->setPattern('EEEE');
$result =$formatter->format($ts);
$formatter->setPattern('d');
$result =$result.' '.$formatter->format($ts);
$formatter->setPattern('MMMM');
$result =$result.' '.$formatter->format($ts);
$formatter->setPattern('Y');
$result =$result.' '.$formatter->format($ts);
return $result;   
}





