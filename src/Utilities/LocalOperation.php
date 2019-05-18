<?php

namespace Brunocfalcao\Larapush\Utilities;

use Illuminate\Support\Facades\Storage;
use Brunocfalcao\Larapush\Exceptions\LocalException;
use Brunocfalcao\Larapush\Exceptions\AccessTokenException;

/**
 * Class that executes all operations contexted in your local web
 * environment.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
final class LocalOperation
{
    private $accessToken;

    protected $zipFilename;

    public function askRemoteToCheckEnvironment()
    {
        $response = ReSTCaller::asPost()
                          ->withHeader('Authorization', 'Bearer '.$this->accessToken->token())
                          ->withHeader('Accept', 'application/json')
                          ->withPayload(['larapush-token' => app('config')->get('larapush.token')])
                          ->withPayload(['environments' => implode(',', app('config')->get('larapush.environment.reserved'))])
                          ->call(larapush_remote_url('check-environment'));

        $this->checkResponseStatus($response);

        return (bool) $response->payload()['prompt'];
    }

    public function runPostScripts(string $transaction) : void
    {
        $response = ReSTCaller::asPost()
                      ->withHeader('Authorization', 'Bearer '.$this->accessToken->token())
                      ->withHeader('Accept', 'application/json')
                      ->withPayload(['larapush-token' => app('config')->get('larapush.token')])
                      ->withPayload(['transaction' => $transaction])
                      ->call(larapush_remote_url('post-scripts'));

        $this->checkResponseStatus($response);
    }

    public function deploy(string $transaction) : void
    {
        $response = ReSTCaller::asPost()
                      ->withHeader('Authorization', 'Bearer '.$this->accessToken->token())
                      ->withHeader('Accept', 'application/json')
                      ->withPayload(['larapush-token' => app('config')->get('larapush.token')])
                      ->withPayload(['transaction' => $transaction])
                      ->call(larapush_remote_url('deploy'));

        $this->checkResponseStatus($response);
    }

    public function runPreScripts(string $transaction) : void
    {
        $response = ReSTCaller::asPost()
                      ->withHeader('Authorization', 'Bearer '.$this->accessToken->token())
                      ->withHeader('Accept', 'application/json')
                      ->withPayload(['larapush-token' => app('config')->get('larapush.token')])
                      ->withPayload(['transaction' => $transaction])
                      ->call(larapush_remote_url('pre-scripts'));

        $this->checkResponseStatus($response);
    }

    public function uploadCodebase(string $transaction) : void
    {
        $response = ReSTCaller::asPost()
                      ->withHeader('Authorization', 'Bearer '.$this->accessToken->token())
                      ->withHeader('Accept', 'application/json')
                      ->withPayload(['larapush-token' => app('config')->get('larapush.token')])
                      ->withPayload(['transaction' => $transaction])
                      ->withPayload(['runbook' => json_encode(app('config')->get('larapush.scripts'))])
                      ->withPayload(['codebase' => base64_encode(file_get_contents(larapush_storage_path("{$transaction}/codebase.zip")))])
                      ->call(larapush_remote_url('upload'));

        $this->checkResponseStatus($response);
    }

    public function preChecks() : void
    {
        $storagePath = app('config')->get('larapush.storage.path');
        if (! is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        if (! is_writable($storagePath)) {
            throw new LocalException('Local storage directory not writeable');
        }
    }

    public function getAccessToken()
    {
        $response = ReSTCaller::asPost()
                   ->withPayload([
                       'grant_type' => 'client_credentials',
                       'client_id' => app('config')->get('larapush.oauth.client'),
                       'client_secret' => app('config')->get('larapush.oauth.secret'),
                   ])
                   ->withHeader('Accept', 'application/json')
                   ->call(app('config')->get('larapush.remote.url').'/oauth/token');

        $this->checkAccessToken($response);

        $this->accessToken = new AccessToken(
            $response->payload()['expires_in'],
            $response->payload()['access_token']
        );

        return $this;
    }

    public function askRemoteForPreChecks() : void
    {
        $response = ReSTCaller::asPost()
                  ->withHeader('Authorization', 'Bearer '.$this->accessToken->token())
                  ->withHeader('Accept', 'application/json')
                  ->withPayload(['larapush-token' => app('config')->get('larapush.token')])
                  ->call(larapush_remote_url('prechecks'));

        $this->checkResponseStatus($response);
    }

    public function ping() : void
    {
        $response = ReSTCaller::asPost()
                  ->withHeader('Authorization', 'Bearer '.$this->accessToken->token())
                  ->withHeader('Accept', 'application/json')
                  ->withPayload(['larapush-token' => app('config')->get('larapush.token')])
                  ->call(larapush_remote_url('ping'));

        $this->checkResponseStatus($response);
    }

    private function checkResponseStatus(ResponsePayload $response) : void
    {
        if (! $response->isOk() || data_get($response->payload(), 'error') != null) {
            throw new LocalException(get_response_payload_friendly_message($response));
        }
    }

    private function checkAccessToken(?ResponsePayload $response) : void
    {
        if (! $response->isOk() || data_get($response->payload(), 'access_token') == null) {
            throw new AccessTokenException(get_response_payload_friendly_message($response));
        }
    }

    public static function new(...$args)
    {
        return new self(...$args);
    }
}
