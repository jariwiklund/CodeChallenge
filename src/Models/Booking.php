<?php
namespace CodeChallenge\Models;

class Booking {
    
    /**
     * UUID
     * @var string
     */
    private $id;
    
    /**
     * I am interpreting "a booking has a customer" as the customer is booked, 
     * not as the customer is the one making the booking, in that case the naming would be $booking_customer
     * 
     * @var \CodeChallenge\Models\Customer
     */
    private $booked_customer;
    
    /**
     * @var \CodeChallenge\Models\Notification[]
     */
    private $notifications;
    
    /**
     * At what time does the booking begin
     * @var \DateTime
     */
    private $begin_time;
    
    /**
     * At what time does the booking end
     * @var \DateTime
     */
    private $end_time;
    
    
    
}