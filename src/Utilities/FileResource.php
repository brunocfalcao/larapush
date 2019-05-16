<?php

namespace Brunocfalcao\Larapush\Utilities;

use Illuminate\Support\Str;

class FileResource
{
    protected $modifiedDate = null;
    protected $createdDate = null;
    protected $realPath = null;
    protected $relativePath = null;
    protected $type = null;

    public function __construct(string $realPath)
    {
        if (is_file($realPath) || is_dir($realPath)) {
            // Apply transformations.
            $this->realPath = unix_separator_path($realPath);
            $this->relativePath = unix_separator_path(substr($realPath, strlen(base_path()) + 1));
            $this->modifiedDate = timestamp_to_carbon(filemtime($this->realPath));
            $this->createdDate = timestamp_to_carbon(filectime($this->realPath));

            $this->type = is_file($realPath) ? 'file' : 'folder';

            return $this;
        }
    }

    public function type()
    {
        return $this->type;
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

    public function createdDate()
    {
        return $this->createdDate;
    }
}
