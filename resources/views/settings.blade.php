<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ config('rackbeat.domain') }}/css/app-settings.css"/>

    <title>Rackbeat integration</title>
</head>
<body>

<div class="container">
    <main role="main" class="small-content">

        <h1 class="title">Velkommen</h1>

        @if (count($errors) > 0)
            <div class="alert danger">
                <strong>Hovsa!</strong> Vi kan se der er opstået nogle fejl.<br>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @else
            @foreach (['success', 'info'] as $key)
                @if(Session::has($key))
                    <div class="alert {{ $key }}">{{ Session::get($key) }}</div>
                @endif
            @endforeach
        @endif

        <form action="{{ route('settings-post', [$connection->rackbeat_user_account_id, $connection]) }}" method="post">
            <div class="card regular">
                <div class="card_content">
                    <h2 class="card_title">PrimeCargo indstillinger</h2>

                    <div class="input-item is-full">
                        <label for="primecargo_owner_code">Ejer kode:</label>

                        <input type="text" name="primecargo_owner_code" required="true" class="input" value="{{ $connection->getSetting('primecargo_owner_code') }}" placeholder="Eks. 555">
                    </div>

                    <div class="input-item is-full">
                        <label for="primecargo_business_type">Forretnings type:</label>

                        <select class="select" required="true" name="primecargo_customer_type" id="primecargo_customer_type">
                            <option value="B2C" @if($connection->getSetting('primecargo_customer_type') === 'B2C' || old('primecargo_customer_type') === 'B2C') selected @endif>Business to Customer</option>
                            <option value="B2B" @if($connection->getSetting('primecargo_customer_type') === 'B2B' || old('primecargo_customer_type') === 'B2B') selected @endif>Business to Business</option>
                        </select>
                    </div>

                    <div class="input-item is-full">
                        <label for="primecargo_shipping_code">Leveringsmetode</label>
                        <select name="primecargo_shipping_code" id="primecargo_shipping_code" class="select" required="true">
                            <option value="29601" @if($connection->getSetting('primecargo_shipping_code') === '29601' || old('primecargo_shipping_code') === '29601') selected @endif>Standard</option>
                            <option value="29602" @if($connection->getSetting('primecargo_shipping_code') === '29602' || old('primecargo_shipping_code') === '29602') selected @endif>[B2B] - GLS</option>
                            <option value="29604" @if($connection->getSetting('primecargo_shipping_code') === '29604' || old('primecargo_shipping_code') === '29604') selected @endif>[B2C] - GLS Home delivery</option>
                            <option value="29605" @if($connection->getSetting('primecargo_shipping_code') === '29605' || old('primecargo_shipping_code') === '29605') selected @endif>[B2B] - GLS EU</option>
                        </select>
                    </div>

                    <div class="input-item is-full">
                        <label for="primecargo_product_type">Leverings pakke type</label>
                        <select class="select" name="primecargo_product_type" id="primecargo_product_type" required="true">
                            <option value="F" @if($connection->getSetting('primecargo_product_type') === 'F' || old('primecargo_product_type') === 'F') selected @endif>Flad</option>
                            <option value="B" @if($connection->getSetting('primecargo_product_type') === 'B' || old('primecargo_product_type') === 'B') selected @endif>Boks</option>
                            <option value="H" @if($connection->getSetting('primecargo_product_type') === 'H' || old('primecargo_product_type') === 'H') selected @endif>Hængende</option>
                            <option value="O" @if($connection->getSetting('primecargo_product_type') === 'O' || old('primecargo_product_type') === 'O') selected @endif>Oversize</option>
                            <option value="S" @if($connection->getSetting('primecargo_product_type') === 'S' || old('primecargo_product_type') === 'S') selected @endif>Sortiment</option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="button regular">Save settings</button>
            {{ csrf_field() }}
        </form>
    </main>
</div>

<script>
	// This below will update the iframe to fit the full height
	function send_height_to_parent_function () {
		var height = document.getElementsByTagName("body")[0].offsetHeight;
		parent.postMessage({"height": height}, "*");
	}

	window.addEventListener("resize", function () {
		if (window.self === window.top) {
			return;
		}
		send_height_to_parent_function();
	});

	window.addEventListener("load", function () {
		if (window.self === window.top) {
			return;
		}

		send_height_to_parent_function();

		var observer = new MutationObserver(send_height_to_parent_function);
		var config = {attributes: true, childList: true, characterData: true, subtree: true};
		observer.observe(window.document, config);
	});

	// This will add a slight margin to the container if not in an iframe
	function inIframe () {
		try {
			return window.self !== window.top;
		} catch (e) {
			return true;
		}
	}

	if (!inIframe()) {
		document.querySelector(".container").style.setProperty("margin-top", "16px");
	}
</script>
</body>
</html>
