<?php

namespace App\Console\Commands;

use App\Bill;
use App\Jobs\ECPayInvoice;
use Illuminate\Console\Command;

class InvoiceIssue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:issue {bill_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Issue invoice';


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
        dispatch(new ECPayInvoice($bill,ECPayInvoice::TYPE_ISSUE));

    }
}
