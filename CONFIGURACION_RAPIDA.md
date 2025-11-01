# ⚡ Configuración Rápida - Solo lo Esencial

## 🔑 Lo que Necesitas Hacer AHORA

Ya configuraste tu email en `config/email_config.php`:
- ✅ Email: `andradefoldine.10@gmail.com`

**Ahora solo necesitas la CONTRASEÑA DE APLICACIÓN:**

---

## 📋 Pasos Muy Rápidos:

1. **Ve a:** https://myaccount.google.com/security

2. **Activa** "Verificación en 2 pasos" (si no está activada)

3. **Busca** "Contraseñas de aplicaciones" (App passwords)

4. **Haz clic** en "Contraseñas de aplicaciones"

5. **Selecciona:**
   - Aplicación: **"Correo"** o **"Otro (Personalizado)"**
   - Dispositivo: **"Otro (Personalizado)"**
   - Nombre: Escribe **"ArtesaníaBoliviana"**

6. **Haz clic** en "Generar"

7. **COPIA** la contraseña que aparece (16 caracteres, tipo: `abcd efgh ijkl mnop`)

8. **Pégala en** `config/email_config.php`:

```php
define('SMTP_PASSWORD', 'abcdefghijklmnop');  // SIN espacios
```

9. **Guarda** el archivo

10. **¡Listo!** Ya puedes enviar correos

---

## 🎯 Ubicación Exacta en Google:

```
Google Account → Seguridad → Acceso a Google → Contraseñas de aplicaciones
```

---

## ⚠️ Importante:

- La contraseña se muestra **UNA SOLA VEZ**
- **QUITA LOS ESPACIOS** cuando la pegues
- Si olvidas copiarla, deberás crear una nueva

---

¿Dónde estás atascado? Consulta `GUIA_PASO_A_PASO_GMAIL.md` para más detalles.


