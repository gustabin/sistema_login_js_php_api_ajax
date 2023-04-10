<section class="content" id="login">
   <div class="card">
     <div class="card-body login-card-body">
       <p class="login-box-msg">Ingresa los datos para iniciar tu sesión</p>
       <form class="form-horizontal" id="formDefault">
         <div class="input-group mb-3">
           <input type="email" class="form-control redondeado" placeholder="Email" name="correo" id="correo" required="" value="">
           <div class="input-group-append">
             <div class="input-group-text">
               <span class="fas fa-envelope"></span>
             </div>
           </div>
         </div>
         <div class="input-group mb-3">
           <input type="password" class="form-control redondeado" placeholder="Password" name="password" id="password" value="" required="">
           <div class="input-group-append">
             <div class="input-group-text">
               <span class="fas fa-lock"></span>
             </div>
           </div>
         </div>
      
         <div class="row" style="padding-top: 10px">
           <div class="col-6">
             <a href="#" onclick="MostrarRecuperar()">Olvide mi password</a>
           </div>          
           <div class="col-6">
             <button type="submit" class="btn btn-primary btn-block" onclick="BuscarUsuario()">Login</button>
           </div>
         </div>
         
         <div class="row">          
           <div class="col-6">
             <a href="#" onclick="MostrarIncluirUsuario()">Crear cuenta</a>
           </div>           
         </div>
       </form>
     </div>
   </div>
</section>

<section class="content" id="recuperar" style="display: none;">
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Formulario para resetear el password</p>
        <div style="display: flex; justify-content: center;">
          <span style="width: 200px !important; display:none" id="barra">
              <img style="width: 206px" src="img/barra.gif" alt="Procesando..." />
          </span>
          <div id="alerta" class="alert alert-success alert-dismissible fade show" role="alert" style="display:none" >
            <strong>¡Éxito!</strong> Le hemos enviado un email con las instrucciones para resetear su password.        
          </div> 
        </div> 

       <div class="list-group-item">
         Indique su usuario (correo electrónico):
       </div>
       <div class="list-group-item">
         <h4 class="list-group-item-heading">
           <form class="form-horizontal" id="formRecuperar">
             <div class="input-group mb-3">
               <input type="email" class="form-control redondeado" placeholder="Correo" name="correoRecuperar" id="correoRecuperar" required="" value="">
               <div class="input-group-append">
                 <div class="input-group-text">
                   <span class="fas fa-envelope"></span>
                 </div>
               </div>
             </div>
             <div class="form-group">
               <div class="col-md-12 control">
                 <div style="border-top: 1px solid#888; padding-top: 15px; font-size: 14px; align: center">
                   ¿Tiene una cuenta?
                   <a href="#" onclick="MostrarLogin()" style="color:blue">Login</a>
                 </div>
               </div>
             </div>
             <div class="col-12">
               <button type="submit" class="btn btn-primary btn-block" onclick="ResetearPassword()">Resetear password</button>
             </div>
           </form>
         </h4>
       </div>
     </div>
   </div>
</section>

<section class="content" id="cambiarPassword" style="display:none">
   <div class="card">
     <div class="card-body login-card-body">
       <p class="login-box-msg">Ingrese un nuevo password</p>
       <form class="form-horizontal" id="formCambiarPassword">
         <input type="hidden" class="form-control redondeado" placeholder="Email" name="emailCambiarPassword" id="emailCambiarPassword" required="" value="">
         <div class="input-group mb-3">
           <input type="password" class="form-control redondeado" placeholder="Password" name="passwordCambiarPassword" id="passwordCambiarPassword" value="" required="">
           <div class="input-group-append">
             <div class="input-group-text">
               <span class="fas fa-lock"></span>
             </div>
           </div>
         </div>
         <div class="input-group mb-3">
           <input type="password" class="form-control redondeado" placeholder="Password" name="retipearCambiarPassword" id="retipearCambiarPassword" value="" required="">
           <div class="input-group-append">
             <div class="input-group-text">
               <i class="fas fa-user-lock"></i>
             </div>
           </div>
         </div>        
         <div class="row" style="padding-top: 10px">
           <div class="col-12">
             <button type="submit" class="btn btn-primary btn-block" onclick="CambiarPassword()">Cambiar Password</button>
           </div>
         </div>
       </form>
       <div class="form-group">
          <div class="col-md-12 control">
            <div style="border-top: 1px solid#888; padding-top: 15px; font-size: 14px; align:center">
              ¿Tiene una cuenta?
              <a href="#" onclick="MostrarLogin()" style="color:blue">Login</a>
            </div>
          </div>
       </div>
     </div>
   </div>
</section>

<section class="content" id="incluirUsuario" style="display:none">
   <div class="card">
     <div class="card-body login-card-body">
       <p class="login-box-msg">Incluir los datos para continuar</p>
       <form class="form-horizontal" id="formIncluirUsuario">
         <div class="input-group mb-3">
           <input type="email" class="form-control redondeado" placeholder="Email" name="emailIncluir" id="emailIncluir" required="" value="">
           <div class="input-group-append">
             <div class="input-group-text">
               <span class="fas fa-envelope"></span>
             </div>
           </div>
         </div>
         <div class="input-group mb-3">
           <input type="text" class="form-control redondeado" placeholder="Nombre" name="nombreIncluir" id="nombreIncluir" value="" required="">
           <div class="input-group-append">
             <div class="input-group-text">
               <i class="fas fa-chalkboard-teacher"></i>
             </div>
           </div>
         </div>
         <div class="input-group mb-3">
           <input type="password" class="form-control redondeado" placeholder="Password" name="passwordIncluir" id="passwordIncluir" value="" required="">
           <div class="input-group-append">
             <div class="input-group-text">
               <span class="fas fa-lock"></span>
             </div>
           </div>
         </div>
         <div class="input-group mb-3">
           <input type="password" class="form-control redondeado" placeholder="Password" name="retipearPassword" id="retipearPassword" value="" required="">
           <div class="input-group-append">
             <div class="input-group-text">
               <i class="fas fa-user-lock"></i>
             </div>
           </div>
         </div>        
         <div class="row" style="padding-top: 10px">
           <div class="col-12">
             <button type="submit" class="btn btn-primary btn-block" onclick="IncluirUsuario()">Guardar</button>
           </div>
         </div>
       </form>
       <div class="form-group">
          <div class="col-md-12 control">
            <div style="border-top: 1px solid#888; padding-top: 15px; font-size: 14px; align="center">
              ¿Tiene una cuenta?
              <a href="#" onclick="MostrarLogin()" style="color:blue">Login</a>
            </div>
          </div>
       </div>
     </div>
   </div>
 </section>