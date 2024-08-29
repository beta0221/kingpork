<?php

namespace App\Console\Commands;

use App\Bill;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class DeleteKolOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bill:delete {dumpNum}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete kol order';

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
     * @return mixed
     */
    public function handle()
    {
        $dumpNum = $this->argument('dumpNum');

        $bills = Bill::where('dumpNum', $dumpNum)->get();

        
        DB::transaction(function() use ($bills){
            foreach ($bills as $bill) {
                $bill->billItems()->delete();
                $bill->delete();
            }
        });

        $this->info("!! ***** 刪除完成：$dumpNum ***** !!");
        
    }
}
