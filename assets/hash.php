<?php
echo password_hash('Admin@123', PASSWORD_DEFAULT), PHP_EOL;
//var_dump($hash, password_verify($plain, $hash));