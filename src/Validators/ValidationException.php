<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace CodeChallenge\Validators;

/**
 * Description of ValidationException
 *
 * @author astridsynnoveschonemann
 */
class ValidationException extends \Exception{
    
    private $parameter_name;
    
    private $invalid_value;
    
    /**
     * @param string $parameter_name
     * @param string $invalid_value
     * @param string $message
     * @param int $code
     * @param \Throwable $previous
     */
    public function __construct(string $parameter_name, string $invalid_value, string $message = "", int $code = 0, \Throwable $previous = null) {
        $this->parameter_name = $parameter_name;
        $this->invalid_value = $invalid_value;
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * @return string
     */
    public function getParameterName():string{
        return $this->parameter_name;
    }
    
    /**
     * @return string
     */
    public function getInvalidValue():string{
        return $this->invalid_value;
    }
}