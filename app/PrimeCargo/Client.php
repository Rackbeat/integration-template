<?php

namespace App\PrimeCargo;


use App\Connection;
use App\PurchaseOrder;
use Carbon\Carbon;
use File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\ArrayToXml\ArrayToXml;

class Client
{
    public function __construct()
    {
        $this->localFileOut = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'out' . DIRECTORY_SEPARATOR);
        $this->localFileIn = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'in' . DIRECTORY_SEPARATOR);

        $this->extFileOut = config('primecargo.fileout');
        $this->extFileIn = config('primecargo.filein');
    }

    public function createSalesOrder($order = null, $shipment = null, Connection $connection)
    {
        if (!$order && !$shipment)
            throw new \Exception('Missing order and/or shipment', 500);


        $salesOrder = [
            'TelegramHeader'   => [
                'OwnerCode'        => $connection->getSetting('primecargo_owner_code'), // todo: change this to match connection setting
                'OrderNumber'      => $order->number,
                'CreationDateTime' => \Carbon\Carbon::now()->format('d-m-Y H:i:s')
            ],
            'SalesOrderHeader' => [
                'OrderData' => [
                    'LanguageCode' => 2,
                    'AutoRelease'  => 1,
                    'OrderTypeId'  => 2,
                    'TemplateCode' => 29601,
                    'HoldCode'     => 0,
                ],
                'Receiver'  => [
                    'Name'          => $order->delivery_address->name,
                    'Address1'      => $order->delivery_address->street,
                    'Address2'      => $order->delivery_address->street2,
                    'Address3'      => '',
                    'Zipcode'       => $order->delivery_address->zipcode,
                    'City'          => $order->delivery_address->city,
                    'State'         => '',
                    'Country'       => $order->delivery_address->country,
                    'Email'         => $order->customer->contact_email,
                    'ContactPerson' => $order->customer->name,
                    'PhoneFax'      => $order->customer->contact_phone,
                ],
                'Shipping'  => [
                    'Date'        => Carbon::parse($shipment->shipped_at)->format('d-m-Y'),
                    'ProductCode' => $connection->getSetting('primecargo_shipping_code'), // todo: Create a setting for this based on what default shipping method they want.
                ],
                'Customer'  => [
                    'Number' => $order->customer->number
                ],

            ],
            'SalesOrderLine'   => $this->handleSalesOrderLines($order->lines, $order, $connection)
        ];

        $xml = new ArrayToXml($salesOrder, [
            'rootElementName' => 'SalesOrder',
            '_attributes'     => [
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xmlns:xsd' => 'http://www.w3.org/2001/XMLSchema',
                'xmlns'     => 'http://www.primecargo.dk'
            ]
        ], true, 'ISO-8859-1', '1.0');
        $xml = $xml->toXml();

        $filename = 'DW2K' . Carbon::now()->format('Ymdhis') . $order->number . '.xml';
        $this->uploadXMLToFtp($filename, $xml);
    }

    public function createPurchaseOrder($purchaseOrder = null, Connection $connection)
    {
        $purchaseOrderXML = [
            'TelegramHeader'      => [
                'OwnerCode'        => $connection->getSetting('primecargo_owner_code'), // todo: change this to match connection setting
                'OrderNumber'      => $purchaseOrder->number,
                'CreationDateTime' => Carbon::now()->format('d-m-Y H:i:s')
            ],
            'PurchaseOrderHeader' => [
                'ETA'        => Carbon::parse($purchaseOrder->preferred_delivery_date)->format('d-m-Y'),
                'StatusType' => 'C'
            ],
            'PurchaseOrderLine'   => $this->handlePurchaseOrderLines($purchaseOrder->lines, $connection)
        ];

        $xml = new ArrayToXml($purchaseOrderXML, [
            'rootElementName' => 'PurchaseOrder',
            '_attributes'     => [
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xmlns:xsd' => 'http://www.w3.org/2001/XMLSchema',
                'xmlns'     => 'http://www.primecargo.dk'
            ]
        ], true, 'ISO-8859-1', '1.0');
        $xml = $xml->toXml();

        $filename = 'DW2K' . Carbon::now()->format('Ymdhis') . $purchaseOrder->number . '.xml';
        $this->uploadXMLToFtp($filename, $xml);

        return $connection->purchaseOrders()->create([
            'number'      => $purchaseOrder->number,
            'is_uploaded' => true,
            'uploaded_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }

    public function handleStockRegulation()
    {

        $rawFiles = Storage::disk(config('primecargo.connection'))->files($this->extFileOut);

        $this->parseFiles($rawFiles, 'DW2K108');

        return $rawFiles;
    }

    private function handleSalesOrderLines($lines, $order, Connection $connection)
    {
        $totalLines = [];
        foreach ($lines as $line) {
            $constructLine = [
                'OrderLine'         => $line->id,
                'UnitCodeId'        => $line->item->barcode,
                'PartNumber'        => $line->child_id,
                'Quantity'          => $line->quantity,
                'Description'       => $line->item->description,
                'CostPrice'         => $line->item_cost_price,
                'CostCurrencyCode'  => $order->currency,
                'SalesPrice'        => $line->line_price,
                'SalesCurrencyCode' => $order->currency,
            ];

            if (count($line->variations) > 0) {
                $variantArea = [];
                foreach ($line->variations as $variation) {
                    $variantArea[] = [
                          'Id' => $variation->variation_type->id,
                          'Name' => $variation->variation_option->name
                    ];
                }

                $constructLine['VariantArea']['Variant'] = $variantArea;
            }

            $totalLines[] = $constructLine;
        }

        return $totalLines;
    }

    private function handlePurchaseOrderLines($lines, Connection $connection)
    {
        $totalLines = [];
        foreach ($lines as $line) {
            $constructLine = [
                'OrderLine'    => $line->id,
                'UnitCodeId'   => $line->item->barcode,
                'PartNumber'   => $line->child_id,
                'Quantity'     => $line->quantity,
                'FIFO'         => 0,
                'ProductType'  => 'F' // todo: Figure out if this should be a connection setting
            ];

            if (count($line->variations) > 0) {
                $variantArea = [];
                foreach ($line->variations as $variation) {
                    $variantArea[] = [
                        'Id' => $variation->variation_type->id,
                        'Name' => $variation->variation_option->name
                    ];
                }

                $constructLine['VariantArea']['Variant'] = $variantArea;
            }

            $totalLines[] = $constructLine;
        }

        return $totalLines;
    }

    private function uploadXMLToFtp($filename, $xml)
    {
        $file = File::put($this->localFileOut . $filename, $xml);

        Storage::disk('local-ftp')->put($this->extFileIn . $filename, $xml);
        Log::info('Successfully uploaded ' . $filename . ' to "' . $this->extFileIn . '" on the FTP server');
    }

    private function getFileFromFtp($filename)
    {
        return Storage::disk('local-ftp')->get($filename);
    }

    private function parseFiles($rawFiles, $filename = null)
    {
        $parsedFiles = [];
        foreach ($rawFiles as $rawFile) {
            $explode = explode('/', $rawFile, 2);
            $parsedFiles[] = @$explode[1];
        }

        if ($filename) {
            $filtered = array_filter($parsedFiles, function ($item) use ($filename) {
                if (preg_match("/({$filename})/i", $item)) {
                    return $item;
                }
            });
        }
    }
}