<?php

$motDePasse = "Admin@228";

$hash = password_hash($motDePasse, PASSWORD_BCRYPT, [
    'cost' => 12
]);

echo $hash;