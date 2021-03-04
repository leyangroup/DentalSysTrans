<?php

function fdiMapping () {
    return [
        'e01f' =>	'48',
        'e01e' =>	'47',
        'e01d' =>	'46',
        'e01c' =>	'45',
        'e01b' =>	'44',
        'e01a' =>	'43',
        'e019' =>	'42',
        'e018' =>	'41',
        'e017' =>	'31',
        'e016' =>	'32',
        'e015' =>	'33',
        'e014' =>	'34',
        'e013' =>	'35',
        'e012' =>	'36',
        'e011' =>	'37',
        'e010' =>	'38',
        'e00f' =>	'28',
        'e00e' =>	'27',
        'e00d' =>	'26',
        'e00c' =>	'25',
        'e00b' =>	'24',
        'e00a' =>	'23',
        'e009' =>	'22',
        'e008' =>	'21',
        'e007' =>	'11',
        'e006' =>	'12',
        'e005' =>	'13',
        'e004' =>	'14',
        'e003' =>	'15',
        'e002' =>	'16',
        'e001' =>	'17',
        'e000' =>	'18',
        'e020' =>	'55',
        'e021' =>	'54',
        'e022' =>	'53',
        'e023' =>	'52',
        'e024' =>	'51',
        'e025' =>	'61',
        'e026' =>	'62',
        'e027' =>	'63',
        'e028' =>	'64',
        'e029' =>	'65',
        'e02a' =>	'75',
        'e02b' =>	'74',
        'e02c' =>	'73',
        'e02d' =>	'72',
        'e02e' =>	'71',
        'e02f' =>	'81',
        'e030' =>	'82',
        'e031' =>	'83',
        'e032' =>	'84',
        'e033' =>	'85',
        'e034' =>	'FM',
        'e035' =>	'UR',
        'e036' =>	'UL',
        'e037' =>	'LL',
        'e038' =>	'LR',
        'e039' =>	'UA',
        'e03a' =>	'LA',
        'e03b' =>	'',
        'e03c' =>	'',
        'e03d' =>	'',
        'e03e' =>	'',
        'e03f' =>	'99',

        '00faG' => '11',
        '00faF' => '12',
        '00faE' => '13',
        '00faD' => '14',
        '00faC' => '15',
        '00faB' => '16',
        '00faA' => '17',
        '00fa@' => '18',
        '00fa{' => '19',

        '00faH' => '21',
        '00faI' => '22',
        '00faJ' => '23',
        '00faK' => '24',
        '00faL' => '25',
        '00faM' => '26',
        '00faN' => '27',
        '00faO' => '28',
        '00fa|' => '29',

        '00faW' => '31',
        '00faV' => '32',
        '00faU' => '33',
        '00faT' => '34',
        '00faS' => '35',
        '00faR' => '36',
        '00faQ' => '37',
        '00faP' => '38',
        '00fa}' => '39',

        '00faX' => '41',
        '00faY' => '42',
        '00faZ' => '43',
        '00fa[' => '44',
        "00fa\\\\" => '45',
        '00fa]' => '46',
        '00fa^' => '47',
        '00fa_' => '48',
        '00fa~' => '49',

        '00fad' => '51',
        '00fac' => '52',
        '00fab' => '53',
        '00faa' => '54',
        '00fa`' => '55',

        '00fae' => '61',
        '00faf' => '62',
        '00fag' => '63',
        '00fah' => '64',
        '00fai' => '65',

        '00fan' => '71',
        '00fam' => '72',
        '00fal' => '73',
        '00fak' => '74',
        '00faj' => '75',

        '00fao' => '81',
        '00fap' => '82',
        '00faq' => '83',
        '00far' => '84',
        '00fas' => '85',

        '00fat' => 'FM',
        '00fau' => 'UR',
        '00fav' => 'UL',
        '00faw' => 'LL',
        '00fax' => 'LR',
        '00fay' => 'UA',
        '00faz' => 'LA',
        '00a1' => '99', // '00fa'=>'99',
    ];
}

        
function fdiConvert($string) {
    $string = trim($string);
    $unicodeString = json_encode($string);

    // switch (json_last_error()) {
    //     case JSON_ERROR_NONE:
    //         echo ' - No errors';
    //     break;
    //     case JSON_ERROR_DEPTH:
    //         echo ' - Maximum stack depth exceeded';
    //     break;
    //     case JSON_ERROR_STATE_MISMATCH:
    //         echo ' - Underflow or the modes mismatch';
    //     break;
    //     case JSON_ERROR_CTRL_CHAR:
    //         echo ' - Unexpected control character found';
    //     break;
    //     case JSON_ERROR_SYNTAX:
    //         echo ' - Syntax error, malformed JSON';
    //     break;
    //     case JSON_ERROR_UTF8:
    //         echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
    //     break;
    //     default:
    //         echo ' - Unknown error';
    //     break;
    // }

    // var_dump($unicodeString);

    $replacedString = str_replace('"', '', $unicodeString);
    $fdiUnicodes = explode('\\u', $replacedString);

    unset($fdiUnicodes[0]);

    $result = '';
    $fdiMapping = fdiMapping();

    foreach ($fdiUnicodes as $unicode) {
        $result .= $fdiMapping[$unicode];
    }

    return $result;
}