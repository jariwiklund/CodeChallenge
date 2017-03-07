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
 * @author Jari Wiklund
 */
class DaySchedule {
    
    /**
     * @var \CodeChallenge\Models\Appointment[]
     */
    private $appointments;
    
    function __construct(array $appointments) {
        $this->appointments = $appointments;
    }
    
    /**
     * @return \DateTime
     */
    public function getDay(){
        return $this->appointments[0]->getBegins();
    }
    
    /**
     * @return \CodeChallenge\Models\Appointment[]
     */
    function getAppointments(): array {
        return $this->appointments;
    }
}