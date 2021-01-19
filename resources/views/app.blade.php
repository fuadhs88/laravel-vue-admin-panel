<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Admin panel</title>
    <style href="{{ asset('css/app.css') }}"></style>
    <script>
        window.Laravel = <?php echo json_encode([
                                'csrfToken' => csrf_token(),
                            ]); ?>
    </script>
</head>

<body>
    <div id="app">

    </div>
    <script src="{{ asset('js/app.js') }}">
    </script>
</body>

</html>