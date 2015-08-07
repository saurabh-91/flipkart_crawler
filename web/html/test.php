
<?php
include('Logger.php');
$logger = Logger::getLogger("main");
$logger->info("This is an informational message.");
$logger->warn("I'm not feeling so good...");
?>