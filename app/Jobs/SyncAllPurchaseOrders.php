<?php

namespace App\Jobs;

use App\Connection;
use App\PrimeCargo\Client as PrimeCargo;
use App\PurchaseOrder;
use App\Rackbeat\Client as Rackbeat;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncAllPurchaseOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Connection
     */
    private $integration;
    /**
     * @var int
     */
    private $page;

    /**
     * Create a new job instance.
     *
     * @param Connection $integration
     * @param null $purchaseOrder
     */
    public function __construct(Connection $integration, $page = 0)
    {
        $this->integration = $integration;
        $this->page = $page;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $rackbeat = new Rackbeat($this->integration->rackbeat_token);

        $purchaseOrders = $rackbeat->getPurchaseOrders($this->page, 250, 'number,lines,is_ready_for_receiving,preferred_delivery_date,currency,total_total');

        $readyForRecevingPurchaseOrders = array_filter($purchaseOrders->purchase_orders, function ($purchaseOrder) {
            if ($purchaseOrder->is_ready_for_receiving) {
                return $purchaseOrder;
            }
        });

        $primecargo = new PrimeCargo();

        foreach ($readyForRecevingPurchaseOrders as $purchaseOrder) {
            if ($this->integration->purchaseOrders()->where('number', '=', $purchaseOrder->number)->get()->count() <= 0) {
                $primecargo->createPurchaseOrder($purchaseOrder, $this->integration);
            }
        }

        if ($purchaseOrders->page < $purchaseOrders->pages) {
            dispatch(new SyncAllPurchaseOrders($this->integration, ($purchaseOrders->page + 1)));
        }
    }
}
