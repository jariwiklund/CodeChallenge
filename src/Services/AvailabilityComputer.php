<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace CodeChallenge\Services;

/**
 * Description of AvailabilityComputer
 *
 * @author astridsynnoveschonemann
 */
class AvailabilityComputer {
    
    
    /**
     * 
     * @param \CodeChallenge\Services\Calendar $calendarToFindTimeslotsIn
     * @param \DatePeriod $searchSpace
     * @param \DateInterval $timeslotLength
     * @param \CodeChallenge\Models\AvailabilityFilter[] $filters
     */
    public function __construct(Calendar $calendarToFindTimeslotsIn, \DatePeriod $searchSpace, \DateInterval $timeslotLength, $filters) {
        ;
    }
    
    public function findTimeslots(){
        
    }
    
}