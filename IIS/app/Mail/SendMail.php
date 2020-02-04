<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->data['flag'] == "createUser"){
            return $this->from('vut.proj.iis@gmail.com')->subject('Successful registration')->view('EmailTemplates.registerEmailTemplate')->with('data', $this->data);
        }
        else if($this->data['flag'] == "payment"){
            return $this->from('vut.proj.iis@gmail.com')->subject('Payment request')->view('EmailTemplates.paymentEmailTemplate')->with('data', $this->data);
        }
        else if($this->data['flag'] == "deleteUser"){
            return $this->from('vut.proj.iis@gmail.com')->subject('Profile deleted')->view('EmailTemplates.deleteEmailTemplate')->with('data', $this->data);
        }
        else if($this->data['flag'] == "cancelTicket"){
            return $this->from('vut.proj.iis@gmail.com')->subject('Ticket canceled')->view('EmailTemplates.cancelTicketEmailTemplate')->with('data', $this->data);
        }
        else if($this->data['flag'] == "updateUser"){
            return $this->from('vut.proj.iis@gmail.com')->subject('User information update')->view('EmailTemplates.updateUserEmailTemplate')->with('data', $this->data);
        }
        else if($this->data['flag'] == "confirmation"){
            return $this->from('vut.proj.iis@gmail.com')->subject('Ticket confirmation')->view('EmailTemplates.confirmationEmailTemplate')->with('data', $this->data);
        }
        else{
            return 0;
        }
    }
}
