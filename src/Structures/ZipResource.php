<?php

namespace Brunocfalcao\Larapush\Structures;

use PhpZip\Model\ZipInfo;
use Illuminate\Support\Str;

/**
 * Class that stores a Zip resource to be later on added to the zip file.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
class ZipResource
{
    protected $modifiedDate = null;

    protected $relativePath = null;

    protected $realPath;

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
