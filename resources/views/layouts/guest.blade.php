<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Page')</title>

    @include('components.header')

    @yield('styles')
</head>

<body>
    @include('components.navbar')

    <div class="content">
        @yield('content')
    </div>

    @include('components.footer')

    @yield('scripts')
</body>

</html>
