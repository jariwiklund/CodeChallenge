<?php
namespace CodeChallenge\Services;

interface EmailClient {
    
    /**
     * @param string $reciever_address
     * @param string $subject
     * @param string $message
     * 
     * @throws \Exception
     */
    public function sendEmail(string $reciever_address, string $subject, string $message);
}