<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace CodeChallenge\Models;

/**
 * Description of Calendar
 *
 * @author astridsynnoveschonemann
 */
class Calendar {
    
    /**
     *
     * @var \CodeChallenge\Models\DaySchedule[]
     */
    private $schedule;
    
    /**
     * 
     * @param \CodeChallenge\Models\DaySchedule[] $schedule
     */
    public function __construct($schedule) {
        $this->schedule = $schedule;
    }
    
    /**
     * 
     * @param \DateTime $day
     * @return \CodeChallenge\Models\DaySchedule
     */
    public function getDayScheduleForDay(\DateTime $day){
        foreach ($this->schedule as $daySchedule){
            if($daySchedule->getDay() == $day){
                return $daySchedule;
            }
        }
    }
    
    /**
     * 
     * @return \CodeChallenge\Models\DaySchedule[]
     */
    public function getSchedule(){
        return $this->schedule;
    }
    
}