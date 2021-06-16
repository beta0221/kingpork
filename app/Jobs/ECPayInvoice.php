<?php

namespace App\Jobs;

use App\Bill;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ECPayInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const TYPE_ISSUE = "/invoice/ecpay_invoice_issue.php";
    const TYPE_DELAY = "/invoice/ecpay_invoice_delay.php";
    const TYPE_TRIGGER = "/invoice/ecpay_invoice_trigger.php";

    private $bill;
    private $type;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Bill $bill,$type)
    {
        $this->bill = $bill;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        sleep(3);
        
        $products = $this->bill->products();
        $Items = [];
        foreach ($products as $product) {
            $ItemAmount = $product->quantity * $product->price;
            $Items[] = [
                'ItemName' => $product->name,
                'ItemCount' => $product->quantity,
                'ItemWord' => '組',
                'ItemPrice' => $product->price,
                'ItemTaxType' => 1,
                'ItemAmount' => $ItemAmount,
            ];
        }
        
        $data = [
            'RelateNumber'=>$this->bill->bill_id,
            'Items'=>$Items,
            'CustomerEmail'=>$this->bill->ship_email,
            'SalesAmount'=>$this->bill->price
        ];
        $data = escapeshellarg(json_encode($data));

        $file_path = storage_path() . $this->type;
        $output = shell_exec("php $file_path $data");

        // Log::useDailyFiles(storage_path().'/logs/invoice.log');
        Log::notice('訂單編號：' . $this->bill->bill_id);
        Log::notice($output);
        Log::notice('-----------');

    }
}
