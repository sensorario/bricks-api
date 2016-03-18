<?php

namespace Bricks;

use JsonSerializable;

final class Persist
{
    public static function jsonSerializable(
        JsonSerializable $json
    ) {
        $fileName = 'app/data/' . strtolower(
            str_replace('\\', '.', get_class($json))
        );

        error_log($fileName);

        $content = serialize($json);
        $newLine = file_exists($fileName) ? "\n" : "";
        $handle = fopen($fileName, 'a+');
        $newRecord = $newLine . $content;
        fwrite($handle, $newRecord);
        fclose($handle);
    }
}

