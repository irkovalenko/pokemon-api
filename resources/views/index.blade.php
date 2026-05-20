<!doctype html>
<html lang="en">

@include('head')

<body class="bg-gray-100">

    @include('nav')

    <main class="container mx-auto mt-20 px-4">
        @yield('body')
    </main>

    @include('footer')

</body>

</html>