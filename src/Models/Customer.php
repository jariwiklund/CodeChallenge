<?php
namespace CodeChallenge\Models;

class Customer {
    
    /**
     * UUID
     * @var string
     */
    private $id;
    
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var string
     */
    private $email_address;
    
    /**
     * @var string
     */
    private $message_phone_number;
    
    /**
     * @param string $id UUID
     * @param string $name
     * @param string $email_address
     * @param string $message_phone_number
     */
    public function __construct(string $id, string $name, string $email_address, string $message_phone_number) {
        $this->id = $id;
        $this->name = $name;
        $this->email_address = $email_address;
        $this->message_phone_number = $message_phone_number;
    }
    
    /**
     * @param string $name
     * @param string $email_address
     * @param string $message_phone_number
     * @return \CodeChallenge\Models\Customer
     */
    public static function CreateNew(string $name, string $email_address, string $message_phone_number): \CodeChallenge\Models\Customer{
        $id = Uuid::CreateAsString();
        return new \CodeChallenge\Models\Customer($id, $name, $email_address, $message_phone_number);
    }
    
    /**
     * @return string
     */
    function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    function getEmailAddress() {
        return $this->email_address;
    }

    /**
     * @return string
     */
    function getMessagePhoneNumber() {
        return $this->message_phone_number;
    }
}