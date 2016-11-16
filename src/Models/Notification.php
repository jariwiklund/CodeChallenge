<?php
namespace CodeChallenge\Models;

class Notification {
    
    const PRIORITY_CRITICAL = 'critical';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_RELAXED = 'relaxed';
    
    private $id;
    
    private $reciever;
    
    private $send_time;
    
    private $confirmed_recieved;
    
    /**
     * @example "Hi you have been booked for X at Y time"
     * 
     * @var type 
     */
    private $contents;
    
    private $priority; 
    
    function __construct(string $id, $reciever, $send_time, $confirmed_recieved, $contents, $priority) {
        $this->id = $id;
        $this->reciever = $reciever;
        $this->send_time = $send_time;
        $this->confirmed_recieved = $confirmed_recieved;
        $this->contents = $contents;
        $this->priority = $priority;
    }

    function getId() {
        return $this->id;
    }

    function getReciever() {
        return $this->reciever;
    }

    function getSendTime() {
        return $this->send_time;
    }

    function getConfirmedRecieved() {
        return $this->confirmed_recieved;
    }

    function getContents() {
        return $this->contents;
    }

    function getPriority() {
        return $this->priority;
    }
}