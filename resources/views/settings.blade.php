<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="//{{ config('rackbeat.domain') }}/css/app-settings.css"/>

	<title>Rackbeat integration</title>
</head>
<body>

<div class="container" style="margin-top: 50px; margin-bottom: 50px">
	<main role="main" class="small-content">

		<h1 class="title" style="margin: 0 0 16px">Welcome</h1>
		<p style="font-size: 16px; margin: 0 0 32px; color: #555">Some subtitle</p>

		<form action="..." method="post">
			<div class="card regular">
				<div class="card_content">
					<h2 class="card_title">SOME CARD TITLE</h2>

					<div class="input-item">
						<label for="test">Some test dropdown</label>

						<select class="select" name="test" id="test">
							<option value="0">Nothing</option>
						</select>
					</div>

					<h2 class="card_title" style="margin-top: 32px">Another card title</h2>

					<div class="prop-box">
						<label for="next_journal_number">Some text field</label>
						<input type="text" id="test_input" name="test_input" class="input"/>
					</div>

					<div class="table-responsive">
						<table>
							<thead>
							<tr>
								<th>1</th>
								<th>2</th>
								<th>3</th>
							</tr>
							</thead>

							<tbody>
							<tr>
								<td>1</td>
								<td>2</td>
								<td>3</td>
							</tr>
							<tr>
								<td>1</td>
								<td>2</td>
								<td>3</td>
							</tr>
							<tr>
								<td>1</td>
								<td>2</td>
								<td>3</td>
							</tr>
							</tbody>
						</table>
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
	function send_height_to_parent_function() {
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
	function inIframe() {
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
