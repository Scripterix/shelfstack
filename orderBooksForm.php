<?php
declare(strict_types=1);

require_once __DIR__ . '/shared/config.php';

header('Location: ' . app_url('shop.php'), true, 302);
exit;
