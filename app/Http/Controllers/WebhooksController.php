<?php

namespace App\Http\Controllers;

use App\Connection;
use App\Rackbeat\Client as Rackbeat;
use App\PrimeCargo\Client as PrimeCargo;
use Illuminate\Http\Request;

class WebhooksController extends Controller
{
    public function orderShipped(Request $request, $userAccountId, Connection $connection)
    {
        \Log::info('WEBHOOK: OrderShipped received. Data contains: ' . json_encode($request->all()));

        if ($request->key_name === 'number') {
            $rackbeat = new Rackbeat($connection->rackbeat_token);
            try {
                $order = $rackbeat->getOrder($request->key, '
                    number,
                    lines(id,child_id,quantity,line_price,item_cost_price,variations,item(description,barcode)),
                    currency,
                    customer,
                    delivery_address,
                    billing_address,
                    is_shipped,
                    is_cancelled,
                    is_partly_shipped,
                    our_reference,
                    their_reference'
                )->order;

                $shipment = array_slice($rackbeat->getShipments($request->key, 'id,is_shipped,shipped_at')->order_shipments, -1)[0];

                $primecargo = new PrimeCargo();

                $primecargo->createSalesOrder($order, $shipment, $connection);
            } catch (\Exception $e) {
                \Log::error('Having difficulties fetching needed parameters from Rackbeat. Message: ' . $e->getMessage());
            }


        }
    }
}
