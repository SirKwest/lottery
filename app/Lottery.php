<?php


namespace App;

use App\RuleSchema;
use App\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class Lottery
{
    /** @var Participant[] */
    protected $participants;

    /** @var array */
    public $winners;

    /** @var int[] */
    protected $sequence;

    /** @var RuleSchema */
    protected $rules;

    /** @var int */
    protected $fund;

    /** @var int */
    protected $number;

    public function __construct($fund)
    {
        $this->sequence = [];
        $this->fund = $fund;
        $this->number = DB::table('lottery')->count() + 1;
        DB::table('lottery')->insert(['bank' => $this->fund, 'sequence' => '', 'winners' => '']);
    }

    private function roll()
    {
        if (count($this->sequence) == 90) {
            return 0;
        }

        do {
            $newNumber = rand(1, 90);
        } while (in_array($newNumber, $this->sequence));

        Log::debug('New number: ' . $newNumber);
        $this->sequence[] = $newNumber;
        return $newNumber;
    }


    private function getParticipants() : array
    {
        //TODO: Add getting tickets from DB
        $participants = [];
        return [];
    }

    public function setParticipants(array $data)
    {
        $this->participants = $data;
    }

    private function calculatePrize($part) : int
    {
        if (empty($this->fund)) {
            return 0;
        }
        return $this->fund * $part / 100;
    }

    public function initiate()
    {
        Log::info('Initiating lottery process');
        //$this->participants = $this->getParticipants();
        $this->rules = new RuleSchema();
        $rulesSchema = $this->rules->getSchema();
        usort($rulesSchema, function($a, $b) {
            return $a['priority'] < $b['priority'];
        });

        do {
            $newNumber = $this->roll();
            if (!$newNumber || empty($this->participants)) {
                break;
            }
            $rule = current($rulesSchema);
            $prize = $this->calculatePrize($rule['prize']);
            $winCondition = $rule['rule'];
            Log::debug('Win Condition: '. $winCondition);
            $result = false;
            foreach ($this->participants as $key => $participant) {
                $ticket = $participant->getTicket();
                //$ticket->printTicket();
                $ticket->mark($newNumber);
                if (method_exists($this->rules, $winCondition)) {
                    $result = call_user_func("\App\RuleSchema::" . $winCondition, $ticket);
                }
                if ($result) {
                    //$ticket->printTicket();
                    $this->winners[$rule['name']][] = ['name' => $participant->getName(),'ticket' => $ticket, 'prize' => $prize];
                    //var_dump($this->winners[$rule['name']]);
                    unset($this->participants[$key]);
                    array_shift($rulesSchema);
                }
            }
            if (isset($this->winners[$rule['name']])) {
                Log::debug('Calculating winners prize');
                $winnersAmount = count($this->winners[$rule['name']]);
                foreach ($this->winners[$rule['name']] as $winner) {
                    $winner['prize'] /= $winnersAmount;
                }
                $this->fund -= $prize;
            }
        } while (true);
        $this->finishing();
        Log::info('Finishing lottery process');
    }

    protected function finishing()
    {
        DB::table('lottery')->updateOrInsert(['id' => $this->number], ['sequence' => $this->sequence]);
        DB::table('lottery')->updateOrInsert(['id' => $this->number], ['winners' => $this->winners]);
    }

    public function getTopWinners($size = 10)
    {
        if (empty($this->winners)) {
            Log::info('There are no winners here');
            return [];
        }
        $winners = [];
        foreach ($this->winners as $winnersBySpecificRule) {
            $winners = array_merge($winners, $winnersBySpecificRule);
        }
        usort($winners, function ($a, $b) {
            return $a['prize'] < $b['prize'];
        });

        if ($size) {
            return array_slice($winners, 0, $size);
        }
    }
}
