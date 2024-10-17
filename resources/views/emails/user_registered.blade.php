<!DOCTYPE html>
<html>

<head>
    <title>Welcome to MoMo Education!</title>
    <style type='text.css'>
      body{
        font-family: 'sans-serif',
        margin: 0 0.5em;
      }
    </style>
</head>

<body>
    <img src="{{ $message->embed(base_path('/resources/img/icon2024original.png')) }}" />
    <h1>Welcome, {{ $user->name }}!</h1>
    <p>Your account has been created successfully.</p>
    <p>Please click the following link to set your password:</p>

    <a href="{{ $url }}">{{ $url }}</a>

    <p>If you did not request this, no further action is required.</p>
</body>

</html>