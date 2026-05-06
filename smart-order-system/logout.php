<?php
// logout.php
session_start();
require_once 'classes/Auth.php';
(new Auth())->logout();
