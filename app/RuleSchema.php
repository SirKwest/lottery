<?php


namespace App;


class RuleSchema
{
    public function getSchema()
    {
        return [
            [
                'name'        => 'gold',
                'priority'    => '10',
                'rule'        => 'lineConditionWin',
                'prize'       => '50',
            ],
            [
                'name'        => 'silver',
                'priority'    => '9',
                'rule'        => 'blockConditionWin',
                'prize'       => '50',
            ],
            [
                'name'        => 'bronze',
                'priority'    => '8',
                'rule'        => 'twoBlockConditionWin',
                'prize'       => '50',
            ]
        ];
    }
    public function lineConditionWin(Ticket $ticket)
    {
        foreach ($ticket->getTicket() as $block)
        {
            foreach ($block as $line)
            {
                if (!array_sum($line)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function blockConditionWin(Ticket $ticket)
    {
        $ticket_blocks = $ticket->getTicket();

        $last_block = end($ticket_blocks);
        $checkSum = $this->checkBlockStatus($last_block);
        if (!$checkSum) {
            return true;
        }

        $first_block = reset($ticket_blocks);
        $checkSum = $this->checkBlockStatus($first_block);
        return !$checkSum;
    }

    private function checkBlockStatus($block)
    {
        $sum = 0;
        foreach ($block as $line)
        {
            $sum += array_sum($line);
        }
        return $sum;
    }

    public function twoBlockConditionWin(Ticket $ticket)
    {
        $ticket_blocks = $ticket->getTicket();
        $blockStatuses = [];

        foreach ($ticket_blocks as $block)
        {
            $blockStatuses[] = $this->checkBlockStatus($block);
        }

        $completedBlocks = array_filter($blockStatuses, function($status) {
            return !$status;
        });
        return count($completedBlocks) >= 2;
    }
}
