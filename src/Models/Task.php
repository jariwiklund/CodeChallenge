<?php
namespace CodeChallenge\Models;

interface Task {
    
    const STATUS_TO_BE_EXECUTED = 'to_be_executed';
    const STATUS_FOR_RETRY = 'retry';
    const STATUS_EXECUTING = 'executing';
    const STATUS_SUCCEEDED = 'succeeded';
    const STATUS_FAILED = 'failed';
    
    /**
     * Does the deed, send email, sms, tigger another booking, whatever
     */
    public function execute();
    
    /**
     * @return string 'executing','to_be_executed','failed','succeeded'
     */
    public function getStatus();
    
    /**
     * example: description of why the task failed
     * 
     * @return string
     */
    public function getStatusMessage();
    
    /**
     * @return \DateTime
     */
    public function getExecutionTime();
    
}