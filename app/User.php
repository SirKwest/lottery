<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    protected $table = 'lottery_tickets';
    protected $primaryKey = 'ticket_id';
}
