<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace CodeChallenge\Validators;

/**
 * Description of RequiredPropertyMissing
 *
 * @author astridsynnoveschonemann
 */
class RequiredPropertyMissing extends \Exception{
    
    /**
     * @var string
     */
    private $property_name;
    
    public function __construct(string $property_name, string $message = "", int $code = 0, \Throwable $previous = null) {
        $this->property_name = $property_name;
        parent::__construct($message, $code, $previous);
    }
    
    public function getPropertyName():string{
        return $this->property_name;
    }
    
}