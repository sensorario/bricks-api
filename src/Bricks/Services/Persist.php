<?php

namespace Bricks\Services;

use Bricks\Objects\ObjectInterface;
use JsonSerializable;
use Psr\Log\LoggerInterface;

final class Persist
{
    private $fileName;

    private $logger;

    public function __construct(
        ObjectInterface $object,
        NamesGenerator $namesGenerator,
        LoggerInterface $logger
    ) {
        $this->fileName = $namesGenerator
            ->generateName($this->object = $object);
        $this->logger = $logger;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function persist()
    {
        $content = serialize($this->object);
        $newLine = file_exists($this->fileName) ? "\n" : "";
        $handle = fopen($this->fileName, 'a+');
        $newRecord = $newLine . $content;
        fwrite($handle, $newRecord);
        fclose($handle);
    }
}

