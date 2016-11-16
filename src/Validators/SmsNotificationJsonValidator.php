<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace CodeChallenge\Validators;

/**
 * Description of SmsNotificationJsonValidator
 *
 * @author astridsynnoveschonemann
 */
class SmsNotificationJsonValidator extends \CodeChallenge\Validators\JsonSchemaValidator{
    
    public function __construct(){
        parent::__construct(array(
            'properties' => array(
                'sms_phone_number' => array(
                    'type' => 'string',
                    'min_length' => 4,
                    'max_length' => 64
                ),
                'customer_name' => array(
                    'type' => 'string',
                    'min_length' => 3,
                    'max_length' => 255
                )
            ),
            'required' => array('email', 'customer_name')
        ));
    }
}