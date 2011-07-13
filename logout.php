<?php
session_start();
$_SESSION['user']=NULL;
unset($_SESSION['user']);
header('Location: login.php');