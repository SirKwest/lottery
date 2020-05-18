<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LotteryStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lottery:statistics';

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

    public function fn_array_merge()
    {
        $arg_list = func_get_args();
        $preserve_keys = true;
        $result = array();
        if (is_bool(end($arg_list))) {
            $preserve_keys = array_pop($arg_list);
        }

        foreach ((array) $arg_list as $arg) {
            foreach ((array) $arg as $k => $v) {
                if ($preserve_keys == true) {
                    $result[$k] = !empty($result[$k]) && is_array($result[$k]) ? $this->fn_array_merge($result[$k], $v) : $v;
                } else {
                    $result[] = $v;
                }
            }
        }

        return $result;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $sum = DB::table('lottery')->sum('bank');
        echo 'Totally gambled: ' . $sum;
        $tickets = DB::table('lottery')->get('winners');
        //var_dump($tickets);
        $winners = [];
        foreach ($tickets as $ticket) {
            $winners[] = json_decode($ticket->winners, true);
        }
        /*var_dump($winners);
        $result = [];
        foreach ($winners as $winner) {
            var_dump($result);
            $result = $this->fn_array_merge($result, $winner);
        }*/
    }
}
