<?php
    function saveSession($username, $is_admin) {
        $_SESSION['username'] = $username;
        $_SESSION['is_admin'] = $is_admin;
    }
    function checkPost() {
        return ($_SERVER['REQUEST_METHOD'] == 'POST') ? true : false;
    }
    function getPost() {
        $username = $_POST['user'] ?? '';
        $password = $_POST['password'] ?? '';
        return [$username, $password];
    }
    function verifyLogin($username, $password) {
        // conexión a la bbdd (ajusta credenciales al crearla)
        $pdo = new PDO('mysql:host=localhost;dbname=complaints_chat;charset=utf8mb4',
               'usuario', 'contraseña', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        // Consulta preparada: Busca usuario y une con la tabla contraseñas
        $stmt = $pdo->prepare('SELECT U.username, P.passwd, U.is_admin 
                   FROM Users U
                   JOIN Passwords P ON U.id_passwd = P.id
                   WHERE U.username = ?');

        // ejecuta la consulta con el parametro del username
        $stmt->execute([$username]);

        // se guarda en un array asociativo el resultado de la consulta
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['passwd'])) {
            return [
                'username' => $user['username'],
                'is_admin' => $user['is_admin'],
                'login' => true
            ];
        } else {
            return [
                'login' => false,
                'error' => 'Tas equivocao'
            ];
        }
    }

    function main() {
        session_start();

        $error = "";

        if (checkPost()) {
            list($username, $password) = getPost();
            $resultado = verifyLogin($username, $password);

            if ($resultado['login']) {
                saveSession($resultado['username'], $resultado['is_admin']);
                if ($resultado['is_admin']) {
                    header("Location: admin_panel.php");
                    exit;
                } else {
                    header("Location: chat.php");
                    exit;
                }
            } else {
                $error = $resultado['error'];
            }
        }
    }
    main();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/sign_in.css">
    <title>Inicio de sesión</title>
</head>
<body>
    <div id="formulario-inicio-sesion">
        <form method="POST" action="">
            <label for="user">Usuario:</label>
            <input type="text" id="user" name="user" required>
            
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Iniciar sesión">
        </form>
    </div>
</body>
</html>