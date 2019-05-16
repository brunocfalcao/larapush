<?php

namespace Brunocfalcao\Larapush\Utilities;

use Illuminate\Support\Str;
use PhpZip\Model\ZipInfo;

class ZipResource
{
    protected $modifiedDate = null;
    protected $relativePath = null;

    public function __construct(ZipInfo $zipInfo)
    {
        $this->relativePath = $zipInfo->getName();
        $this->modifiedDate = timestamp_to_carbon($zipInfo->getMtime());
    }

    public function realPath()
    {
        return Str::endsWith($this->realPath, '/') ? substr($this->realPath, 0, -1) : $this->realPath;
    }

    public function relativePath()
    {
        return Str::endsWith($this->relativePath, '/') ? substr($this->relativePath, 0, -1) : $this->relativePath;
    }

    public function modifiedDate()
    {
        return $this->modifiedDate;
    }
}
