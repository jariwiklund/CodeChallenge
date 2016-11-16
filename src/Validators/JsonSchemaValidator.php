<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace CodeChallenge\Validators;

/**
 * Use http://json-schema.org/
 */
class JsonSchemaValidator implements \CodeChallenge\Validators\Validator{
    
    private $json_schema;
    
    /**
     * @example:
{
	"title": "Example Schema",
	"type": "object",
	"properties": {
		"firstName": {
			"type": "string"
		},
		"lastName": {
			"type": "string"
		},
		"age": {
			"description": "Age in years",
			"type": "integer",
			"minimum": 0
		}
	},
	"required": ["firstName", "lastName"]
}
     * 
     * 
     * @param array $json_schema
     */
    public function __construct($json_schema) {
        $this->json_schema = $json_schema;
    }

    /**
     * @param string $data
     * @throws \CodeChallenge\Validators\UnparsableJsonString
     * @throws \CodeChallenge\Validators\ValidationExceptions
     */
    public function validateData($data): void {
        
        $json_array = \json_decode($data, true);
        if(is_null($json_array)){
            throw new \CodeChallenge\Validators\UnparsableJsonString("Could not decode the json data");
        }
        
        $validation_exceptions = array();
        
        foreach ($this->json_schema['properties'] as $key => $value){
            if(array_key_exists($key, $json_array)){
                try{
                    $this->validateParameter($key, $json_array[$key], $json_schema['properties'][$key]);
                }
                catch(\CodeChallenge\Validators\ValidationException $ve){
                    $validation_exceptions[] = $ve;
                }
            }
            else if(\in_array($key, $this->json_schema['required'])){
                $validation_exceptions[] = new \CodeChallenge\Validators\RequiredPropertyMissing($key);
            }
        }
        if(count($validation_exceptions) > 0){
            throw new \CodeChallenge\Validators\ValidationExceptions($validation_exceptions);
        }
    }
    
    /**
     * @param mixed $parameter
     * @param array $json_schema_constraints todo: objectify this
     * @throws \CodeChallenge\Validators\ValidationException
     */
    private function validateParameter($name, $parameter, $json_schema_constraints){
        switch ($json_schema_constraints['type']){
            case 'string':
                if(\array_key_exists('pattern', $json_schema_constraints)){
                    $this->validateRegexString(
                        $name, 
                        $string, 
                        $json_schema_constraints['pattern']
                    );
                }
                else{
                    $this->validateString(
                        $name, 
                        $string, 
                        $json_schema_constraints['min_length'], 
                        $json_schema_constraints['max_length']
                    );
                }
                break;
            case 'enum':
            case 'integer':
            case 'object':
            default:
                throw new NotImplementedException("Validation of json type ".$json_schema_constraints['type']." is not yet implemented");
                break;
        }
    }
    
    /**
     * @param string $string
     * @param int $min_length
     * @param int $max_length
     * @throws \CodeChallenge\Validators\ValidationException
     */
    private function validateString($name, $string, $min_length, $max_length){
        if( \mb_strlen($string) < $min_length ){
            throw new \CodeChallenge\Validators\ValidationException($name, $string, "The string was too short. Must be at least ".$min_length." long");
        }
        if(\mb_strlen($string) > $max_length){
            throw new \CodeChallenge\Validators\ValidationException($name, $string, "The string was too long. Must be max ".$mac_length." long");
        }
    }
    
    /**
     * @param string $name
     * @param string $string
     * @param string $pattern
     * @throws \CodeChallenge\Validators\ValidationException
     */
    private function validateRegexString($name, $string, $pattern){
        if(!\preg_match($pattern, $string)){
            throw new \CodeChallenge\Validators\ValidationException($name, $string, "The string did not match the pattern ".$pattern);
        }
    }
}