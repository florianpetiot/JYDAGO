<?php
$fichier = $_REQUEST["fichier"];

// suppression du fichier
unlink($fichier);

?>