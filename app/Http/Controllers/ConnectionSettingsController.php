<?php

namespace App\Http\Controllers;

use App\Connection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConnectionSettingsController extends Controller
{
    public function view($userAccountId, Connection $connection)
    {
        if ((int)$connection->rackbeat_user_account_id !== (int)$userAccountId) {
            abort(404);
        }

        return view('settings')->with([
            'connection' => $connection
        ]);
    }

    public function store($userAccountId, Connection $connection, Request $request)
    {
        if ((int)$connection->rackbeat_user_account_id !== (int)$userAccountId) {
            abort(404);
        }

        $B2CShippingMethods = [
            '29601',
            '29603',
            '29604',
        ];
        $B2BShippingMethods = [
            '29601',
            '29602',
            '29605',
        ];

        $validator = Validator::make($request->all(), [
            'primecargo_owner_code'    => 'required',
            'primecargo_customer_type' => 'required',
            'primecargo_shipping_code' => 'required',
            'primecargo_product_type'  => 'required'
        ]);

        $validator->after(function ($validator)  use ($request, $B2CShippingMethods, $B2BShippingMethods) {
            switch ($request->get('primecargo_customer_type')) {
                case 'B2B':
                    if (in_array($request->get('primecargo_shipping_code'), $B2CShippingMethods))
                        $validator->errors()->add('primecargo_shipping_code', 'Den leveringsmetode du har valgt undstøtter ikke din forretningstype');
                    break;
                case 'B2C':
                    if (in_array($request->get('primecargo_shipping_code'), $B2BShippingMethods))
                        $validator->errors()->add('primecargo_shipping_code', 'Den leveringsmetode du har valgt undstøtter ikke din forretningstype');
                    break;
            }
        });

        if ($validator->fails())
            return redirect()->back()->withErrors($validator->errors())->withInput();

        $connection->setSetting('primecargo_owner_code', $request->get('primecargo_owner_code'));
        $connection->setSetting('primecargo_customer_type', $request->get('primecargo_customer_type'));
        $connection->setSetting('primecargo_shipping_code', $request->get('primecargo_shipping_code'));
        $connection->setSetting('primecargo_product_type', $request->get('primecargo_product_type'));

        return redirect()->back()->withSuccess('Dine indstillinger er blevet gemt.');
    }
}
