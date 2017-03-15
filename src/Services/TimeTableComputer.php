<?php
namespace CodeChallenge\Services;

/**
 * Resolution in minutes, means how many minutes each bit represents
 * 1 in the mask means the timeslot is occupied(not available)
 *
 * @author Jari Wiklund
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
    public static function DateTimeToMinuteTheDay(\DateTime $time):int{
        //todo: benchmark against format('U') / unix timestamp
        $num_hours = (int) $time->format('G');
        $num_minutes = (int) $time->format('i');
        return ($num_hours*60+$num_minutes);
    }
    
    
    /**
     * Complementary to DateTimeToMinuteTheDay
     * 85 will return 01:25
     * 333 will return 05:33
     * 
     * @param int $minute_of_day
     */
    public static function MinuteOfDayToDateTime(int $minute_of_day): \DateTime{
        $hour = floor($minute_of_day/60);
        $minute_of_day = $minute_of_day%60;
        $date_time = new \DateTime();
        $date_time->setTime($hour, $minute_of_day, 0);
        return $date_time;
    }
    
    /**
     * PRE: the schedule only contains appointments for the same day
     * @param \CodeChallenge\Models\DaySchedule $schedule
     * @return \GMP
     */
    public static function DayScheduleToGmp(\CodeChallenge\Models\DaySchedule $schedule, int $resolution_in_minutes){
        $gmp = gmp_init('0x0');
        foreach ($schedule->getAppointments() as $appointment){
            $current_gmp = static::TimeSlotToGmp(
                $appointment->getTimeSlot(), 
                $resolution_in_minutes
            );
            $gmp = gmp_or($gmp, $current_gmp);
        }
        return $gmp;
    }
    
    /**
     * Will only return the first found timeslot
     * For a GMP like this: 0000000011111111100000000
     * The TimeSlot will look like this: TimeSlot(08:00, 17:00);
     * 111100000000000000000000 => TimeSlot(00:00, 04:00);
     * 
     * @param \GMP $gmp
     * @param int $resolution_in_minutes
     */
    public static function GmpToTimeSlot(\GMP $gmp, int $resolution_in_minutes): \CodeChallenge\Models\TimeSlot{
        
        //first we look for the first on-bit => true
        $what_to_look_for = true;
        $bits_to_check = static::CalculateNumTimeslotsPerDayForResolution($resolution_in_minutes);
        $start = null;
        for($i=0; $i<=$bits_to_check; $i++){
            if( gmp_testbit($gmp, $i) === $what_to_look_for){
                if($start !== null){
                    $end = static::MinuteOfDayToDateTime(($i)*$resolution_in_minutes);
                    return new \CodeChallenge\Models\TimeSlot($start, $end);
                }
                else{
                    //then we look for when the timeslot ends: when the bit turns off => false
                    $what_to_look_for = false;
                    $start = static::MinuteOfDayToDateTime($i*$resolution_in_minutes);
                }
            }
        }
        
        if($start !== null){
            $end = new \DateTime();
            $end->setTime(24, 0, 0);
            return new \CodeChallenge\Models\TimeSlot($start, $end);
        }
    }


    /**
     * At res in minutes=60, this could be the day_mask:
     * 000000001111110000000000
     * Which should produce this array:
     * [
     *      Timeslot(0, 1),
     *      Timeslot(1, 2),
     *      Timeslot(2, 3),
     *      Timeslot(3, 4),
     *      Timeslot(4, 5),
     *      Timeslot(5, 6),
     *      Timeslot(6, 7),
     *      Timeslot(7, 8),
     *      Timeslot(14, 15),
     *      Timeslot(15, 16),
     *      Timeslot(16, 17),
     *      Timeslot(17, 18),
     *      Timeslot(18, 19),
     *      Timeslot(19, 20),
     *      Timeslot(20, 21),
     *      Timeslot(21, 22),
     *      Timeslot(22, 23),
     *      Timeslot(23, 24),
     * ]
     * 
     * @param \CodeChallenge\Models\DaySchedule $daily_schedule
     * @param int $timeslot_length_in_minutes
     * @param int $resolution_in_minutes
     * @return \CodeChallenge\Models\TimeSlot[]
     */
    public static function FindAvailTimeslotsInSchedule(\CodeChallenge\Models\DaySchedule $daily_schedule, int $timeslot_length_in_minutes, int $resolution_in_minutes){
        //todo: sanity-check length with resolution
        if($timeslot_length_in_minutes%$resolution_in_minutes !== 0){
            throw new \InvalidArgumentException("Timeslot length (".$timeslot_length_in_minutes.") must be divisible by resolution(".$resolution_in_minutes.")");
        }
        
        $bitshift_length = $timeslot_length_in_minutes/$resolution_in_minutes;
        
        $day_mask = static::DayScheduleToGmp($daily_schedule, $resolution_in_minutes);
        
        $timeslot_bin = static::MinutesToGmp($timeslot_length_in_minutes, $resolution_in_minutes);
        $timeslots = [];
        $timeslots_to_check = static::CalculateNumTimeslotsPerDayForResolution($resolution_in_minutes);
        for($i=0; $i<$timeslots_to_check; $i++){
            if(gmp_cmp(gmp_and($day_mask, $timeslot_bin),0) === 0){
                $timeslots[] = static::GmpToTimeSlot($timeslot_bin, $resolution_in_minutes);
                $i+=$bitshift_length;
                $timeslot_bin = static::gmp_shiftl($timeslot_bin, $bitshift_length);
            }
            else{
                $timeslot_bin = static::gmp_shiftl($timeslot_bin, 1);
            }
        }
        return $timeslots;
    }
    
    private static function gmp_shiftl($x,$n): \GMP { // shift left
        return(gmp_mul($x,gmp_pow(2,$n)));
    }

    private static function gmp_shiftr($x,$n): \GMP { // shift right
        return(gmp_div($x,gmp_pow(2,$n)));
    } 
    
    /**
     * A free schedule is a schedule where each appointment represents free time
     * 
     * @param \GMP $gmp
     * @param \DateTime $day_to_map this to set the date
     * @return \CodeChallenge\Models\Schedule
     */
    public static function GmpToFreeSchedule(\GMP $gmp, \DateTime $day_to_map) : \CodeChallenge\Models\Schedule{
        $num_slots = \strlen(gmp_strval($gmp, 2));
        $day_to_map->setTime(0, 0, 0);
        if($num_slots > self::MINUTES_PER_DAY){
            throw new \InvalidArgumentException('The bin_string is too long('.$num_slots.') it can ax be 1440 chars(one minute resolution)');
        }
        if( self::MINUTES_PER_DAY % $num_slots != 0){
            throw new \InvalidArgumentException('The bin_string is invalid, its length must be a divisor of 1440(one minute resolution)');
        }
        $appointments = array();
        $num_minutes_per_slot = self::MINUTES_PER_DAY/$num_slots;
        for($i=0; $i<$num_slots;$i++){
            if(gmp_testbit($gmp, $i)){
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
     * If minutes is 60 and resolution in minutes is 15, this will return 11110000....
     * If minutes is 120 and resolution in minutes is 15, this will return 111111110000....
     * If minutes is 30 and resolution in minutes is 15, this will return 110000000....
     * if minutes is 240 and resolution is 60, this will return 111100000000000000000000
     * if minutes is 480 and resolution is 60, this will return 111111110000000000000000
     */
    public static function MinutesToGmp(int $minutes, int $resolution_in_minutes): \GMP{
        if($minutes < $resolution_in_minutes){
            throw new \InvalidArgumentException('Minutes('.$minutes.'), must at least be the same as the resolution('.$resolution_in_minutes.')');
        }
        if($minutes % $resolution_in_minutes != 0){
            throw new \InvalidArgumentException('The minutes ('.$minutes.'), must be wholy dividable by the resolution in minutes('.$resolution_in_minutes.')');
        }
        
        $gmp = gmp_init('0x0');
        
        $bits_length = $minutes / $resolution_in_minutes;
        //todo: benchmark setbit against bitshifting
        for($i=0; $i<= $bits_length; $i++){
            gmp_setbit($gmp, $i);
        }
        
        $length = static::CalculateNumTimeslotsPerDayForResolution($resolution_in_minutes);
        for($i=$bits_length; $i<=$length;$i++){
            gmp_setbit($gmp, $i, false);
        }
        return $gmp;
    }
    
    /**
     * @param \DateTime $begin_time
     * @param \DateTime $end_time
     * @param int $resolution_in_minutes
     * @return \GMP
     * @throws \InvalidArgumentException
     */
    public static function TimeIntervalToGmp(\DateTime $begin_time, \DateTime $end_time, int $resolution_in_minutes): \GMP{
        if($resolution_in_minutes > 60){
            throw new \InvalidArgumentException("Resolution can max be 60 minutes(one hour)");
        }
        if( self::MINUTES_PER_DAY % $resolution_in_minutes != 0 ){
            throw new \InvalidArgumentException("Resolution must be a divisor of 1440(num minutes in 24 hours)");
        }
        if(($end_time->getTimestamp() - $begin_time->getTimestamp()) < $resolution_in_minutes){//needs work!
            throw new \InvalidArgumentException("Difference(".($end_time->getTimestamp() - $begin_time->getTimestamp()).") between begin and end is smaller than the resolutiuon");
        }
        if($begin_time->format('z') != $end_time->format('z')){
            throw new \InvalidArgumentException('Begin and end time, must be the same day');
        }
       
        $begin_min_of_day = \CodeChallenge\Services\TimeTableComputer::DateTimeToMinuteTheDay($begin_time);
        if($resolution_in_minutes > 1){
            $begin_resultion_index = (int)floor($begin_min_of_day/$resolution_in_minutes);
        }
        else{
            $begin_resultion_index = $begin_min_of_day;
        }
        
        //regarding minus 1: we assume that the last minute should not be included, but is the max end of the period 
        $end_min_of_day = \CodeChallenge\Services\TimeTableComputer::CalculateMinuteOfTheDay($end_time)-1;
        if($resolution_in_minutes > 1){
            $end_resultion_index = (int)floor($end_min_of_day/$resolution_in_minutes);
        }
        else{
            $end_resultion_index = $end_min_of_day;
        }
        
        $timeslots = \CodeChallenge\Services\TimeTableComputer::CalculateNumTimeslotsPerDayForResolution($resolution_in_minutes);
        $gmp = gmp_init('0x0');
        for($i = 0; $i<$timeslots; $i++){
            if($i >= $begin_resultion_index && $i <= $end_resultion_index){
                gmp_setbit($gmp, $i);
            }
            else{
                gmp_setbit($gmp, $i, false);
            }
        }
        return $gmp;
    }
    
    /**
     * @param \DateTime $begin_time
     * @param \DateTime $end_time
     * @param int $resolution_in_minutes
     * @return \GMP
     * @throws \InvalidArgumentException
     */
    public static function TimeSlotToGmp(\CodeChallenge\Models\TimeSlot $time_slot, int $resolution_in_minutes): \GMP{
        if($resolution_in_minutes > 60){
            throw new \InvalidArgumentException("Resolution can max be 60 minutes(one hour)");
        }
        if( 1440 % $resolution_in_minutes != 0 ){
            throw new \InvalidArgumentException("Resolution must be a divisor of 1440(num minutes in 24 hours)");
        }
       
        $begin_min_of_day = \CodeChallenge\Services\TimeTableComputer::DateTimeToMinuteTheDay($time_slot->getBegin());
        if($resolution_in_minutes > 1){
            $begin_resultion_index = (int)floor($begin_min_of_day/$resolution_in_minutes);//do thorough testing to see whether it really is floor we should use
        }
        else{
            $begin_resultion_index = $begin_min_of_day;
        }
        
        //regarding minus 1: we assume that the last minute should not be included, but is the max end of the period 
        $end_min_of_day = \CodeChallenge\Services\TimeTableComputer::DateTimeToMinuteTheDay($time_slot->getEnd())-1;
        if($resolution_in_minutes > 1){
            $end_resultion_index = (int)floor($end_min_of_day/$resolution_in_minutes);//do thorough testing to see whether it really is floor we should use
        }
        else{
            $end_resultion_index = $end_min_of_day;
        }
        
        $num_timeslots = \CodeChallenge\Services\TimeTableComputer::CalculateNumTimeslotsPerDayForResolution($resolution_in_minutes);
        $gmp = gmp_init('0x0');
        for($i = 0; $i<$num_timeslots; $i++){
            if($i >= $begin_resultion_index && $i <= $end_resultion_index){
                gmp_setbit($gmp, $i);
            }
            else{
                gmp_setbit($gmp, $i, false);
            }
        }
        return $gmp;
    }
}