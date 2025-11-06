<?php

namespace App\Jobs;

use App\Bill;
use App\InvoiceLog;
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
            if($product->quantity == 0){ continue; }
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

        //紅利折抵
        if($this->bill->bonus_use > 0){
            $Items[] = [
                'ItemName' => '紅利折扣',
                'ItemCount' => 1,
                'ItemWord' => '組',
                'ItemPrice' => (0 - $this->bill->bonus_use),
                'ItemTaxType' => 1,
                'ItemAmount' => (0 - $this->bill->bonus_use),
            ];
        }

        //優惠折抵
        if($this->bill->promo_discount_amount > 0){
            $Items[] = [
                'ItemName' => '優惠折扣',
                'ItemCount' => 1,
                'ItemWord' => '組',
                'ItemPrice' => (0 - $this->bill->promo_discount_amount),
                'ItemTaxType' => 1,
                'ItemAmount' => (0 - $this->bill->promo_discount_amount),
            ];
        }

        $CustomerIdentifier = '';
        $CustomerName = $this->bill->ship_name;
        $Print = '1';       //一律列印
        $CustomerAddr = '';
        $CustomerAddr = $this->bill->ship_county . $this->bill->ship_district . $this->bill->ship_address;
        if($this->bill->ship_receipt == 3){ //三聯式發票
            $CustomerIdentifier = $this->bill->ship_three_id;
            $CustomerName = $this->bill->ship_three_company;
            // $Print = '1';
        }

        $data = [
            'RelateNumber'=>$this->bill->bill_id,
            'Items'=>$Items,
            'CustomerEmail'=>$this->bill->ship_email,
            'SalesAmount'=>$this->bill->price,
            'CustomerIdentifier'=>$CustomerIdentifier,
            'CustomerName'=>$CustomerName,
            'CustomerAddr'=>$CustomerAddr,
            'Print'=>$Print
        ];
        $data = escapeshellarg(json_encode($data));

        $file_path = storage_path() . $this->type;
        $output = shell_exec("php $file_path $data");

        Log::notice('<發票紀錄：' . $this->bill->bill_id . '>');
        Log::notice($output);
        Log::notice('</發票紀錄：' . $this->bill->bill_id . '>');
        InvoiceLog::log($this->bill->bill_id,$output);

    }
}
