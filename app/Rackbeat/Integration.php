<?php namespace App\Rackbeat;

use GuzzleHttp\Client as curl;

class Integration
{
    protected $curl;

    public function __construct( $token = '' )
    {
        $this->curl = new curl( [
            'base_uri' => config( 'rackbeat.integration_endpoint' ),
            'headers'  => [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
                'User-Agent'   => 'Internal Rackbeat integration'
            ]
        ] );
    }

    public static function accept( $token )
    {
        $request = ( new self )->curl->post( "accept/{$token}" );

        return json_decode( $request->getBody()->getContents() );
    }

    public static function cancel( $token )
    {
        $request = ( new self )->curl->post( "cancel/{$token}" );

        return json_decode( $request->getBody()->getContents() );
    }
}
