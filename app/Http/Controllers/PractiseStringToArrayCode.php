
<?php

function splitStringIntoGroups(String $string)
{
    $arrayData = str_split($string); //string to arrayData

    //get length in variable $len
    $len = array_count_values($arrayData);

    //build array with groups
    foreach ($len as $key => $val) {
        unset($matchstring);
        for ($i = 1; $i <= $val; $i++) {
            $matchstring[] = $key;
        }
        $FinalArr[] = implode(" ", $matchstring);
    }
    $outputString = '["' . implode('", "', $FinalArr) . '"]';

    echo $outputString;
}

$string = "aabbbcccdddz51598696&&##555555####%%%%%!@#$%^&*";
splitStringIntoGroups($string);

?>
