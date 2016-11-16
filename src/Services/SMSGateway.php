<?php
namespace CodeChallenge\Services;

interface SMSGateway {
    
    /**
     * 
     * @param string $reciever_phone_number
     * @param string $message
     * @throws RecieverPhoneNumberNonExisting
     * @throws ServiceTemporarilyUnavailable
     */
    public function SendSms(string $reciever_phone_number, string $message);
    
}