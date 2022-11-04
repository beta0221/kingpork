<?php

namespace App\Console\Commands;

use App\Bill;
use App\Helpers\ECPay;
use Illuminate\Console\Command;

class CheckBill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bill:check {from} {to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '檢查信用卡訂單';

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
        $from = $this->argument('from');
        $to = $this->argument('to');

        $bills = Bill::where('id','>=',$from)
        ->where('id','<=',$to)
        ->where('status',1)
        ->where('pay_by','CREDIT')
        ->get(); 

        foreach ($bills as $bill) {
            $this->info('bill:' . $bill->bill_id . ' (' . $bill->shipmentName() . ')');
            
            $ecpay = new ECPay($bill);
            $info = $ecpay->getPayRequestInfo();
            if(!isset($info['RtnCode'])){ continue; }

            if($info['RtnCode'] != 1){
                $this->info("!! ***** 錯誤訂單 ***** !!");
            }

            $this->info('------------------------');
        }

        $this->info('from:'.$from.', to:'.$to.' (success)');
    }
}
