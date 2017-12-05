<!DOCTYPE html>
<html>
<head>
	<meta charset='utf8'>
	<title>@yield('title','Sample App') - Laravel_55</title>
	<link rel="stylesheet" type="text/css" href="/css/app.css">
</head>
<body>
	@include('layouts._header')
	<div class="container">
		<div class="col-md-offset-1 col-md-10">
			@include('shared._messages')
			@yield('content')
			@include('layouts._footer')
		</div>
	</div>
	<script src="/js/app.js"></script>
</body>
</html>