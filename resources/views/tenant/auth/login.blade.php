<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Tenant</title>
</head>
<body>
    <main style="max-width: 28rem; margin: 4rem auto; font-family: sans-serif;">
        <h1>Masuk ke SIAKAD</h1>
        @if ($errors->any())
            <div role="alert">{{ $errors->first() }}</div>
        @endif
        <form method="post" action="{{ route('tenant.login.store') }}">
            @csrf
            <label>Email <input type="email" name="email" value="{{ old('email') }}" required autofocus></label>
            <label>Password <input type="password" name="password" required></label>
            <label><input type="checkbox" name="remember" value="1"> Ingat saya</label>
            <button type="submit">Masuk</button>
        </form>
    </main>
</body>
</html>
