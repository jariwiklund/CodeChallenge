<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace CodeChallenge\Validators;

/**
 * Basically when json_decode returns false
 */
class UnparsableJsonString extends \Exception{
    
    /**
     * @param string $message
     * @param int $code
     * @param \Throwable $previous
     */
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}