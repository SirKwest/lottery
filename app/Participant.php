<?php


namespace App;


use Illuminate\Support\Facades\DB;

class Participant
{
    /** @var string */
    protected $name;

    /** @var Ticket */
    protected $ticket;

    public function __construct()
    {
        $this->name = 'Random name';
        $this->ticket = new Ticket();
        $this->ticket->generateTicket();
        //$this->ticket->printTicket();
    }

    public function getTicket()
    {
        return $this->ticket;
    }

    public function getName()
    {
        return $this->name;
    }

    protected function save()
    {

    }
}
