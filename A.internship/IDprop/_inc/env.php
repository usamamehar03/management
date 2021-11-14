<?php 
$comp = '/A.internship/IDprop';
$path = sprintf('http://localhost%s', $comp);
# $path = 'http://localhost/tenant_portal';
$GLOBALS['actual_link'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";