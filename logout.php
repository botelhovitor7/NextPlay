<?php
require_once 'admin/config/sessao.php';
fazerLogout();
header('Location: index.php?saiu=1');
exit;
