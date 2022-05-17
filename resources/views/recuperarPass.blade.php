<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>RestablacerPass</title>
</head>

<body>
    <h4>Estimado: {{ $user->name }}</h4>
    <p>Usted ha solicitado el reestablecimiento de contraseña desde
        <strong>SAJONA</strong>, la siguiente es la contraseña que deberá utilizar en su siguiente ingreso:
    </p>
    <p><strong>Clave de acceso:</strong> {{ $pass }}</p>
    <p>La contraseña fue generada de manera automática por
        <strong>SAJONA</strong>, recuerde cambiarla la próxima vez que ingrese al sistema.</p>
</body>

</html>
