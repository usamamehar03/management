<?php
namespace filter;
function sanitize_number($input,$key,&$errorlist)
{
    //validate number
    $result =str_replace(' ', '', $input);       //remove space
    if(!preg_match('/^[0-9]+$/', $result))     //check only number
    {
        $index=$key."Error";
        $errorlist[$index]="true";
    }
    $result=intval($result);
    return $result;
}
function sanitize_string($input,$key,&$errorlist)
{
    $result =preg_replace('/^\s+|\s+$|\s+(?=\s)/', '', $input);
    if(!preg_match ('/^[a-zA-Z\s]+$/', $result))
    {
        $index=$key."Error";
        $errorlist[$index]="true";
    }       
    return $result;
}
function filter_post($input,$cap = 0){
    $result = stripcslashes($input);         //remove back slash
    $result = filter_var($result, FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_HIGH);//remove tags and acsii codes values
    if($cap == 1){
        $result = ucwords($result);
    }
    return $result;
}

// function filter_post_breaks($input,$line_breaks = 0){
//     $result = strip_tags($input);
//     $result = stripcslashes($input);
//     if($line_breaks === 1){
//         $result =  str_replace("\r\n","<br>",$result);
//     }
//     return $result;
// }