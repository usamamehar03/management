<?php
namespace filter;
//validation
function validate_float($input,$key,&$errorlist)
{
    //validate float
    $result =str_replace(' ', '', $input);       //remove space
    if(!preg_match('/^-?(?:\d+|\d*\.\d+)$/', $result))     //check only number
    {
        $index=$key."Error";
        $errorlist[$index]="true";
    }
    $result=floatval($result);
    return $result;
}
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
function sanitize_notes($input,$key,&$errorlist)
{
    $result =preg_replace('/^\s+|\s+$|\s+(?=\s)/', '', $input);
    if(!preg_match ('/^[a-zA-Z.,\s]+$/', $result))
    {
        $index=$key."Error";
        $errorlist[$index]="true";
    }       
    return $result;
}

function sanitize_string($input,$key,&$errorlist)
{
    $result =preg_replace('/^\s+|\s+$|\s+(?=\s)/', '', $input);
    if(!preg_match ('/^[a-zA-Z\s]+$/', $result))  //'/^[a-zA-Z.,\s]+$/' for comma and fullstop too
    {
        $index=$key."Error";
        $errorlist[$index]="true";
    }       
    return $result;
}
function only_letters_numbers($input,$key,&$errorlist)
{
    
    $result =preg_replace('/^\s+|\s+$|\s+(?=\s)/', '', $input);
    if(!preg_match ('/^[\s0-9A-Za-z]+$/', $result))
    {
        $index=$key."Error";
        $errorlist[$index]="true";
    }       
    return $result;
}
function validate_email($input,$key,&$errorlist)
{
    $result=filter_var($input,FILTER_SANITIZE_EMAIL);
    $result = stripcslashes($result);
    if (!filter_var($result,FILTER_VALIDATE_EMAIL))
    {
        $index=$key."Error";
        $errorlist[$index]="true";
    }
    return $result;
}
function sanitize_email($data)
{
    $result=filter_var($data['email'],FILTER_SANITIZE_EMAIL);
    $result = stripcslashes($result);
    if (filter_var($result,FILTER_VALIDATE_EMAIL))
    {
       return $result;
    }
    else
    {
        return null;
    }
}
function date_difference($present, $future)
{
    $present=date_create($present);
    $future=date_create($future);
    $diff=date_diff($present,$future);
    $days=$diff->format("%R%a");
    return $days;
}
function time_difference($present,$future)
{
    $to_time = strtotime($present);
    $from_time = strtotime($future);
    $diff=(($to_time - $from_time) / 60);
    return $diff;
}


function filter_post($input,$cap = 0){
    $result = strip_tags($input);
    $result = stripcslashes($input);
    if($cap == 1){
        $result = ucwords($result);
    }
    return $result;
}
function filter_post_breaks($input,$line_breaks = 0){
    $result = strip_tags($input);
    $result = stripcslashes($input);
    if($line_breaks === 1){
        $result =  str_replace("\r\n","<br>",$result);
    }
    return $result;
}