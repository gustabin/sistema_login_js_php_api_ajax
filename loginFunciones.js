localStorage.removeItem('bandera');

function MostrarRecuperar() {
  $('#recuperar').show();
  $('#login').hide();
  $('#incluirUsuario').hide();
  $('#cambiarPassword').hide();
}

function MostrarLogin() {
  $('#recuperar').hide();
  $('#login').show();
  $('#incluirUsuario').hide();
  $('#cambiarPassword').hide();
}

function MostrarIncluirUsuario() {
  $('#recuperar').hide();
  $('#login').hide();
  $('#incluirUsuario').show();
  $('#cambiarPassword').hide();
}

function MostrarCambiarPassword() {
  $('#recuperar').hide();
  $('#login').hide();
  $('#incluirUsuario').hide();
  $('#cambiarPassword').show();
}

function RemoverToken() {
  sessionStorage.removeItem('token');
  window.location.href = 'tools/logout.php';
}

// url: 'loginApi.php/auth',  BuscarUsuario POST
// url: 'loginApi.php/verificar', VerificarToken GET
// url: 'loginApi.php/resetear', ResetearPassword POST
// url: 'loginApi.php/cambiar', CambiarPassword POST
// url: 'loginApi.php/', IncluirUsuario POST

function BuscarUsuario() {
  if (localStorage.getItem('bandera') === null) {
    localStorage.setItem('bandera', 'entro');
    $('body').on('submit', '#formDefault', function (event) {
      event.preventDefault();
      if ($('#formDefault').valid()) {
        $.ajax({
          type: 'POST',
          url: 'loginApi.php/auth',
          dataType: 'json',
          data: $(this).serialize(),
          success: function (respuesta) {
            console.log(respuesta);
            localStorage.removeItem('bandera');
            if (respuesta.exito == 1) {
              sessionStorage.setItem('token', respuesta.token);
              window.location.href = 'tienda.php';
            }
            if (respuesta.error == 1) {
              swal(
                'Houston, tenemos un problema',
                'Este usuario no existe o el password es invalido',
                'error',
              );
            }
            if (respuesta.error == 2) {
              swal(
                'Houston, tenemos un problema',
                'Tienes que cambiar el password',
                'error',
              );
              // Asignar el valor al campo emailCambiarPassword
              document.getElementById('emailCambiarPassword').value =
                respuesta.correo;
              MostrarCambiarPassword();
            }
            if (respuesta.error == 3) {
              swal(
                'Houston, tenemos un problema',
                respuesta.mensaje + ' ' + respuesta.numero_error,
                'error',
              );
            }
          },
        });
      }
    });
  }
}

function VerificarToken() {
  if (localStorage.getItem('bandera') === null) {
    localStorage.setItem('bandera', 'entro');
    const token = sessionStorage.getItem('token');
    $.ajax({
      type: 'GET',
      url: 'loginApi.php/verificar',
      dataType: 'json',
      data: $(this).serialize(),
      headers: {
        Authorization: 'Bearer ' + token,
      },
      success: function (respuesta) {
        console.log(respuesta);
        if (respuesta.exito == 1) {
          swal('Bienvenido', respuesta.nombre, 'success');
        }
        document.getElementById('nombre').textContent = respuesta.nombre;
        document.getElementById('correo').textContent = respuesta.correo;
        document.getElementById('login_logout').innerHTML =
          '<a href="#" onclick="RemoverToken()">Logout</a>';
        if (respuesta.error == 1) {
          swal(
            'Houston, tenemos un problema',
            'Debes realizar el login para poder ingresar',
            'error',
          );

          setTimeout(function () {
            window.location.href = 'index.php';
          }, 4000);
        }
        if (respuesta.error == 2) {
          swal(
            'Houston, tenemos un problema',
            respuesta.mensaje + ' ' + respuesta.numero_error,
            'error',
          );
        }
      },
    });
  }
}

function ResetearPassword() {
  if (localStorage.getItem('bandera') === null) {
    localStorage.setItem('bandera', 'entro');
    $('body').on('submit', '#formRecuperar', function (event) {
      event.preventDefault();
      if ($('#formRecuperar').valid()) {
        $('#barra').show();
        $.ajax({
          type: 'POST',
          url: 'loginApi.php/resetear',
          dataType: 'json',
          data: $(this).serialize(),
          success: function (respuesta) {
            $('#barra').hide();
            console.log(respuesta);
            if (respuesta.error == 1) {
              swal(
                'Houston, tenemos un problema',
                'Este usuario no existe',
                'error',
              );
            }
            if (respuesta.error == 2) {
              swal(
                'Houston, tenemos un problema',
                'Debe colocar un correo',
                'error',
              );
            }
            if (respuesta.error == 3) {
              swal(
                'Houston, tenemos un problema',
                'Debe colocar un correo válido',
                'error',
              );
            }
            if (respuesta.error == 4) {
              swal(
                'Houston, tenemos un problema',
                respuesta.mensaje + ' ' + respuesta.numero_error,
                'error',
              );
            }
            if (respuesta.exito == 1) {
              swal(
                'Mensaje enviado satisfactoriamente',
                'Todo bien',
                'success',
              );
              document.getElementById('correoRecuperar').value = '';
              $('#alerta').show();
              setTimeout(function () {
                document.getElementById('alerta').remove();
              }, 4000);
            }
          },
        });
      }
    });
  }
}

function CambiarPassword() {
  if (localStorage.getItem('bandera') === null) {
    localStorage.setItem('bandera', 'entro');
    $('body').on('submit', '#formCambiarPassword', function (event) {
      event.preventDefault();
      if ($('#formCambiarPassword').valid()) {
        $('#barra').show();
        $.ajax({
          type: 'POST',
          url: 'loginApi.php/cambiar',
          dataType: 'json',
          data: $(this).serialize(),
          success: function (respuesta) {
            $('#barra').hide();
            if (respuesta.error == 1) {
              swal(
                'Houston, tenemos un problema',
                'Ocurrió un error, contácte al administrador del sistema!',
                'error',
              );
            }
            if (respuesta.error == 2) {
              swal('Houston, tenemos un problema', 'Email invalido!', 'error');
            }
            if (respuesta.error == 3) {
              swal(
                'Houston, tenemos un problema',
                `El password no cumple con las reglas de seguridad. 
                  No pudo guardarse. 
  
                  Debe poseer: 
                  -Debe tener una longitud entre 8 y 20 caracteres! 
                  -Debe tener letras! 
  
                  Password invalido!`,
                'error',
              );
            }
            if (respuesta.error == 4) {
              swal(
                'Houston, tenemos un problema',
                'Debe completar todos los datos!',
                'error',
              );
            }
            if (respuesta.error == 5) {
              swal(
                'Houston, tenemos un problema',
                'Debe completar el campo reescribir password!',
                'error',
              );
            }
            if (respuesta.error == 6) {
              swal(
                'Houston, tenemos un problema',
                'El campo password y el campo reescribir password no son iguales!',
                'error',
              );
            }
            if (respuesta.error == 7) {
              swal(
                'Houston, tenemos un problema',
                respuesta.mensaje + ' ' + respuesta.numero_error,
                'error',
              );
            }
            if (respuesta.exito == 1) {
              swal('Password cambiado con exito!', 'Todo bien', 'success');
              setTimeout(function () {
                window.location.href = 'index.php';
              }, 2000);
            }
          },
        });
      }
    });
  }
}

function IncluirUsuario() {
  if (localStorage.getItem('bandera') === null) {
    localStorage.setItem('bandera', 'entro');
    $('body').on('submit', '#formIncluirUsuario', function (event) {
      event.preventDefault();
      if ($('#formIncluirUsuario').valid()) {
        $('#barra').show();
        $.ajax({
          type: 'POST',
          url: 'loginApi.php/',
          dataType: 'json',
          data: $(this).serialize(),
          success: function (respuesta) {
            $('#barra').hide();
            if (respuesta.error == 1) {
              swal(
                'Houston, tenemos un problema',
                'El email de la cuenta ya existe!',
                'error',
              );
            }
            if (respuesta.error == 2) {
              swal('Houston, tenemos un problema', 'Email invalido!', 'error');
            }
            if (respuesta.error == 3) {
              swal(
                'Houston, tenemos un problema',
                `El password no cumple con las reglas de seguridad. 
                  No pudo guardarse. 
  
                  Debe poseer: 
                  -Debe tener una longitud entre 8 y 20 caracteres! 
                  -Debe tener letras! 
  
                  Password invalido!`,
                'error',
              );
            }
            if (respuesta.error == 4) {
              swal(
                'Houston, tenemos un problema',
                'Debe completar todos los datos!',
                'error',
              );
            }
            if (respuesta.error == 5) {
              swal(
                'Houston, tenemos un problema',
                'Debe completar el campo reescribir password!',
                'error',
              );
            }
            if (respuesta.error == 6) {
              swal(
                'Houston, tenemos un problema',
                'El campo password y el campo reescribir password no son iguales!',
                'error',
              );
            }
            if (respuesta.error == 7) {
              swal(
                'Houston, tenemos un problema',
                respuesta.mensaje + ' ' + respuesta.numero_error,
                'error',
              );
            }
            if (respuesta.exito == 1) {
              swal('Usuario creado satisfactoriamente', 'Todo bien', 'success');
              setTimeout(function () {
                window.location.href = 'index.php';
              }, 4000);
            }
          },
        });
      }
    });
  }
}
