<?php
namespace CodeChallenge\Models;

class SendEmailTask implements \CodeChallenge\Models\Task{
    
    private $reciver_email_address;
    
    private $subject;
    
    private $message;
    
    private $status;
    
    private $execution_time;
    
    /**
     * @var \CodeChallenge\Services\EmailClient
     */
    private $email_client;

    /**
     * @param \CodeChallenge\Services\EmailClient $email_client
     * @param string $reciever_address
     * @param string $subject
     * @param string $message
     */
    public function __construct(\CodeChallenge\Services\EmailClient $email_client, string $reciever_email_address, string $subject, string $message ){
        $this->email_client = $email_client;
        $this->reciver_email_address = $reciever_email_address;
        $this->subject = $subject;
        $this->message = $message;
    }
    
    public function execute() {
        $this->status = \CodeChallenge\Models\Task::STATUS_EXECUTING;
        //Persist
        try{
            $this->email_client->sendEmail(
                $this->reciver_email_address, 
                $this->subject, 
                $this->message
            );
            $this->status = \CodeChallenge\Models\Task::STATUS_SUCCEEDED;
        }
        //todo: perhaps inspect the exception, but email is "fire and forget" most of the time
        //so an exception is probably something quite significant
        catch( \Exception $e){
            $this->status = \CodeChallenge\Models\Task::STATUS_FAILED;
        }
        //Persist
    }

    /**
     * @return \DateTime
     */
    public function getExecutionTime(): \DateTime {
        return $this->execution_time;
    }

    /**
     * @return string
     */
    public function getStatus(): string {
        return $this->status;
    }
    
    public function getStatusMessage(): string {
        throw new NotImplementedException();
    }
}