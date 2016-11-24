<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace CodeChallenge\Models;

/**
 * Description of Schedule
 *
 * @author astridsynnoveschonemann
 */
class Schedule {
    
    /**
     * @var \CodeChallenge\Models\Appointment[]
     */
    private $appointments;
    
    /**
     * @var Person
     */
    private $owner;
    
    function __construct(array $appointments, Person $owner) {
        $this->appointments = $appointments;
        $this->owner = $owner;
    }
    
    /**
     * @return \CodeChallenge\Models\Appointment[]
     */
    function getAppointments(): array {
        return $this->appointments;
    }

    function getOwner(): Person {
        return $this->owner;
    }
    
}