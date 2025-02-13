<?php
require_once 'config/config.php';
init_session();
session_destroy();
header("Location: login.php");
exit();
?> 