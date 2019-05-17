<?php

namespace Brunocfalcao\Larapush\Utilities;

use Illuminate\Support\Str;

/**
 * Class that stores a file resource structure.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
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
