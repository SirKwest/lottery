<?php


namespace App\Http\Controllers;


use App\Ticket;

class ClientController extends Controller
{
    public function register()
    {
        $ticket = new Ticket();
        return view('client', ['ticket' => $ticket->generateTicket()]);
    }

    private function getAllTickets()
    {
        $res = [];
        for($i = 1; $i <= 10; $i++) {
            $res[] = $this->generateTicket();
        }
        return $res;
    }

    private function checkWinningLine()
    {

    }

    private function checkWinningBlock()
    {

    }

    private function checkWinningTwoBlocks()
    {

    }

    private function roll($previousNumbers)
    {
        $roll = rand(1, 90);
        return (in_array($roll, $previousNumbers) ? $this->roll($previousNumbers) : $roll);
    }

    public function lottery()
    {
        $tickets = $this->getAllTickets();
        $lotterySequence = [];
        $lineCriteriaWins = false;
        $blockCriteriaWins = false;
        $twoBlocksCriteriaWins = false;
        do  {
            $new_number = $this->roll($lotterySequence);
            $lotterySequence[] = $new_number;
            if (count($lotterySequence) > 4 && !$lineCriteriaWins) {
                foreach ($tickets as $ticket) {
                    $this->checkWinningLine($ticket, $lotterySequence);
                }
            }
        } while ((count($lotterySequence) != 90));
    }

}
