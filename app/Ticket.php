<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
        Log::info('Entering generate ticket function');
        $this->numberOfLines = $numberOfLines;
        $this->numberOfBlocks = $numberOfBlocks;
        $ticket = [];
        for($i = 1; $i <= $this->numberOfBlocks; $i++) {
            $ticket[$i] = $this->generateBlock($this->numberOfLines);
        }
        $this->numbers = $ticket;
        return $ticket;
    }

    public function mark($number)
    {
        Log::debug('Marking number '. $number);
        foreach ($this->numbers as &$block)
        {
            foreach ($block as &$line)
            {
                $line = array_map(function($value) use ($number) {
                    return ($value == $number) ? 0 : $value;
                }, $line);
            }
            unset($line);
        }
        unset($block);
    }

    private function generateBlock($numberOfLines)
    {
        Log::info('Entering generate block function');
        Log::debug('number of lines: ' . $numberOfLines);
        $block = [];
        for($i = 1; $i <= $numberOfLines; $i++)
        {
            $block[$i] = $this->generateLine();
        }
        return $block;
    }

    private function generateLine()
    {
        Log::info('Entering generate line function');
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

    public function printTicket()
    {
        for($i = 1; $i <= $this->numberOfBlocks; $i++) {
            echo 'Printing Block ' . $i;
            for($j = 1; $j <= $this->numberOfLines; $j++) {
                var_dump($this->numbers[$i][$j]);
            }
        }
    }
}
