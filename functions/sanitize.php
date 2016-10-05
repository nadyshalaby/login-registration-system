<?php

function escape($string){
	return htmlentities($string, ENT_QUOTES, 'UTF-8');
}

/**
 * explode the the given string according to the specified array of delimiters
 * @param array $delimiters 
 * @param string $string 
 * @return array
 */
function multiexplode (array $delimiters,$string) {
    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return  $launch;
}
