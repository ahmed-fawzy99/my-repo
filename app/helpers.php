<?php
function human_filesize($bytes, $dec = 0): string {
    $size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    if ($factor == 0) $dec = 0;
    return sprintf("%.{$dec}f %s", $bytes / (1024 ** $factor), $size[$factor]);
}
