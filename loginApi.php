<?php
session_start();
$dbHost = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "usuario";

// Conexión a la base de datos
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

// Si hay un error en la conexión, muestra el mensaje y detiene la ejecución del script
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Endpoint para la autenticación de un usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['PATH_INFO'] === '/auth') {
    // Obtiene los datos del formulario
    $correo = $_POST['correo'];
    $password = $_POST['password'];
        
    if ($password == "passwordProvicional") {
        $data = array("error" => '2', 'mensaje' => 'Tienes que cambiar el password', 'correo' => $correo);
        die(json_encode($data));
    }
        
    try{
        // Consulta a la base de datos para verificar si el usuario existe
        // Crear consulta preparada
        $query = "SELECT * FROM user WHERE correo = ? LIMIT 1";
        $stmt = $conn->prepare($query);
    
        // Unir el valor de la variable $correo a la consulta preparada
        $stmt->bind_param("s", $correo);
    
        // Ejecutar la consulta
        $stmt->execute();
    
        // Obtener resultado
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // El usuario existe en la base de datos, continuar con la lógica de la aplicación
            // Obtiene los datos del usuario de la base de datos
            $user = $result->fetch_assoc();
    
            // Verifica si la contraseña es correcta
            if (!password_verify($password, $user['password'])) {
                $data = array("error" => '1', 'mensaje' => 'Contraseña incorrecta');
                // Cerrar la consulta y la conexión a la base de datos
                $stmt->close();
                $conn->close();
                die(json_encode($data));
            }
    
            // Genera un token de autenticación para el usuario
            $token = bin2hex(random_bytes(32));
    
            // Guarda el token en la base de datos
            // Preparar la consulta SQL con un marcador de posición para el valor de ID y el token
            $query = "UPDATE user SET token = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
    
            // Vincular los valores a los marcadores de posición
            mysqli_stmt_bind_param($stmt, "si", $token, $user['id']);
    
            // Ejecutar la consulta preparada
            mysqli_stmt_execute($stmt);
    
            // Regresa el token al cliente
            $data = array("exito" => '1', 'token' => $token);
            // Cerrar la consulta y la conexión a la base de datos
            $stmt->close();
            $conn->close();            
            die(json_encode($data));
        } else {
            // Si no se encuentra al usuario, regresa un error
            $data = array("error" => '1', 'mensaje' => 'Usuario no encontrado');
            die(json_encode($data));
        }
    } catch(mysqli_sql_exception $e) {
        $error = array("error" => '3', "mensaje" => $e->getMessage(), "numero_error" => $e->getCode());
        $stmt->close();
        $conn->close();
        die(json_encode($error));
    }  
}

// Endpoint para verificar si el usuario está autenticado
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['PATH_INFO'] === '/verificar') {    
    // Obtiene el token de autenticación del encabezado de la petición    
    $headers = apache_request_headers();
    $authorization_header = isset($headers['Authorization']) ? $headers['Authorization'] : '';
    
    // Extraer el token del encabezado
    list($token) = sscanf($authorization_header, 'Bearer %s');    
    // $token ahora contiene el valor del token    
    
    try{
        // Consulta a la base de datos para verificar si el token es válido
        // Crear la consulta preparada con un marcador de posición "?"
        $query = "SELECT * FROM user WHERE token = ? LIMIT 1";
    
        // Preparar la consulta
        $stmt = $conn->prepare($query);
    
        // Vincular el valor de la variable $token al marcador de posición "?"
        $stmt->bind_param("s", $token);
    
        // Ejecutar la consulta
        $stmt->execute();
    
        // Obtener los resultados de la consulta
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Obtiene los datos del usuario de la base de datos
            $user = $result->fetch_assoc();
    
            // Regresa un mensaje de prueba al cliente
            $data = array(
                "exito" => '1', 
                "nombre" => $user['nombre'],
                "correo" => $user['correo']
            );
            $stmt->close();
            $conn->close();
            die(json_encode($data));
        } else {
            // Si el token no es válido, regresa un error
            if ($result->num_rows === 0 OR $token == NULL) {
                // http_response_code(401);
                $data = array("error" => '1', 'mensaje' => 'Token inválido');
                $stmt->close();
                $conn->close();
                die(json_encode($data));
            }
        }
    } catch(mysqli_sql_exception $e) {
        $error = array("error" => '2', "mensaje" => $e->getMessage(), "numero_error" => $e->getCode());
        $stmt->close();
        $conn->close();
        die(json_encode($error));
    } 
}

// Endpoint para resetear el password y enviar un correo al usuario 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['PATH_INFO'] === '/resetear') { 
    $correo = strtolower($_POST['correoRecuperar']);

    $validaemail = preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $correo);

    if ($validaemail == 0) {
        $data = array("error" => '3');
        die(json_encode($data));
    }

    if (empty($correo)) {
        $data = array("error" => '2');
        die(json_encode($data));
    }

    // Sanitizar los datos recibidos
    $correo = filter_var($correo, FILTER_SANITIZE_EMAIL);

    // Validar email 
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        // El correo no es válido
        $data = array("error" => '3');
        die(json_encode($data));
    }
    
    try {    
        // preparar la consulta preparada
        $query = "SELECT * FROM user WHERE correo = ?";
        $stmt = $conn->prepare($query);
    
        // asignar los parámetros y ejecutar la consulta
        $stmt->bind_param("s", $correo);
        $stmt->execute();
    
        // obtener los resultados
        $result = $stmt->get_result();

        while ($data = mysqli_fetch_array($result)) {
            // El usuario existe en la base de datos, continuar con la lógica de la aplicación
            $nombre = utf8_encode($data['nombre']);
            $id = $data['id'];
            $password = "passwordProvicional"; 
    
            // Creamos una nueva contraseña y Hasheamos antes de almacenarla en la base de datos
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);
    
            //actualizamos el password en el registro del usurio
            $query = "UPDATE user SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $password_hashed, $id);
            $stmt->execute();
    
            $destino = "gustabin@yahoo.com";
            $asunto = "Solicitud de clave del sistema";
            $cuerpo = "<h2>Hola, un usuario esta recuperando el password en el carrito!</h2>
                Hemos recibido la siguiente información:<br>	
                <b>Usuario: </b> $nombre <br>	
                <b>Correo: </b> $correo<br>	
                <br><br>
                <br>
                El equipo de carrito de compras.<br>
                <img src=https://www.gustabin.com/img/logoEmpresa.png height=50px width=50px />
                <a href=https://www.facebook.com/gustabin2.0>
                <img src=https://www.gustabin.com/img/logoFacebook.jpg alt=Logo Facebook height=50px width=50px></a>
                <h5>Desarrollado por Gustabin<br>
                Copyright © 2021. Todos los derechos reservados. Version 1.0.0 <br></h5>
                ";
    
            $yourWebsite = "gustabin.com";
            $yourEmail = "info@gustabin.com";
            $cabeceras = "From: $yourWebsite <$yourEmail>\n" . "Reply-To: cuentas@gustabin.com" . "\n" . "Content-type: text/html";
    
            mail($destino, $asunto, $cuerpo, $cabeceras);
    
            $destino = $correo;
            $asunto = "Recuperación de password del sistema web";
            $cuerpo = "<h2>Apreciado cliente, </h2> $nombre <br>
                Hemos recuperado los datos solicitados. <br><br>
                Su password es: $password<br>
                Su usuario es: $correo<br><br><br>
                Gracias por confiar en nosotros.
                <br>
                El equipo de carrito de compras.<br>
                <img src=https://www.gustabin.com/img/logoEmpresa.png height=50px width=50px />
                <a href=https://www.facebook.com/gustabin2.0>
                <img src=https://www.gustabin.com/img/logoFacebook.jpg alt=Logo Facebook height=50px width=50px></a>
                <h5>Desarrollado por Gustabin<br>
                Copyright © 2021. Todos los derechos reservados. Version 1.0.0 <br></h5>
                ";
    
            $yourWebsite = "gustabin.com";
            $yourEmail = "info@gustabin.com";
            $cabeceras = "From: $yourWebsite <$yourEmail>\n" . "Reply-To: cuentas@gustabin.com" . "\n" . "Content-type: text/html";
    
            mail($destino, $asunto, $cuerpo, $cabeceras);
            $data = array(
                "exito" => '1',
                "nombre" => $nombre,
                "correo" => $correo
            );
            $stmt->close();
            $conn->close();
            die(json_encode($data));
        } 
    } catch(mysqli_sql_exception $e) {
        $error = array("error" => '4', "mensaje" => $e->getMessage(), "numero_error" => $e->getCode());
        $stmt->close();
        $conn->close();
        die(json_encode($error));
    }        
}

// Endpoint para cambiar el password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['PATH_INFO'] === '/cambiar') {
    $correo = $_POST['emailCambiarPassword'];
    $password = $_POST['passwordCambiarPassword'];
    $retipearPassword = $_POST['retipearCambiarPassword'];

    if ($password != $retipearPassword) {
        $data = array("error" => '6', "mensaje" => 'El campo password y el campo reescribir password no son iguales!');
        die(json_encode($data));
    }

    //Validar con preg_match
    $validaemail = preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $correo);
    $validaPassword = preg_match("#.*^(?=.{8,20})(?=.*[a-z]).*$#", $password);
    $validaRetipearPassword = preg_match("#.*^(?=.{8,20})(?=.*[a-z]).*$#", $retipearPassword);

    if ($validaemail == 0) {
        $data = array("error" => '2', "mensaje" => 'Email invalido!');
        die(json_encode($data));
    }

    if ($validaPassword == 0) {
        $data = array("error" => '3', "mensaje" => 'El password no cumple con las reglas de seguridad.');
        die(json_encode($data));
    }

    if ($validaRetipearPassword == 0) {
        $data = array("error" => '5');
        die(json_encode($data));
    }

    if (empty($correo) or empty($password) or empty($retipearPassword)) {
        $data = array("error" => '4', "mensaje" => 'Debe completar todos los datos!');
        die(json_encode($data));
    }
    
    try {
        // Hasheamos la contraseña antes de almacenarla en la base de datos
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
    
        // Guarda el token en la base de datos
        // Preparar la consulta
        $query = "UPDATE user SET password = ? WHERE correo = ?";
        $stmt = $conn->prepare($query);
        
        // Asignar los parámetros
        $stmt->bind_param("ss", $password_hashed, $correo);
    
        // Ejecutar la consulta preparada
        $stmt->execute();
    
        // Obtener los resultados de la consulta
        $result = $stmt->get_result();
        
        if ($stmt->affected_rows > 0) {
            // La contraseña se actualizó correctamente
            $data = array("exito" => '1', "mensaje" => 'Password cambiado con exito!');
            $stmt->close();
            $conn->close();
            die(json_encode($data));
        } else {
            // Hubo un error al actualizar la contraseña
            $data = array("error" => '1');
            $stmt->close();
            $conn->close();
            die(json_encode($data));
        }
    } catch(mysqli_sql_exception $e) {
        $error = array("error" => '7', "mensaje" => $e->getMessage(), "numero_error" => $e->getCode());
        $stmt->close();
        $conn->close();
        die(json_encode($error));
    }    
}

// Endpoint para crear un usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['PATH_INFO'] === '/') {
    $correo = $_POST['emailIncluir'];
    $nombre = $_POST['nombreIncluir'];
    $password = $_POST['passwordIncluir'];
    $retipearPassword = $_POST['retipearPassword'];

    if ($password != $retipearPassword) {
        $data = array("error" => '6', "mensaje" => 'El campo password y el campo reescribir password no son iguales!');
        die(json_encode($data));
    }

    //Validar con preg_match
    $validaemail = preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $correo);
    $validaPassword = preg_match("#.*^(?=.{8,20})(?=.*[a-z]).*$#", $password);
    $validaRetipearPassword = preg_match("#.*^(?=.{8,20})(?=.*[a-z]).*$#", $retipearPassword);

    if ($validaemail == 0) {
        $data = array("error" => '2', "mensaje" => 'Email invalido!');
        die(json_encode($data));
    }

    if ($validaPassword == 0) {
        $data = array("error" => '3', "mensaje" => 'El password no cumple con las reglas de seguridad.');
        die(json_encode($data));
    }

    if ($validaRetipearPassword == 0) {
        $data = array("error" => '5');
        die(json_encode($data));
    }

    if (empty($correo) or empty($nombre) or empty($password) or empty($retipearPassword)) {
        $data = array("error" => '4', "mensaje" => 'Debe completar todos los datos!');
        die(json_encode($data));
    }

    // Hasheamos la contraseña antes de almacenarla en la base de datos
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    try {
         // Preparar la consulta preparada
        $query = "INSERT INTO `user` (`id`, `nombre`, `correo`, `password`) VALUES (NULL, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        // Asignar los valores a la consulta preparada
        $stmt->bind_param("sss", $nombre, $correo, $password_hashed);
        // Ejecutar la consulta preparada
        $stmt->execute();

        // Obtener los resultados de la consulta
        $result = $stmt->get_result();
        if ($stmt->affected_rows > 0) {
            // El usuario se insertó correctamente
            $data = array("exito" => '1', "mensaje" => 'Usuario registrado con exito!');
            $stmt->close();
            $conn->close();
            die(json_encode($data));
        } else {
            // Hubo un error al insertar el usuario
            $data = array("error" => '1');
            $stmt->close();
            $conn->close();
            die(json_encode($data));
        }        
    } catch(mysqli_sql_exception $e) {
      
        $error = array("error" => '7', "mensaje" => $e->getMessage(), "numero_error" => $e->getCode());
        $stmt->close();
        $conn->close();
        die(json_encode($error));
    }    
}
