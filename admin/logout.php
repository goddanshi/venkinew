<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/lib/admin_auth.php';

venki_admin_session_start();
venki_admin_logout();

header('Location: index.php', true, 302);
exit;
