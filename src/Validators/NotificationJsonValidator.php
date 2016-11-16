<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace CodeChallenge\Validators;

/**
 * Description of NotificationJsonValidator
 *
 * @author astridsynnoveschonemann
 */
class NotificationJsonValidator extends \CodeChallenge\Validators\JsonSchemaValidator{
    
    public function __construct(){
        parent::__construct(array(
            'properties' => array(
                'notification_type' => array(
                    'type' => 'string',
                    'pattern' => '/sms|email/'
                ),
                'notification_time_relative_to_booking_begin' => array(//minutes
                    'type' => 'int',
                    'minimum' => 0,
                    'maximum' => 60
                )
            ),
            'required' => array('email', 'customer_name')
        ));
    }
}