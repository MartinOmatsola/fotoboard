<?php
include_once 'User.php';
include_once 'utils.php';

$password = crypt('jamielynn', '$1$' . randomString(8) . '$');
print $password;
//$user = new User("Martin", "Okorodudu", "martin.omatsola@gmail.com", $password);
//$user->flush();

?>
