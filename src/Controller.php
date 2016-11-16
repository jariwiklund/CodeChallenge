<?php
class Controller {
    
    private $request;
    public function __construct(\Psr\Http\Message\RequestInterface $request){
        //todo: setup routing...
        $this->request = $request;
    }
    
    public function CreateBooking(){
        $validator = new \CodeChallenge\Validators\BookingJsonValidator();
        $validator->validateData($this->request->getBody());
    }
    
    public function AddNotificationToBooking(\CodeChallenge\Models\Booking $booking){
        $validator = new \CodeChallenge\Validators\NotificationJsonValidator();
        $validator->validateData($this->request->getBody());
        //todo:persist
    }
    
}