# Plan de Trabajo - Sistema de Productos Artesanales

## 📊 Estado Actual del Sistema

### ✅ Lo que YA tienes implementado:
- ✅ Base de datos completa (`bdproduc_artesanales.sql`)
- ✅ Autenticación básica ([`login.php`](login.php:1), [`registro.php`](registro.php:1))
- ✅ Listado de productos ([`productos-artesania-bolivia.php`](productos-artesania-bolivia.php:1))
- ✅ Carrito de compras funcional ([`carrito.php`](carrito.php:1))
- ✅ Sistema de roles (comprador, vendedor, delivery, administrador)
- ✅ Dashboards básicos para cada rol
- ✅ Gestión de pedidos y entregas

### ❌ Lo que NECESITAS implementar/mejorar:

---

## 🎯 REQUISITOS Y ARCHIVOS A TRABAJAR

### 1️⃣ **AUTENTICACIÓN Y REGISTRO DE USUARIO**

#### Archivos a MODIFICAR:
- **[`login.php`](login.php:1)** 
  - ✨ Agregar validación de email antes de login
  - ✨ Implementar "recordar sesión" (remember me)
  - ✨ Mejorar mensajes de error
  - ✨ Agregar captcha para seguridad
  - ✨ Limitar intentos de login fallidos

- **[`registro.php`](registro.php:1)**
  - ✨ Agregar verificación de email (enviar código)
  - ✨ Validación de fortaleza de contraseña
  - ✨ Agregar campos: teléfono, fecha nacimiento, dirección
  - ✨ Términos y condiciones checkbox
  - ✨ Captcha anti-bots

- **[`google_login.php`](google_login.php:1)**
  - ✨ Completar integración con Google OAuth
  - ✨ Crear/vincular cuenta automáticamente
  - ✨ Sincronizar datos de perfil

#### Archivos a CREAR:
- **`recuperar_password.php`** - Formulario para solicitar recuperación
- **`reset_password.php`** - Formulario para establecer nueva contraseña
- **`verificar_email.php`** - Verificar código enviado por email
- **`config/email_config.php`** - Configuración para envío de emails

---

### 2️⃣ **PERFIL DEL USUARIO**

#### Archivos a CREAR:
- **`perfil.php`** - Vista principal del perfil del usuario
  - Mostrar información personal
  - Historial de compras
  - Productos favoritos
  - Direcciones guardadas
  
- **`editar_perfil.php`** - Formulario para editar información
  - Datos personales (nombre, apellido, teléfono)
  - Cambiar contraseña
  - Foto de perfil
  - Preferencias de notificación

- **`mis_compras.php`** - Historial detallado de compras
  - Lista de pedidos
  - Estado de entregas
  - Descargar facturas PDF
  - Tracking de envíos

- **`favoritos.php`** - Gestión de productos favoritos
  - Lista de productos guardados
  - Agregar/eliminar favoritos
  - Notificar cuando hay stock

- **`direcciones.php`** - Gestión de direcciones de entrega
  - Agregar nuevas direcciones
  - Marcar dirección por defecto
  - Editar/eliminar direcciones

#### Archivos PHP backend a CREAR:
- **`api/perfil_handler.php`** - Procesar actualizaciones de perfil
- **`api/favoritos_handler.php`** - Gestionar favoritos (agregar/quitar)
- **`api/direcciones_handler.php`** - CRUD de direcciones

#### Tabla BD adicional necesaria:
```sql
CREATE TABLE favoritos (
  id_favorito INT PRIMARY KEY AUTO_INCREMENT,
  id_usuario INT NOT NULL,
  id_producto INT NOT NULL,
  fecha_agregado DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
  FOREIGN KEY (id_producto) REFERENCES producto(id_producto)
);

CREATE TABLE direcciones_entrega (
  id_direccion INT PRIMARY KEY AUTO_INCREMENT,
  id_usuario INT NOT NULL,
  nombre_direccion VARCHAR(100),
  departamento VARCHAR(50),
  provincia VARCHAR(50),
  ciudad VARCHAR(50),
  zona VARCHAR(100),
  calle VARCHAR(100),
  numero VARCHAR(20),
  referencia TEXT,
  es_predeterminada BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);
```

---

### 3️⃣ **GESTIÓN DE PRODUCTOS**

#### Archivos a MODIFICAR:
- **[`productos-artesania-bolivia.php`](productos-artesania-bolivia.php:1)**
  - ✨ Agregar paginación (mostrar 12-20 productos por página)
  - ✨ Mejorar galería de imágenes (zoom, lightbox)
  - ✨ Agregar botón "favoritos" ❤️
  - ✨ Mostrar calificación promedio (estrellas)
  - ✨ Agregar vista rápida (quick view) sin cambiar de página

- **[`detalles_producto.php`](detalles_producto.php:1)**
  - ✨ Agregar sección de reseñas
  - ✨ Productos relacionados/similares
  - ✨ Zoom en imágenes
  - ✨ Compartir en redes sociales
  - ✨ Preguntas frecuentes del producto

#### Archivos a CREAR:
- **`producto_detalle_completo.php`** - Vista extendida con toda la info
- **`api/productos_api.php`** - API REST para operaciones de productos
  - GET /productos - Lista con filtros
  - GET /producto/{id} - Detalle específico
  - POST /producto/favorito - Agregar a favoritos

---

### 4️⃣ **CARRITO DE COMPRA**

#### Archivos a MODIFICAR:
- **[`carrito.php`](carrito.php:1)** - ✅ Ya funcional, pero mejorar:
  - ✨ Actualizar cantidad desde el carrito
  - ✨ Aplicar cupones de descuento
  - ✨ Calcular impuestos si aplica
  - ✨ Guardar carrito en BD (no solo sesión)
  - ✨ Recuperar carrito si usuario vuelve

- **[`añadir_al_carrito.php`](añadir_al_carrito.php:1)**
  - ✨ Validar stock disponible
  - ✨ Notificación visual de producto agregado
  - ✨ Opción "comprar ahora" (checkout directo)

#### Archivos a CREAR:
- **`actualizar_carrito.php`** - Actualizar cantidades
- **`cupones.php`** - Sistema de cupones de descuento
- **`api/carrito_api.php`** - API para operaciones de carrito
  - Guardar carrito en BD
  - Sincronizar entre sesiones
  - Calcular totales

#### Tabla BD adicional:
```sql
CREATE TABLE carrito_guardado (
  id_carrito INT PRIMARY KEY AUTO_INCREMENT,
  id_usuario INT NOT NULL,
  id_producto INT NOT NULL,
  cantidad INT NOT NULL,
  detalles TEXT,
  fecha_agregado DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
  FOREIGN KEY (id_producto) REFERENCES producto(id_producto)
);

CREATE TABLE cupones (
  id_cupon INT PRIMARY KEY AUTO_INCREMENT,
  codigo VARCHAR(50) UNIQUE NOT NULL,
  tipo_descuento ENUM('porcentaje', 'monto_fijo'),
  valor_descuento DECIMAL(10,2),
  fecha_inicio DATE,
  fecha_fin DATE,
  usos_maximos INT,
  usos_actuales INT DEFAULT 0,
  activo BOOLEAN DEFAULT TRUE
);
```

---

### 5️⃣ **PASARELA DE PAGO**

#### Archivos a MODIFICAR:
- **[`carrito.php`](carrito.php:1)** - Sección de checkout
  - ✨ Integrar pasarela de pago real (no simulada)
  - ✨ Validar datos de tarjeta
  - ✨ Generar QR dinámico para cada compra
  - ✨ Confirmar pago antes de procesar pedido

- **[`procesar_pedido.php`](procesar_pedido.php:1)**
  - ✨ Verificar pago completado
  - ✨ Actualizar stock automáticamente
  - ✨ Generar factura PDF automática
  - ✨ Enviar email de confirmación

- **[`generar_pdf.php`](generar_pdf.php:1)**
  - ✨ Mejorar diseño de factura
  - ✨ Incluir QR de pago
  - ✨ Agregar términos y condiciones

#### Archivos a CREAR:
- **`pagos/pasarela_tarjeta.php`** - Integración con procesador de tarjetas
- **`pagos/pasarela_qr.php`** - Generación de QR para pagos móviles
- **`pagos/verificar_pago.php`** - Webhook para confirmar pagos
- **`pagos/config_pagos.php`** - Configuración de APIs de pago
- **`confirmar_compra.php`** - Página de confirmación post-pago

#### Servicios a integrar:
- **Para Bolivia:**
  - Tigo Money (QR)
  - BCP Bolivia (tarjetas)
  - Simple (pasarela QR)
  - Kushki (pasarela internacional)

---

### 6️⃣ **BÚSQUEDA Y FILTRADO**

#### Archivos a CREAR:
- **`buscar.php`** - Página de resultados de búsqueda
  - Búsqueda por texto (nombre, descripción)
  - Filtros por:
    - Categoría
    - Rango de precio (min-max)
    - Comunidad/artesano
    - Calificación
    - Disponibilidad (con stock)
  - Ordenar por:
    - Relevancia
    - Precio (menor a mayor / mayor a menor)
    - Más recientes
    - Más vendidos
    - Mejor calificados

- **`api/busqueda_api.php`** - API de búsqueda
  - Búsqueda rápida (autocompletar)
  - Filtros combinados
  - Búsqueda con paginación

#### Archivos a MODIFICAR:
- **[`productos-artesania-bolivia.php`](productos-artesania-bolivia.php:1)**
  - ✨ Agregar barra de búsqueda en header
  - ✨ Filtros laterales avanzados
  - ✨ Botones de ordenamiento

#### JavaScript necesario:
- **`js/busqueda.js`** - Autocompletar y filtros dinámicos

---

### 7️⃣ **REDES SOCIALES**

#### Archivos a CREAR:
- **`compartir_producto.php`** - Generar enlaces para compartir
- **`social/facebook_share.php`** - Integración con Facebook
- **`social/whatsapp_share.php`** - Compartir por WhatsApp
- **`social/instagram_link.php`** - Vincular cuenta Instagram

#### Archivos a MODIFICAR:
- **[`detalles_producto.php`](detalles_producto.php:1)**
  - ✨ Botones para compartir en:
    - Facebook
    - WhatsApp
    - Twitter/X
    - Instagram (link)
    - Copiar enlace
  - ✨ Meta tags Open Graph para preview

- Todos los archivos principales:
  - ✨ Agregar en footer:
    - Links a redes sociales
    - Feed de Instagram
    - Botón "Síguenos"

#### Componentes a agregar:
```html
<!-- Meta tags para compartir en redes -->
<meta property="og:title" content="Nombre Producto">
<meta property="og:description" content="Descripción">
<meta property="og:image" content="URL imagen">
<meta property="og:url" content="URL producto">
```

---

### 8️⃣ **SISTEMA DE RESEÑAS** (Adicional, usa tabla `reseña` existente)

#### Archivos a CREAR:
- **`agregar_reseña.php`** - Formulario para dejar reseña
  - Calificación 1-5 estrellas
  - Comentario
  - Foto opcional del producto recibido
  - Solo usuarios que compraron pueden reseñar

- **`api/reseñas_api.php`** - Gestión de reseñas
  - POST /reseña - Crear nueva reseña
  - GET /producto/{id}/reseñas - Obtener reseñas
  - DELETE /reseña/{id} - Eliminar (solo admin o autor)

#### Archivos a MODIFICAR:
- **[`detalles_producto.php`](detalles_producto.php:1)**
  - ✨ Mostrar reseñas existentes
  - ✨ Promedio de calificación
  - ✨ Filtrar reseñas (más útiles, recientes)
  - ✨ Botón "Dejar reseña"

---

## 📋 ORDEN DE TRABAJO RECOMENDADO

### FASE 1: FUNDAMENTOS (Semana 1-2)
```
1. Mejorar Autenticación y Registro
   └─ Modificar: login.php, registro.php
   └─ Crear: recuperar_password.php, reset_password.php, verificar_email.php
   
2. Crear Sistema de Perfil
   └─ Crear: perfil.php, editar_perfil.php, mis_compras.php
   └─ Crear tablas: favoritos, direcciones_entrega
```

### FASE 2: PRODUCTOS Y BÚSQUEDA (Semana 3)
```
3. Mejorar Gestión de Productos
   └─ Modificar: productos-artesania-bolivia.php, detalles_producto.php
   └─ Crear: api/productos_api.php
   
4. Implementar Búsqueda y Filtrado
   └─ Crear: buscar.php, api/busqueda_api.php
   └─ Modificar: Header en todos los archivos
```

### FASE 3: CARRITO Y PAGOS (Semana 4)
```
5. Optimizar Carrito
   └─ Modificar: carrito.php, añadir_al_carrito.php
   └─ Crear: api/carrito_api.php, cupones.php
   └─ Crear tabla: carrito_guardado, cupones
   
6. Integrar Pasarela de Pago
   └─ Modificar: procesar_pedido.php, generar_pdf.php
   └─ Crear: pagos/pasarela_*.php, confirmar_compra.php
```

### FASE 4: ENGAGEMENT (Semana 5)
```
7. Redes Sociales
   └─ Crear: compartir_producto.php, social/*.php
   └─ Modificar: Agregar botones en todos los productos
   
8. Sistema de Reseñas
   └─ Crear: agregar_reseña.php, api/reseñas_api.php
   └─ Modificar: detalles_producto.php
```

---

## 📁 ESTRUCTURA DE ARCHIVOS FINAL

```
htdocs/
├── index.php ✅
├── login.php ✅ (modificar)
├── registro.php ✅ (modificar)
├── recuperar_password.php ⭐ CREAR
├── reset_password.php ⭐ CREAR
├── verificar_email.php ⭐ CREAR
├── perfil.php ⭐ CREAR
├── editar_perfil.php ⭐ CREAR
├── mis_compras.php ⭐ CREAR
├── favoritos.php ⭐ CREAR
├── direcciones.php ⭐ CREAR
├── productos-artesania-bolivia.php ✅ (modificar)
├── detalles_producto.php ✅ (modificar)
├── buscar.php ⭐ CREAR
├── carrito.php ✅ (modificar)
├── añadir_al_carrito.php ✅ (modificar)
├── procesar_pedido.php ✅ (modificar)
├── generar_pdf.php ✅ (modificar)
├── confirmar_compra.php ⭐ CREAR
├── agregar_reseña.php ⭐ CREAR
├── compartir_producto.php ⭐ CREAR
├── db.php ✅
├── logout.php ✅
│
├── api/
│   ├── perfil_handler.php ⭐ CREAR
│   ├── favoritos_handler.php ⭐ CREAR
│   ├── direcciones_handler.php ⭐ CREAR
│   ├── productos_api.php ⭐ CREAR
│   ├── carrito_api.php ⭐ CREAR
│   ├── busqueda_api.php ⭐ CREAR
│   └── reseñas_api.php ⭐ CREAR
│
├── pagos/
│   ├── config_pagos.php ⭐ CREAR
│   ├── pasarela_tarjeta.php ⭐ CREAR
│   ├── pasarela_qr.php ⭐ CREAR
│   └── verificar_pago.php ⭐ CREAR
│
├── social/
│   ├── facebook_share.php ⭐ CREAR
│   ├── whatsapp_share.php ⭐ CREAR
│   └── instagram_link.php ⭐ CREAR
│
├── config/
│   └── email_config.php ⭐ CREAR
│
├── js/
│   └── busqueda.js ⭐ CREAR
│
├── css/ ✅
├── img/ ✅
├── Dashboard/ ✅
└── TCPDF-main/ ✅
```

---

## 🛠️ HERRAMIENTAS Y LIBRERÍAS NECESARIAS

1. **PHPMailer** - Para envío de emails
2. **QR Code Generator** - Ya tienes pero mejorar
3. **Payment Gateway SDK** - Kushki, Simple, etc.
4. **TCPDF** - Ya tienes para PDFs ✅
5. **jQuery/Ajax** - Para interacciones dinámicas
6. **SweetAlert2** - Para notificaciones bonitas
7. **Leaflet** - Para mapas (ya tienes) ✅

---

## 🔒 SEGURIDAD A IMPLEMENTAR

- ✅ Validación de inputs (ya parcial)
- ⭐ Protección CSRF en formularios
- ⭐ Sanitización de datos
- ⭐ Rate limiting para APIs
- ⭐ Encriptación de datos sensibles
- ⭐ Logs de actividad
- ⭐ Sesiones seguras

---

## 📊 TABLAS DE BD ADICIONALES NECESARIAS

```sql
-- Ya tienes todas las tablas principales ✅
-- Solo agregar estas:

CREATE TABLE favoritos (
  id_favorito INT PRIMARY KEY AUTO_INCREMENT,
  id_usuario INT NOT NULL,
  id_producto INT NOT NULL,
  fecha_agregado DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
  FOREIGN KEY (id_producto) REFERENCES producto(id_producto)
);

CREATE TABLE direcciones_entrega (
  id_direccion INT PRIMARY KEY AUTO_INCREMENT,
  id_usuario INT NOT NULL,
  nombre_direccion VARCHAR(100),
  departamento VARCHAR(50),
  provincia VARCHAR(50),
  ciudad VARCHAR(50),
  zona VARCHAR(100),
  calle VARCHAR(100),
  numero VARCHAR(20),
  referencia TEXT,
  es_predeterminada BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);

CREATE TABLE carrito_guardado (
  id_carrito INT PRIMARY KEY AUTO_INCREMENT,
  id_usuario INT NOT NULL,
  id_producto INT NOT NULL,
  cantidad INT NOT NULL,
  detalles TEXT,
  fecha_agregado DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
  FOREIGN KEY (id_producto) REFERENCES producto(id_producto)
);

CREATE TABLE cupones (
  id_cupon INT PRIMARY KEY AUTO_INCREMENT,
  codigo VARCHAR(50) UNIQUE NOT NULL,
  tipo_descuento ENUM('porcentaje', 'monto_fijo'),
  valor_descuento DECIMAL(10,2),
  fecha_inicio DATE,
  fecha_fin DATE,
  usos_maximos INT,
  usos_actuales INT DEFAULT 0,
  activo BOOLEAN DEFAULT TRUE
);
```

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

### Autenticación y Registro
- [ ] Recuperación de contraseña
- [ ] Verificación de email
- [ ] Login con Google completo
- [ ] Captcha anti-bots
- [ ] Remember me

### Perfil de Usuario
- [ ] Vista de perfil
- [ ] Editar información
- [ ] Cambiar contraseña
- [ ] Foto de perfil
- [ ] Historial de compras
- [ ] Gestión de favoritos
- [ ] Gestión de direcciones

### Gestión de Productos
- [ ] Paginación
- [ ] Vista rápida
- [ ] Zoom de imágenes
- [ ] Productos relacionados
- [ ] Favoritos

### Carrito de Compra
- [ ] Actualizar cantidades
- [ ] Cupones de descuento
- [ ] Guardar en BD
- [ ] Recuperar carrito

### Pasarela de Pago
- [ ] Integración con tarjetas
- [ ] QR dinámico
- [ ] Verificación de pago
- [ ] Email de confirmación
- [ ] Factura mejorada

### Búsqueda y Filtrado
- [ ] Búsqueda por texto
- [ ] Filtros avanzados
- [ ] Autocompletar
- [ ] Ordenamiento

### Redes Sociales
- [ ] Botones compartir
- [ ] Meta tags Open Graph
- [ ] Links footer
- [ ] WhatsApp directo

### Sistema de Reseñas
- [ ] Dejar reseña
- [ ] Ver reseñas
- [ ] Calificación promedio
- [ ] Filtros de reseñas

---

## 🎯 PRIORIDADES

### ALTA PRIORIDAD (Implementar primero)
1. ✅ Perfil de usuario completo
2. ✅ Búsqueda y filtrado
3. ✅ Pasarela de pago real
4. ✅ Sistema de reseñas

### MEDIA PRIORIDAD
5. ✅ Favoritos
6. ✅ Cupones de descuento
7. ✅ Redes sociales

### BAJA PRIORIDAD (Mejoras)
8. ✅ Notificaciones push
9. ✅ Chat en vivo
10. ✅ Programa de lealtad

---

## 📞 ¿Por dónde empezar?

**RECOMENDACIÓN:** Comienza con **FASE 1 - Perfil de Usuario**

Esto te permitirá:
- Tener una base sólida de gestión de usuarios
- Probar el sistema de autenticación mejorado
- Preparar el terreno para favoritos y direcciones

**Siguiente paso:** Dime qué fase quieres que te ayude a implementar primero y comenzamos a crear los archivos necesarios.