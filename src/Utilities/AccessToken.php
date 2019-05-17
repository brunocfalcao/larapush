<?php

namespace Brunocfalcao\Larapush\Utilities;

/**
 * Class that stores an OAuth access token.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
class AccessToken
{
    public $expiresIn = null;
    public $token = null;

    public function __construct(int $expiresIn, string $token)
    {
        [$this->expiresIn, $this->token] = [$expiresIn, $token];
    }
}
