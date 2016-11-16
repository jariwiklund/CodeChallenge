<?php

namespace CodeChallenge\Validators;

interface Validator {
    
    /**
     * @param string $data raw json_data
     * @return void
     * @throws \CodeChallenge\Validators\ValidationExceptions
     * @throws \CodeChallenge\Validators\UnparsableJsonString
     */
    public function validateData($data);
    
}