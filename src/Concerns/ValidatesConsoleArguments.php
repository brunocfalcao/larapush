<?php

namespace Brunocfalcao\Larapush\Concerns;

use Illuminate\Support\Facades\Validator;

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
