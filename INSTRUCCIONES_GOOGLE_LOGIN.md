# 📱 Instrucciones para Configurar Google Login

## 🎯 Paso a Paso para Obtener las Credenciales de Google

### 1️⃣ Crear un Proyecto en Google Cloud Console

1. Ve a: **https://console.cloud.google.com/**
2. Inicia sesión con tu cuenta de Google
3. Haz clic en el selector de proyectos (arriba a la izquierda)
4. Clic en **"NUEVO PROYECTO"**
5. Nombre del proyecto: `ArtesaniaBolivia` (o el que prefieras)
6. Haz clic en **"CREAR"**
7. Espera a que se cree el proyecto (unos segundos)

### 2️⃣ Habilitar la API de Google Sign-In

1. En el menú lateral, ve a **"APIs y servicios"** > **"Biblioteca"**
2. Busca: `Google+ API` o `Google Identity`
3. Haz clic en la API y luego en **"HABILITAR"**

### 3️⃣ Configurar la Pantalla de Consentimiento OAuth

1. Ve a **"APIs y servicios"** > **"Pantalla de consentimiento de OAuth"**
2. Selecciona **"Externo"** (para pruebas) o **"Interno"** (si tienes Google Workspace)
3. Haz clic en **"CREAR"**

4. **Completar información:**
   - **Nombre de la aplicación:** `ArtesaníaBolivia`
   - **Correo electrónico de asistencia:** tu-email@gmail.com
   - **Logotipo de la aplicación:** (opcional)
   - **Dominios autorizados:** `localhost` (para desarrollo)
   - **Correo de contacto del desarrollador:** tu-email@gmail.com
   
5. Haz clic en **"GUARDAR Y CONTINUAR"**

6. **Ámbitos (Scopes):** 
   - Haz clic en **"AGREGAR O QUITAR ÁMBITOS"**
   - Selecciona:
     - `email`
     - `profile`
     - `openid`
   - Haz clic en **"ACTUALIZAR"**
   
7. Haz clic en **"GUARDAR Y CONTINUAR"**

8. **Usuarios de prueba** (si elegiste "Externo"):
   - Haz clic en **"+ AGREGAR USUARIOS"**
   - Agrega tu email de prueba
   - Haz clic en **"AGREGAR"**
   
9. Haz clic en **"GUARDAR Y CONTINUAR"**

10. Revisa el resumen y haz clic en **"VOLVER AL PANEL"**

### 4️⃣ Crear Credenciales OAuth 2.0

1. Ve a **"APIs y servicios"** > **"Credenciales"**
2. Haz clic en **"+ CREAR CREDENCIALES"**
3. Selecciona **"ID de cliente de OAuth"**

4. **Configurar el cliente:**
   - **Tipo de aplicación:** `Aplicación web`
   - **Nombre:** `ArtesaniaBolivia Web Client`
   
5. **Orígenes de JavaScript autorizados:**
   ```
   http://localhost
   http://localhost:80
   http://localhost/tu-carpeta-proyecto
   ```
   
6. **URIs de redireccionamiento autorizados:**
   ```
   http://localhost/google_login.php
   http://localhost/tu-carpeta-proyecto/google_login.php
   ```
   ⚠️ **IMPORTANTE:** Reemplaza `tu-carpeta-proyecto` con la ruta real de tu proyecto

7. Haz clic en **"CREAR"**

8. **¡GUARDA TUS CREDENCIALES!**
   - Se mostrará una ventana con:
     - **ID de cliente:** algo como `123456789-abc123.apps.googleusercontent.com`
     - **Secreto del cliente:** (no lo necesitas para login frontend)
   
9. **COPIA EL ID DE CLIENTE** completo

---

## 🔧 Configurar el Proyecto

### 📄 Paso 1: Actualizar [`config/google_config.php`](config/google_config.php:1)

Abre el archivo y reemplaza:

```php
define('GOOGLE_CLIENT_ID', 'TU_CLIENTE_ID_AQUI.apps.googleusercontent.com');
```

Por tu ID de cliente real:

```php
define('GOOGLE_CLIENT_ID', '123456789-abc123def456.apps.googleusercontent.com');
```

### 📄 Paso 2: Actualizar [`index.php`](index.php:338)

Busca la línea 338 (aproximadamente) donde dice:

```javascript
client_id: 'TU_CLIENTE_ID.apps.googleusercontent.com',
```

Y reemplázalo con tu ID de cliente:

```javascript
client_id: '123456789-abc123def456.apps.googleusercontent.com',
```

### 📄 Paso 3: Actualizar otros archivos (opcional)

Si también quieres Google Login en [`carrito.php`](carrito.php:1) y [`productos-artesania-bolivia.php`](productos-artesania-bolivia.php:1), busca la misma línea y actualízala.

---

## ✅ Probar la Integración

### 1. Abrir el proyecto en el navegador:
```
http://localhost/tu-carpeta-proyecto/index.php
```

### 2. Hacer clic en "Iniciar Sesión"

### 3. Hacer clic en el botón de Google

### 4. Seleccionar tu cuenta de Google

### 5. Si todo está bien:
- ✅ Se creará/actualizará el usuario en la BD
- ✅ Se iniciará sesión automáticamente
- ✅ Redirigirá según el rol del usuario

---

## 🐛 Solución de Problemas Comunes

### ❌ Error: "Invalid Client ID"
**Causa:** El CLIENT_ID no es correcto o no coincide
**Solución:** 
- Verifica que copiaste el CLIENT_ID completo (incluye el `.apps.googleusercontent.com`)
- Asegúrate de actualizar TANTO `config/google_config.php` COMO `index.php`

### ❌ Error: "redirect_uri_mismatch"
**Causa:** La URL de redirección no está autorizada
**Solución:**
1. Ve a Google Cloud Console > Credenciales
2. Edita tu cliente OAuth
3. Agrega exactamente la URL que estás usando, ejemplo:
   ```
   http://localhost/htdocs/google_login.php
   ```

### ❌ El botón de Google no aparece
**Causa:** El script de Google no se carga
**Solución:**
- Verifica tu conexión a internet
- Abre la consola del navegador (F12) y busca errores
- Asegúrate que esta línea esté en el `<head>`:
  ```html
  <script src="https://accounts.google.com/gsi/client" async defer></script>
  ```

### ❌ Error: "Token inválido o email no verificado"
**Causa:** La cuenta de Google no tiene el email verificado
**Solución:**
- Usa una cuenta de Google con email verificado
- Verifica tu email en la configuración de Google

### ❌ Error 403: "access_blocked"
**Causa:** El proyecto está en modo "Externo" y el usuario no está en la lista de prueba
**Solución:**
1. Ve a Pantalla de consentimiento OAuth
2. Agrega el usuario a "Usuarios de prueba"
O
3. Publica la aplicación (solo para producción)

---

## 📊 Verificar en la Base de Datos

Después de un login exitoso con Google, verifica que el usuario se creó:

```sql
SELECT * FROM usuario WHERE correo = 'tu-email@gmail.com';
```

Deberías ver:
- ✅ `nombre`: Tu nombre de Google
- ✅ `apellido`: Tu apellido de Google (o "Google User")
- ✅ `correo`: tu-email@gmail.com
- ✅ `fecha_registro`: Fecha actual
- ✅ `estado`: Activo
- ✅ `rol`: comprador

---

## 🔒 Seguridad en Producción

Cuando pases a producción:

1. **Actualizar orígenes autorizados:**
   ```
   https://tu-dominio.com
   https://www.tu-dominio.com
   ```

2. **Actualizar URIs de redirección:**
   ```
   https://tu-dominio.com/google_login.php
   ```

3. **Publicar la aplicación:**
   - Ve a Pantalla de consentimiento OAuth
   - Haz clic en "PUBLICAR APLICACIÓN"
   - Sigue el proceso de verificación de Google

4. **Usar HTTPS:**
   - Google requiere HTTPS en producción
   - Obtén un certificado SSL (Let's Encrypt es gratis)

---

## 📚 Recursos Adicionales

- **Documentación oficial:** https://developers.google.com/identity/gsi/web/guides/overview
- **Google Cloud Console:** https://console.cloud.google.com/
- **Verificar dominios:** https://search.google.com/search-console

---

## 🎉 ¡Listo!

Si seguiste todos los pasos, el login con Google debería funcionar correctamente.

**Archivos modificados:**
- ✅ [`config/google_config.php`](config/google_config.php:1) - Configuración
- ✅ [`google_login.php`](google_login.php:1) - Backend que procesa el login
- ✅ [`index.php`](index.php:320) - Frontend con botón de Google
- ✅ [`carrito.php`](carrito.php:1) - (actualizado)
- ✅ [`productos-artesania-bolivia.php`](productos-artesania-bolivia.php:1) - (actualizado)

**¿Necesitas ayuda?**
Revisa la sección de "Solución de Problemas" o consulta los logs del navegador (F12 > Console).