<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace CodeChallenge\Services;

/**
 * Description of TimeTableComputer
 *
 * @author astridsynnoveschonemann
 */
class TimeTableComputer {
    
    /**
     * If resolution_in_minutes = 1, then we need a 1440-bit (90 hex) value to do the computation
     * If resulution_in_minutes = 5: 288 / 18 hex
     * If resulution_in_minutes = 10: 144 / 9 hex
     * If resulution_in_minutes = 15: 96 / 6 hex
     * ETC.
     */
    const MINUTES_PER_DAY = 1440;
    
    /**
     * @param int $resolution_in_minutes
     * @return int
     */
    public static function CalculateNumTimeslotsPerDayForResolution(int $resolution_in_minutes):int{
        if($resolution_in_minutes > 1){
            return (int)ceil(\CodeChallenge\Services\TimeTableComputer::MINUTES_PER_DAY/$resolution_in_minutes);
        }
        else{
            return \CodeChallenge\Services\TimeTableComputer::MINUTES_PER_DAY;
        }
    }
    
    /**
     * 01:25 will return 85
     * 05:33 will return 333
     * 
     * @param \DateTime $time
     * @return int
     */
    public static function CalculateMinuteOfTheDay(\DateTime $time):int{
        $num_hours = (int) $time->format('G');
        $num_minutes = (int) $time->format('i');
        return ($num_hours*60+$num_minutes);
    }
    
    /**
     * @param \CodeChallenge\Models\Schedule $schedule
     */
    public function MapDayScheduleToBinRepresentation(\CodeChallenge\Models\Schedule $schedule, int $resolution_in_minutes){
        if($resolution_in_minutes > 60){
            throw new \InvalidArgumentException("Resolution can max be 60 minutes(one hour)");
        }
    }
    
    /**
     * @param \DateTime $begin_time
     * @param \DateTime $end_time
     * @param int $resolution_in_minutes
     * @return string
     */
    public static function MapTimeIntervalToBinRepresentation(\DateTime $begin_time, \DateTime $end_time, int $resolution_in_minutes):string{
        if($resolution_in_minutes > 60){
            throw new \InvalidArgumentException("Resolution can max be 60 minutes(one hour)");
        }
        if(($end_time->getTimestamp() - $begin_time->getTimestamp()) < $resolution_in_minutes){//needs work!
            throw new \InvalidArgumentException("Difference(".($end_time->getTimestamp() - $begin_time->getTimestamp()).") between begin and end is smaller than the resolutiuon");
        }
       
        $begin_min_of_day = \CodeChallenge\Services\TimeTableComputer::CalculateMinuteOfTheDay($begin_time);
        if($resolution_in_minutes > 1){
            $begin_resultion_index = (int)floor($begin_min_of_day/$resolution_in_minutes);//do thorough testing to see wether it really is ceil we should use
        }
        else{
            $begin_resultion_index = $begin_min_of_day;
        }
        
        //regarding minus 1: we assume that the last minute should not be included, but is the max end of the period 
        $end_min_of_day = \CodeChallenge\Services\TimeTableComputer::CalculateMinuteOfTheDay($end_time)-1;
        if($resolution_in_minutes > 1){
            $end_resultion_index = (int)floor($end_min_of_day/$resolution_in_minutes);//do thorough testing to see wether it really is ceil we should use
        }
        else{
            $end_resultion_index = $end_min_of_day;
        }
        
        $timeslots = \CodeChallenge\Services\TimeTableComputer::CalculateNumTimeslotsPerDayForResolution($resolution_in_minutes);
        $timeslots_bin_str = "";
        //build a string prepresentation of the binary pattern
        for($i = 0; $i<$timeslots; $i++){
            if($i >= $begin_resultion_index && $i <= $end_resultion_index){
                $timeslots_bin_str .= "1";
            }
            else{
                $timeslots_bin_str .= "0";
            }
        }
        return $timeslots_bin_str;
        //return gmp_init(\bin2hex($timeslots_bin_str));
    }
}