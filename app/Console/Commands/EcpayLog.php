<?php

namespace App\Console\Commands;

use App\Bill;
use App\Helpers\ECPay;
use Illuminate\Console\Command;

class EcpayLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ecpay:log {bill_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check for ecpay log';

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
        $bill_id = $this->argument('bill_id');

        if(!$bill = Bill::where('bill_id',$bill_id)->first()){ return; }

        $ecpay = new ECPay($bill);
        $info = $ecpay->getPaymentInfo();

        $json = json_encode($info);

        $this->info("-----------------------------------------------");
        $this->info($json);

    }
}
