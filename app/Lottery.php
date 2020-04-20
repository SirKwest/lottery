<?php


namespace App\Http;

use App\RuleSchema;
use App\Ticket;


class Lottery
{
    /** @var Ticket[] */
    protected $participants;

    /** @var array */
    protected $winners;

    /** @var int[] */
    protected $sequence;

    /** @var RuleSchema */
    protected $rules;

    /** @var int */
    protected $fund;

    private function roll()
    {
        if (count($this->sequence) == 90) {
            return 0;
        }

        do {
            $newNumber = rand(1, 90);
        } while (in_array($newNumber, $this->sequence));

        $this->sequence[] = $newNumber;
        return $newNumber;
    }


    private function getParticipants() : array
    {
        //TODO: Add getting tickets from DB
        $participants = [];
        return [];
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
        $this->participants = $this->getParticipants();
        $this->rules = new RuleSchema();
        $rulesSchema = $this->rules->getSchema();
        usort($rulesSchema, function($a, $b) {
            return $a['priority'] > $b['priority'];
        });

        do {
            $newNumber = $this->roll();
            if (!$newNumber || empty($this->participants)) {
                break;
            }
            $rule = current($rulesSchema);
            $prize = $this->calculatePrize($rule['prize']);
            $winCondition = $rule['rule'];
            $result = false;
            foreach ($this->participants as $key => $ticket) {
                $ticket->mark($newNumber);
                if (method_exists($this->rules, $winCondition)) {
                    $result = call_user_func('RulesSchema::' . $winCondition, $ticket);
                }
                if ($result) {
                    $this->winners[$rule['name']][] = ['ticket' => $ticket, 'prize' => $prize];
                    unset($this->participants[$key]);
                    array_shift($rulesSchema);
                }
            }
            if (isset($this->winners[$rule['name']])) {
                $winnersAmount = count($this->winners[$rule['name']]);
                foreach ($this->winners[$rule['name']] as $winner) {
                    $winner['prize'] /= $winnersAmount;
                }
            }
        } while (false);
    }
}
