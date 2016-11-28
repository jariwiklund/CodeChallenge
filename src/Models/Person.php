<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace CodeChallenge\Models;

/**
 * Description of Person
 *
 * @author astridsynnoveschonemann
 */
class Person {
    
    private $name;
    
    public function __construct($name) {
        $this->name = $name;
    }
    
    public function getName(){
        return $this->name;
    }
}