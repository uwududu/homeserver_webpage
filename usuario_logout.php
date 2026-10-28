<?php
session_start();
session_destroy();
header('Location: usuario_login.php');
exit;