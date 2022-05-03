<?php
require_once(__DIR__.'/utils.php');

$line = readline("Command: ");
if (!checkInputLine($line)) {
    printf('Please enter line with : ["remotehost","rfc931","authuser","date","request","status","bytes"] \n');
}
// var_dump($to_array);