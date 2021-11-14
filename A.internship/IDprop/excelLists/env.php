<?php 
$comp = '/Dev7254Pg!/USA4ByH3X9HjG495/IDprop';
$path = sprintf('https://letfaster.tech%s', $comp);
# $path = 'http://localhost/tenant_portal';
$GLOBALS['actual_link'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";