<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Tenant</title>
</head>
<body>
    <main style="max-width: 48rem; margin: 4rem auto; font-family: sans-serif;">
        <h1>Dashboard Tenant</h1>
        <p>Selamat datang, {{ auth('tenant')->user()->name }}.</p>
        <form method="post" action="{{ route('tenant.logout') }}">
            @csrf
            <button type="submit">Keluar</button>
        </form>
    </main>
</body>
</html>
