<?php
namespace CodeChallenge\Models;

class SendSmsTask implements \CodeChallenge\Models\Task{
    
    private $reciver_phone_number;
    
    private $message;
    
    private $status;
    
    private $execution_time;
    
    /**
     * @var \CodeChallenge\Services\SMSGateway
     */
    private $sms_gateway;

    /**
     * @param \CodeChallenge\Services\SMSGateway $gateway
     * @param string $reciever_phone_number
     * @param string $message
     */
    public function __construct(\CodeChallenge\Services\SMSGateway $gateway, string $reciever_phone_number, string $message ){
        $this->reciver_phone_number = $reciever_phone_number;
        $this->message = $message;
    }
    
    public function setStatusToInPipeLine(){
        $this->status = \CodeChallenge\Models\Task::STATUS_IN_PIPELINE;
        //todo: persist
    }
    
    public function execute() {
        $this->status = \CodeChallenge\Models\Task::STATUS_EXECUTING;
        //todo: Persist, so that the taskrunner doesn't 
        try{
            $this->sms_gateway->SendSms($this->reciver_phone_number, $this->message);
            $this->status = \CodeChallenge\Models\Task::STATUS_SUCCEEDED;
        }
        catch( ServiceTemporarilyUnavailable $tmp_unavail){
            $this->status = \CodeChallenge\Models\Task::STATUS_FOR_RETRY;
        }
        catch( RecieverPhoneNumberNonExisting $bogus_phone_number){
            $this->status = \CodeChallenge\Models\Task::STATUS_FAILED;
        }
        //todo: Persist
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