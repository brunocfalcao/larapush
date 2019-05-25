<?php

namespace Brunocfalcao\Larapush\Structures;

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
final class AccessToken
{
    protected $expiresIn = null;
    protected $token = null;

    public function __construct(int $expiresIn, string $token)
    {
        [$this->expiresIn, $this->token] = [$expiresIn, $token];
    }

    public function expiresIn(): int
    {
        return $this->expiresIn;
    }

    public function token(): string
    {
        return $this->token;
    }
}
