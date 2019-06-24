<?php namespace App\Rackbeat;

use App\Economic\Models\Order;
use GuzzleHttp\Client as curl;

/**
 * Class Client
 * @package App\Rackbeat
 */
class Client
{
    /**
     * @var curl
     */
    protected $curl;

    /**
     *
     */
    public const STATE_CREATED = 1;
    /**
     *
     */
    public const STATE_UPDATED = 2;

    /**
     * Client constructor.
     * @param string $token
     */
    public function __construct($token = '')
    {
        $this->curl = new curl([
            'base_uri' => config('rackbeat.endpoint'),
            'headers'  => [
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'User-Agent'    => 'Internal ' . config('app.name') . ' PrimeCargo integration',
                'Authorization' => 'Bearer ' . $token
            ]
        ]);
    }

    /**
     * @return mixed
     */
    public function self()
    {
        return json_decode($this->curl->get('self')->getBody()->getContents());
    }

    /**
     * @param $identifier
     * @param null $default
     * @return null
     */
    public function getSetting($identifier, $default = null)
    {
        try {
            return json_decode($this->curl->get("settings/{$identifier}")->getBody()->getContents())->value ?? $default;
        } catch (\Exception $exception) {
            return $default;
        }
    }

    /**
     * @param $pluginSlug
     * @param $url
     * @return mixed
     */
    public function setupPluginSettingsPage($pluginSlug, $url)
    {
        return json_decode($this->curl->post("plugins/{$pluginSlug}/set-settings-url", [
            'json' => [
                'settings_url' => $url
            ]
        ])->getBody()->getContents());
    }

    /**
     * @param $event
     * @param $url
     * @return mixed
     */
    public function setupWebhook($event, $url)
    {
        return json_decode($this->curl->post('webhooks', [
            'json' => [
                'event' => $event,
                'url'   => $url
            ]
        ])->getBody()->getContents());
    }

    /**
     * @param string $code
     * @param null $clientId
     * @param null $clientSecret
     * @return mixed
     */
    public static function getToken($code = '', $clientId = null, $clientSecret = null)
    {
        $curl = new curl();

        try {
            $request = $curl->post(config('rackbeat.domain') . '/oauth/token', [
                'headers'     => [
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'grant_type'    => 'authorization_code',
                    'client_id'     => $clientId ?? config('rackbeat.client_id'),
                    'client_secret' => $clientSecret ?? config('rackbeat.client_secret'),
                    'code'          => $code
                ],
            ]);

            return json_decode($request->getBody()->getContents());
        } catch (\Exception $exception) {
            dd($exception);
        }
    }

    /**
     * @param $token
     * @return Client
     */
    public static function init($token)
    {
        return new self($token);
    }

    /**
     * @param $orderId
     * @param null $fields
     * @return mixed|string
     */
    public function getOrder($orderId, $fields = null)
    {
        try {
            return json_decode($this->curl->get("orders/$orderId", [
                'query' => [
                    'fields' => $fields
                ]
            ])->getBody()->getContents());
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * @param $orderId
     * @param null $fields
     * @return mixed|string
     */
    public function getShipments($orderId, $fields = null)
    {
        try {
            return json_decode($this->curl->get("order-shipments", [
                'query' => [
                    'order_number' => $orderId,
                    'page'         => 1,
                    'fields'       => $fields
                ]
            ])->getBody()->getContents());
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * @param $purchaseOrderId
     * @param null $fields
     * @return mixed|string
     */
    public function getPurchaseOrder($purchaseOrderId, $fields = null)
    {
        try {
            return json_decode($this->curl->get("purchase-orders/$purchaseOrderId", [
                'query' => [
                    'fields' => $fields
                ]
            ])->getBody()->getContents());
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function getPurchaseOrders($page = 0, $limit= 15, $fields = null)
    {
        try {
            return json_decode($this->curl->get("purchase-orders", [
                'query' => [
                    'page' => $page,
                    'limit' => $limit,
                    'fields' => $fields
                ]
            ])->getBody()->getContents());
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function getReceivedPurchaseOrderReceipts(String $location = null)
    {
        try {
            return json_decode($this->curl->get('purchase-order-receipts', [
                'query' => [
                    'is_received'   => true,
                    'location'      => $location,
                ]
            ])->getBody()->getContents());
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
}