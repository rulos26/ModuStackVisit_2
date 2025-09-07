# 📋 FLUJOS DE TRABAJO - SISTEMA DE VISITAS DOMICILIARIAS

## 📌 ÍNDICE
1. [Carta de Autorización](#carta-de-autorización)
2. [Evaluación de Visita Domiciliaria](#evaluación-de-visita-domiciliaria)
3. [Información Personal](#información-personal)
4. [Cámara de Comercio](#cámara-de-comercio)
5. [Salud](#salud)
6. [Composición Familiar](#composición-familiar)

---

## 🏠 CARTA DE AUTORIZACIÓN

### **1. VISTA INICIAL**
- **Archivo**: `resources/views/evaluador/carta_visita/carta_autorizacion/carta_autorizacion.php`
- **Acción**: Formulario POST con datos del usuario

### **2. CONTROLADOR PRINCIPAL**
- **Controlador**: `App\Controllers\CartaAutorizacionController`
- **Método**: `guardarAutorizacion()`
- **Parámetros**: cedula, nombres, direccion, localidad, barrio, telefono, celular, fecha, autorizacion, correo

### **3. BASE DE DATOS**
- **Tabla Principal**: `autorizaciones`
- **Tabla Secundaria**: `evaluados` (se crea registro automáticamente)

### **4. FLUJO COMPLETO**
```
1. Usuario envía formulario de carta de autorización
   ↓
2. Se guarda en tabla 'autorizaciones'
   ↓
3. Si es exitoso, se llama a guardarEnEvaluados()
   ↓
4. Se verifica si la cédula ya existe en 'evaluados'
   ↓
5. Si NO existe, se inserta con todos los campos mapeados:
   - nombres (autorizaciones) → nombres (evaluados)
   - direccion (autorizaciones) → direccion (evaluados)
   - localidad (autorizaciones) → localidad (evaluados)
   - barrio (autorizaciones) → barrio (evaluados)
   - telefono (autorizaciones) → telefono (evaluados)
   - celular (autorizaciones) → celular_1 (evaluados)
   - correo (autorizaciones) → correo (evaluados)
   ↓
6. Si ya existe, no se hace nada (respeta el constraint UNIQUE)
```

### **5. REDIRECCIONES**
- **✅ Éxito**: Continúa con el proceso de evaluación
- **❌ Error**: Muestra mensaje de error en la misma vista

---

## 🏠 EVALUACIÓN DE VISITA DOMICILIARIA

### **1. VISTA INICIAL**
- **Archivo**: `resources/views/evaluador/evaluacion_visita/visita/index.php`
- **Acción**: Formulario POST con número de cédula (`id_cedula`)

### **2. PROCESAMIENTO INICIAL**
- **Archivo**: `resources/views/evaluador/evaluacion_visita/visita/session.php`
- **Función**: Almacena la cédula en `$_SESSION['id_cedula']`
- **Redirección**: A `informacion_personal/informacion_personal.php`

### **3. FLUJO COMPLETO**
```
1. Usuario ingresa cédula en index.php
   ↓
2. session.php guarda cédula en sesión
   ↓
3. Redirige a informacion_personal.php
   ↓
4. Usuario llena formulario de información personal
   ↓
5. POST → InformacionPersonalController::guardar()
   ↓
6. Se valida y sanitiza la información
   ↓
7. Se verifica si existe registro en tabla 'evaluados'
   ↓
8a. Si NO existe: INSERT en tabla 'evaluados'
8b. Si existe: UPDATE en tabla 'evaluados'
   ↓
9. Redirección exitosa: ../camara_comercio/camara_comercio.php
   ↓
10. Continúa el flujo de evaluación...
```

---

## 👤 INFORMACIÓN PERSONAL

### **1. VISTA INICIAL**
- **Archivo**: `resources/views/evaluador/evaluacion_visita/visita/informacion_personal/informacion_personal.php`
- **Acción**: Formulario POST con datos personales completos

### **2. CONTROLADOR PRINCIPAL**
- **Controlador**: `App\Controllers\InformacionPersonalController`
- **Método**: `guardar()`
- **Parámetros**: Todos los datos del formulario de información personal

### **3. BASE DE DATOS**
- **Tabla**: `evaluados`
- **Operaciones**: `INSERT` (nuevo) o `UPDATE` (existente)

### **4. CAMPOS QUE SE GUARDAN**
```sql
INSERT INTO evaluados (
    id_cedula, id_tipo_documentos, cedula_expedida, nombres, apellidos,
    edad, fecha_expedicion, lugar_nacimiento, celular_1, celular_2,
    telefono, id_rh, id_estatura, peso_kg, id_estado_civil, hacer_cuanto,
    numero_hijos, direccion, id_ciudad, localidad, barrio, id_estrato,
    correo, cargo, observacion
) VALUES (...)
```

### **5. VALIDACIONES**
- Cédula numérica y obligatoria
- Nombres/apellidos solo letras
- Edad entre 18-120 años
- Celular 1: 10 dígitos obligatorio
- Celular 2: 10 dígitos opcional
- Teléfono: 7 dígitos opcional
- Correo válido
- Dirección obligatoria
- Todos los campos de selección obligatorios

### **6. REDIRECCIONES**
- **✅ Éxito**: `../camara_comercio/camara_comercio.php`
- **❌ Error**: Muestra mensaje de error en la misma vista
- **⬅️ Anterior**: `../index.php`

---

## 🏢 CÁMARA DE COMERCIO

### **1. VISTA INICIAL**
- **Archivo**: `resources/views/evaluador/evaluacion_visita/visita/camara_comercio/camara_comercio.php`
- **Acción**: Formulario POST con datos de cámara de comercio

### **2. CONTROLADOR PRINCIPAL**
- **Controlador**: `App\Controllers\CamaraComercioController`
- **Método**: `guardar()`
- **Parámetros**: `tiene_camara`, `nombre`, `razon`, `activdad`, `observacion`

### **3. BASE DE DATOS**
- **Tabla**: `camara_comercio`
- **Campos**: `id_cedula`, `tiene_camara`, `nombre`, `razon`, `activdad`, `observacion`

### **4. ESTRUCTURA DE DATOS**
```sql
CREATE TABLE camara_comercio (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_cedula VARCHAR(20) NOT NULL,
    tiene_camara ENUM('Si', 'No') NOT NULL,
    nombre VARCHAR(200),
    razon VARCHAR(200),
    activdad VARCHAR(200),
    observacion TEXT
);
```

### **5. VALIDACIONES**
- **Campo obligatorio**: `tiene_camara` (Si/No)
- **Campos opcionales** (si tiene_camara = 'Si'):
  - `nombre`: mínimo 2 caracteres
  - `razon`: mínimo 2 caracteres
  - `activdad`: mínimo 2 caracteres
  - `observacion`: máximo 1000 caracteres

### **6. FUNCIONALIDADES ESPECIALES**
- **JavaScript dinámico**: Muestra/oculta campos según selección
- **Indicador de pasos**: Muestra progreso del proceso
- **Validación condicional**: Campos adicionales solo si es necesario

### **7. REDIRECCIONES**
- **✅ Éxito**: `../salud/salud.php`
- **❌ Error**: Muestra mensaje de error en la misma vista
- **⬅️ Anterior**: `../informacion_personal/informacion_personal.php`

---

## 🏥 SALUD

### **1. VISTA INICIAL**
- **Archivo**: `resources/views/evaluador/evaluacion_visita/visita/salud/salud.php`
- **Acción**: Formulario POST con datos de salud

### **2. CONTROLADOR PRINCIPAL**
- **Controlador**: `App\Controllers\SaludController`
- **Método**: `guardar()`
- **Parámetros**: `id_estado_salud`, `tipo_enfermedad`, `tipo_enfermedad_cual`, `limitacion_fisica`, `limitacion_fisica_cual`, `tipo_medicamento`, `tipo_medicamento_cual`, `ingiere_alcohol`, `ingiere_alcohol_cual`, `fuma`, `observacion`

### **3. BASE DE DATOS**
- **Tabla**: `estados_salud`
- **Campos**: `id_cedula`, `id_estado_salud`, `tipo_enfermedad`, `tipo_enfermedad_cual`, `limitacion_fisica`, `limitacion_fisica_cual`, `tipo_medicamento`, `tipo_medicamento_cual`, `ingiere_alcohol`, `ingiere_alcohol_cual`, `fuma`, `observacion`

### **4. FLUJO COMPLETO**
```
1. Usuario llega desde camara_comercio.php
   ↓
2. Se carga la vista salud.php
   ↓
3. Usuario llena formulario de información de salud
   ↓
4. Usuario envía formulario (POST)
   ↓
5. POST → SaludController::guardar()
   ↓
6. Se valida y sanitiza la información
   ↓
7. Se verifica si existe registro en tabla 'estados_salud'
   ↓
8a. Si NO existe: INSERT en tabla 'estados_salud'
8b. Si existe: UPDATE en tabla 'estados_salud'
   ↓
9. Redirección exitosa: ../composición_familiar/composición_familiar.php
```

### **5. VALIDACIONES**
- **Campos obligatorios**:
  - `id_estado_salud`: Estado de salud
  - `tipo_enfermedad`: Si padece enfermedad
  - `limitacion_fisica`: Si tiene limitaciones físicas
  - `tipo_medicamento`: Si toma medicamentos
  - `ingiere_alcohol`: Si ingiere alcohol
  - `fuma`: Si fuma

- **Validaciones condicionales**:
  - Si padece enfermedad (valor 2): `tipo_enfermedad_cual` obligatorio
  - Si tiene limitaciones (valor 2): `limitacion_fisica_cual` obligatorio
  - Si toma medicamentos (valor 2): `tipo_medicamento_cual` obligatorio
  - Si ingiere alcohol (valor 2): `ingiere_alcohol_cual` obligatorio

### **6. OPCIONES DE SELECCIÓN**
- **Estados de salud**: Desde tabla `opc_estados`
- **Parámetros**: Desde tabla `opc_parametro` (Sí/No)

### **7. REDIRECCIONES**
- **✅ Éxito**: `../composición_familiar/composición_familiar.php`
- **❌ Error**: Muestra mensaje de error en la misma vista
- **⬅️ Anterior**: `../camara_comercio/camara_comercio.php`

---

## 👨‍👩‍👧‍👦 COMPOSICIÓN FAMILIAR

### **1. VISTA INICIAL**
- **Archivo**: `resources/views/evaluador/evaluacion_visita/visita/composición_familiar/composición_familiar.php`
- **Acción**: Formulario POST con datos de múltiples miembros familiares

### **2. CONTROLADOR PRINCIPAL**
- **Controlador**: `App\Controllers\ComposicionFamiliarController`
- **Método**: `guardar()`
- **Parámetros**: Arrays de `nombre`, `id_parentesco`, `edad`, `id_ocupacion`, `telefono`, `id_conviven`, `observacion`

### **3. BASE DE DATOS**
- **Tabla**: `composicion_familiar`
- **Operaciones**: `DELETE` (elimina existentes) + `INSERT` (nuevos registros)

### **4. ESTRUCTURA DE DATOS**
```sql
CREATE TABLE composicion_familiar (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_cedula VARCHAR(20) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    id_parentesco INT NOT NULL,
    edad INT NOT NULL,
    id_ocupacion INT,
    telefono VARCHAR(10) NOT NULL,
    id_conviven INT NOT NULL,
    observacion TEXT
);
```

### **5. FLUJO COMPLETO**
```
1. Usuario llega desde salud.php
   ↓
2. Se carga la vista composición_familiar.php
   ↓
3. Usuario puede agregar múltiples miembros familiares
   ↓
4. Usuario envía formulario (POST)
   ↓
5. POST → ComposicionFamiliarController::guardar()
   ↓
6. Se valida y sanitiza la información de cada miembro
   ↓
7. Se eliminan registros existentes para la cédula
   ↓
8. Se insertan todos los nuevos registros de familia
   ↓
9. Redirección exitosa: ../informacion_pareja/tiene_pareja.php
```

### **6. VALIDACIONES**
- **Campos obligatorios por miembro**:
  - `nombre`: mínimo 2 caracteres, máximo 100
  - `id_parentesco`: debe seleccionar parentesco
  - `edad`: entre 0 y 120 años
  - `telefono`: entre 7 y 10 dígitos numéricos
  - `id_conviven`: debe seleccionar si convive

- **Campos opcionales**:
  - `id_ocupacion`: ocupación (opcional)
  - `observacion`: máximo 500 caracteres

- **Validación general**:
  - Debe agregar al menos un miembro de la familia
  - Validación individual para cada miembro agregado

### **7. FUNCIONALIDADES ESPECIALES**
- **JavaScript dinámico**: Agregar/eliminar miembros familiares
- **Validación en tiempo real**: Por cada campo de cada miembro
- **Manejo de arrays**: Procesamiento de múltiples registros
- **Eliminación y reinserción**: Reemplaza datos existentes completamente

### **8. OPCIONES DE SELECCIÓN**
- **Parentescos**: Desde tabla `opc_parentesco`
- **Ocupaciones**: Desde tabla `opc_ocupacion`
- **Parámetros**: Desde tabla `opc_parametro` (Sí/No para conviven)

### **9. REDIRECCIONES**
- **✅ Éxito**: `../informacion_pareja/tiene_pareja.php`
- **❌ Error**: Muestra mensaje de error en la misma vista
- **⬅️ Anterior**: `../salud/salud.php`

---

## 🔄 FLUJO GENERAL DEL SISTEMA

### **SECUENCIA COMPLETA**
```
1. Carta de Autorización
   ↓ (crea registro en evaluados)
2. Evaluación Visita Domiciliaria (index.php)
   ↓ (guarda cédula en sesión)
3. Información Personal
   ↓ (INSERT/UPDATE en evaluados)
4. Cámara de Comercio
   ↓ (INSERT/UPDATE en camara_comercio)
5. Salud
   ↓ (INSERT/UPDATE en estados_salud)
6. Composición Familiar
   ↓ (DELETE + INSERT en composicion_familiar)
7. Información de Pareja
   ↓ (continúa el proceso...)
8. [Otros módulos...]
9. Generación de Informe Final
```

### **CARACTERÍSTICAS COMUNES**
- **Sesión**: Todas las vistas verifican `$_SESSION['id_cedula']`
- **Validación**: Sanitización y validación de datos en todos los controladores
- **Manejo de errores**: Try-catch en todas las operaciones
- **Redirecciones**: Flujo secuencial entre módulos
- **Indicadores de progreso**: Steps horizontales en todas las vistas
- **Responsive**: Diseño adaptativo con Bootstrap

### **TABLAS PRINCIPALES**
- `autorizaciones`: Datos de autorización inicial
- `evaluados`: Información personal del evaluado
- `camara_comercio`: Información empresarial
- `estados_salud`: Información de salud
- `composicion_familiar`: Información de miembros familiares
- `opc_*`: Tablas de opciones para select boxes

---

## 📝 NOTAS TÉCNICAS

### **ARQUITECTURA**
- **Patrón MVC**: Separación clara entre vistas, controladores y modelos
- **Singleton**: Controladores implementan patrón Singleton
- **PDO**: Uso de PDO para operaciones de base de datos
- **Sessions**: Manejo de sesiones para flujo de datos

### **SEGURIDAD**
- **Sanitización**: `trim(strip_tags())` en todos los inputs
- **Validación**: Validación server-side en todos los formularios
- **Prepared Statements**: Uso de prepared statements para prevenir SQL injection
- **Session Management**: Verificación de sesión en todas las vistas

### **UX/UI**
- **Progreso visual**: Indicadores de pasos en todas las vistas
- **Validación en tiempo real**: JavaScript para validación client-side
- **Mensajes de feedback**: Alertas de éxito y error
- **Navegación intuitiva**: Botones anterior/siguiente en todas las vistas

---

*Documento generado automáticamente - Sistema de Visitas Domiciliarias v2.0*
