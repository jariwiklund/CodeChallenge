<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace CodeChallenge\Validators;

/**
 * Description of ValidationExceptions
 *
 * @author astridsynnoveschonemann
 */
class ValidationExceptions extends \Exception{
    
    /**
     *
     * @var \CodeChallenge\Validators\ValidationException[]
     */
    private $validation_exceptions;
    
    /**
     * @param \CodeChallenge\Validators\ValidationException[] $validation_exceptions
     * @param string $message
     * @param int $code
     * @param \Throwable $previous
     */
    public function __construct(array $validation_exceptions, string $message = "", int $code = 0, \Throwable $previous = null) {
        $this->validation_exceptions = $validation_exceptions;
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * @param \CodeChallenge\Validators\ValidationException $validation_exception
     */
    public function addValidationException(\CodeChallenge\Validators\ValidationException $validation_exception){
        $this->validation_exceptions[] = $validation_exception;
    }
    
    /**
     * @return \CodeChallenge\Validators\ValidationException[]
     */
    public function getValidationExceptions(){
        return $this->validation_exceptions;
    }
}