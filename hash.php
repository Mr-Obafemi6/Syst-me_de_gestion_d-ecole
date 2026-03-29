<?php
$password = 'Admin1234!';
echo password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);