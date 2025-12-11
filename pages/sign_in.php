<?php
    function saveSession($username, $is_admin) {
        $_SESSION['username'] = $username;
        $_SESSION['is_admin'] = $is_admin;
    }

    function checkPost() {
        return ($_SERVER['REQUEST_METHOD'] == 'POST');
    }

    function getPost() {
        $username = $_POST['user'] ?? '';
        $password = $_POST['password'] ?? '';
        return [$username, $password];
    }

    function verifyLogin($username, $password) {
        // --- USUARIOS DE PRUEBA (sin base de datos) ---
        // test1 / test1 -> admin
        if ($username === 'test1' && $password === 'test1') {
            return [
                'username' => 'test1',
                'is_admin' => 1,
                'login'    => true
            ];
        }

        // test2 / test2 -> usuario normal
        if ($username === 'test2' && $password === 'test2') {
            return [
                'username' => 'test2',
                'is_admin' => 0,
                'login'    => true
            ];
        }

        // --- A partir de aquí, login normal contra la base de datos ---
        try {
            $pdo = new PDO(
                'mysql:host=localhost;dbname=complaints_chat;charset=utf8mb4',
                'usuario',
                'contraseña',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $stmt = $pdo->prepare(
                'SELECT U.username, P.passwd, U.is_admin 
                 FROM Users U
                 JOIN Passwords P ON U.id_passwd = P.id
                 WHERE U.username = ?'
            );

            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['passwd'])) {
                return [
                    'username' => $user['username'],
                    'is_admin' => $user['is_admin'],
                    'login'    => true
                ];
            } else {
                return [
                    'login' => false,
                    'error' => 'Usuario o contraseña incorrectos'
                ];
            }
        } catch (PDOException $e) {
            // Para pruebas puedes mostrarlo, pero en producción mejor loguearlo y dar mensaje genérico
            return [
                'login' => false,
                'error' => 'Error al conectar con la base de datos'
            ];
        }
    }

    function main() {
        session_start();
        define("ADMIN_PANEL","Location: admin_panel.php");
        define("CHAT_PANEL", "Location: success_login.html");
        $error = "";

        if (checkPost()) {
            list($username, $password) = getPost();
            $resultado = verifyLogin($username, $password);

            if ($resultado['login']) {
                // Regenerar id de sesión es buena práctica
                session_regenerate_id(true);
                saveSession($resultado['username'], $resultado['is_admin']);

                if ($resultado['is_admin']) {
                    header(ADMIN_PANEL);
                    exit;
                } else {
                    header(CHAT_PANEL);
                    exit;
                }
            } else {
                $error = $resultado['error'] ?? 'Error en el inicio de sesión';
            }
        }

        // devolvemos $error para poder mostrarlo en el HTML si quieres
        return $error;
    }

    $error = main();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesión</title>
    <link rel="stylesheet" href="../css/sign_in.css">
    <link rel="icon" type="image/x-icon" href="../img/logo.png">
</head>
<body>
    <div id="formulario-inicio-sesion">
        <h1>Inicio de sesión</h1>
        <form method="POST" action="">
            <label for="user">Usuario:</label>
            <input type="text" id="user" name="user" required>
            
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Iniciar sesión">
        </form>

        <?php if (!empty($error)) : ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
    </div>
</body>
</html>