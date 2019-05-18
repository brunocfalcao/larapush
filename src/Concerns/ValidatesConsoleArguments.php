<?php

namespace Brunocfalcao\Larapush\Concerns;

use Illuminate\Support\Facades\Validator;

/*
 * Trait used to allow a simpler rules validations in the commands.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
trait ValidatesConsoleArguments
{
    protected function validateOptions()
    {
        $validator = Validator::make($this->options(), [
            'client' => 'required|integer',
            'secret' => 'required',
            'token'  => 'required',
        ], $this->messages);

        if ($validator->fails()) {
            return capsule(false, $this->error($validator->errors()->first()));
        }

        return capsule(true);
    }

    protected function askAndValidate($question, $rules, $messages = null)
    {
        $passed = false;
        while (! $passed) {
            $answer = $this->ask($question);

            $validator = Validator::make(['answer'=> $answer], ['answer' => $rules], $messages ?? $this->messages);

            if ($validator->fails()) {
                $this->error($validator->errors()->first());
            } else {
                $passed = true;
            }
        }

        return $answer;
    }
}
