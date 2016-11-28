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
     * PRE: the schedule only contains appointments for the same day
     * @param \CodeChallenge\Models\Schedule $schedule
     * @return string 
     */
    public static function MapDayScheduleToBinRepresentation(\CodeChallenge\Models\Schedule $schedule, int $resolution_in_minutes):string{
        $prev_gmp = gmp_init('0x0');
        foreach ($schedule->getAppointments() as $appointment){
            $hex = '0x'.dechex(bindec(
                \CodeChallenge\Services\TimeTableComputer::MapTimeIntervalToBinRepresentation(
                    $appointment->getBegins(),
                    $appointment->getEnds(), 
                    $resolution_in_minutes
                )
            ));
            $current_gmp = gmp_init($hex);
            $prev_gmp = gmp_or($prev_gmp, $current_gmp);
        }
        return gmp_strval($prev_gmp, 2);
    }
    
    /**
     * @param \CodeChallenge\Models\Schedule[] $daily_schedules
     * @param int $resolution_in_minutes
     * @return string
     */
    public static function MapSchedulesToAvailabilityInBinaryString(array $daily_schedules, int $resolution_in_minutes):string{
        $prev_gmp = gmp_init('0x0');
        foreach ($daily_schedules as $schedule){
            foreach ($schedule->getAppointments() as $appointment){
                $hex = '0x'.dechex(bindec(
                    \CodeChallenge\Services\TimeTableComputer::MapTimeIntervalToBinRepresentation(
                        $appointment->getBegins(),
                        $appointment->getEnds(), 
                        $resolution_in_minutes
                    )
                ));
                $current_gmp = gmp_init($hex);
                $prev_gmp = gmp_or($prev_gmp, $current_gmp);
            }
        }
        return gmp_strval($prev_gmp, 2);
    }
    
    /**
     * The $bin_string is witout any notion of date, its just a representation of timeslots relative to an unnamed date
     * 
     * @param string $bin_string
     * @param \DateTime $day_to_map this to set the date
     * @return \CodeChallenge\Models\Schedule
     */
    public static function MapBinStringToFreeSchedule(string $bin_string, \DateTime $day_to_map) : \CodeChallenge\Models\Schedule{
        $num_slots = \strlen($bin_string);
        $day_to_map->setTime(0, 0, 0);
        if($num_slots > 1440){
            throw new \InvalidArgumentException('The bin_string is too long('.$num_slots.') it can ax be 1440 chars(one minute resolution)');
        }
        if( 1440 % $num_slots != 0){
            throw new \InvalidArgumentException('The bin_string is invalid, its length must be a divisor of 1440(one minute resolution)');
        }
        $appointments = array();
        $num_minutes_per_slot = 1440/$num_slots;
        for($i=0; $i<$num_slots;$i++){
            $cur_slot = $bin_string[$i];
            if($cur_slot == '0'){
                if(!isset($begin_time)){
                    $begin_time = clone $day_to_map;
                    $begin_time->add(new \DateInterval('PT'.($i*$num_minutes_per_slot).'M'));
                }
            }
            else{
                if(isset($begin_time)){
                    $end_time = clone $day_to_map;
                    $end_time->add(new \DateInterval('PT'.($i*$num_minutes_per_slot).'M'));
                    $appointments[] = new \CodeChallenge\Models\Appointment("Free period(".($i*$num_minutes_per_slot).")", $begin_time, $end_time);
                    unset($begin_time);
                }
            }
        }
        if(isset($begin_time)){
            $end_time = $day_to_map->add(new \DateInterval('PT'.($num_slots*$num_minutes_per_slot).'M'));
            $appointments[] = new \CodeChallenge\Models\Appointment("Free period(".($num_slots*$num_minutes_per_slot).")", $end_time, $begin_time);
        }
        return new \CodeChallenge\Models\Schedule($appointments, new \CodeChallenge\Models\Person('NullPerson'));
    }
    
    /**
     * @param \DateTime $begin_time
     * @param \DateTime $end_time
     * @param int $resolution_in_minutes
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function MapTimeIntervalToBinRepresentation(\DateTime $begin_time, \DateTime $end_time, int $resolution_in_minutes):string{
        if($resolution_in_minutes > 60){
            throw new \InvalidArgumentException("Resolution can max be 60 minutes(one hour)");
        }
        if( 1440 % $resolution_in_minutes != 0 ){
            throw new \InvalidArgumentException("Resolution must be a divisor of 1440(num minutes in 24 hours)");
        }
        if(($end_time->getTimestamp() - $begin_time->getTimestamp()) < $resolution_in_minutes){//needs work!
            throw new \InvalidArgumentException("Difference(".($end_time->getTimestamp() - $begin_time->getTimestamp()).") between begin and end is smaller than the resolutiuon");
        }
        if($begin_time->format('z') != $end_time->format('z')){
            throw new \InvalidArgumentException('Begin and end time, must be the same day');
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
    }
}