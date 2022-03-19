<!DOCTYPE html>
<html lang="en">

<head>
    <title>@yield('title')</title>

    <!-- Meta tag Keywords -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8" />
    <!-- //Meta tag Keywords -->

    <!-- Custom-Files -->
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <!-- Bootstrap-Core-CSS -->

</head>
<body>
<header>
    <nav class="navbar flex-column">
        <div class="d-flex">
            <a class="btn btn-primary mx-3" href="{{route('home')}}">Home</a>
            <a class="btn btn-primary mx-3" href="{{route('admin.users')}}">Users</a>
        </div>
    </nav>
</header>

<div class="container mt-5">
    @yield('content')
</div>

</body>
</html>
