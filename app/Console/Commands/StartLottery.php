<?php

namespace App\Console\Commands;

use App\Lottery;
use App\Participant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class StartLottery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lottery:start {participants} {bank}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $numberOfParticipants = $this->argument('participants');
        $fund = $this->argument('bank');
        /*if ($numberOfParticipants < 10) {
            $numberOfParticipants = 10;
        }*/
        $participants = [];
        for($i = 0; $i < $numberOfParticipants; $i++) {
            $participants[] = new Participant();
        }
        $lottery = new Lottery($fund);
        $lottery->setParticipants($participants);
        $lottery->initiate();
        $winners = $lottery->getTopWinners();
        foreach ($winners as $winner) {
            echo $winner['name'] . ' - ' . $winner['prize'] . "\n";
        }
        //var_dump($result);
        //DB::table('users')->updateOrInsert(['token' => 'ololo', 'ticket_id' => 3], ['token' => 'ololo', 'ticket_id' => 3]);
    }
}
