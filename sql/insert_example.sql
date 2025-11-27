START TRANSACTION;

-- Insert password
INSERT INTO Passwords (passwd)
VALUES ('hashed_password_example');

-- Insert user usando el id de la contraseña recién insertada
INSERT INTO Users (username, id_passwd, is_admin)
VALUES ('anon_user_001', LAST_INSERT_ID(), 0);

-- Si todo salió bien, confirmamos la transacción
COMMIT;
