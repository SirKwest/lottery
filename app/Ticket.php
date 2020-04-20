<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'lottery_tickets';
    protected $primaryKey = 'ticket_id';

    protected $numbers;
    protected $numberOfBlocks;
    protected $numberOfLines;


    public function getTicket()
    {
        return $this->numbers;
    }

    public function generateTicket($numberOfBlocks = 3, $numberOfLines = 3)
    {
        $this->numberOfLines = $numberOfLines;
        $this->numberOfBlocks = $numberOfBlocks;
        $ticket = [];
        for($i = 1; $i <= $this->numberOfBlocks; $i++)
        {
            $ticket[$i] = $this->generateBlock($this->$numberOfLines);
        }
        $this->numbers = $ticket;
        return $ticket;
    }

    public function mark($number)
    {
        foreach ($this->numbers as $block)
        {
            foreach ($block as &$line)
            {
                $line = array_map(function($value) use ($number) {
                    return ($value == $number) ? 0 : $value;
                }, $line);
            }
            unset($line);
        }
    }

    private function generateBlock($numberOfLines)
    {
        $block = [];
        for($i = 1; $i <= $numberOfLines; $i++)
        {
            $block[$i] = $this->generateLine();
        }
        return $block;
    }

    private function generateLine()
    {
        $columns = [];
        while (count($columns) != 5) {
            $new_column = rand(1, 9);
            if (!isset($columns[$new_column])) {
                $columns[$new_column] = rand(1, 10) + ($new_column - 1) * 10;
            }
        }

        ksort($columns);
        return $columns;
    }
}
