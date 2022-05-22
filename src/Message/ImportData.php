<?php

namespace App\Message;

class ImportData
{

    private $import;

    public function __construct($import)
    {
        $this->import = $import;
    }

    public function getImport()
    {
        return $this->import;
    }

    public function __toString()
    {
        return $this->getImport();
    }
}