# 📋 FLUJOS DE TRABAJO - SISTEMA DE VISITAS DOMICILIARIAS

## 📌 ÍNDICE
1. [Carta de Autorización](#carta-de-autorización)
2. [Evaluación de Visita Domiciliaria](#evaluación-de-visita-domiciliaria)
3. [Información Personal](#información-personal)
4. [Cámara de Comercio](#cámara-de-comercio)
5. [Salud](#salud)
6. [Composición Familiar](#composición-familiar)
7. [Tipo de Vivienda](#tipo-de-vivienda)
8. [Estado de Vivienda](#estado-de-vivienda)
9. [Inventario de Enseres](#inventario-de-enseres)
10. [Servicios Públicos](#servicios-públicos)
11. [Cuentas Bancarias](#cuentas-bancarias)
12. [Tiene Pasivo](#tiene-pasivo)
13. [Pasivos](#pasivos)
14. [Aportante](#aportante)
15. [Data Crédito](#data-crédito)
16. [Reportado](#reportado)
17. [Ingresos Mensuales](#ingresos-mensuales)
18. [Gasto](#gasto)
19. [Estudios](#estudios)
20. [Información Judicial](#información-judicial)
21. [Experiencia Laboral](#experiencia-laboral)
22. [Concepto Final del Evaluador](#concepto-final-del-evaluador)
23. [Registro de Fotos](#registro-de-fotos)

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

## 🏡 TIPO DE VIVIENDA

### **1. VISTA INICIAL**
- **Archivo**: `resources/views/evaluador/evaluacion_visita/visita/tipo_vivienda/tipo_vivienda.php`
- **Acción**: Formulario POST con datos sobre el tipo de vivienda del evaluado

### **2. CONTROLADOR PRINCIPAL**
- **Controlador**: `App\Controllers\TipoViviendaController`
- **Método**: `guardar()`
- **Parámetros**: Datos del formulario (ejemplo: tipo_vivienda, tenencia, servicios, observaciones, etc.)

### **3. BASE DE DATOS**
- **Tabla**: `tipo_vivienda`
- **Campos**: `id_cedula`, `tipo_vivienda`, `tenencia`, `servicios`, `observaciones`, etc.

### **4. FLUJO COMPLETO**
```
1. Usuario accede a tipo_vivienda.php
   ↓
2. Llena y envía el formulario (POST)
   ↓
3. POST → TipoViviendaController::guardar()
   ↓
4. El controlador valida y sanitiza los datos
   ↓
5. Se verifica si existe registro para la cédula en 'tipo_vivienda'
   ↓
6a. Si NO existe: INSERT en tabla 'tipo_vivienda'
6b. Si existe: UPDATE en tabla 'tipo_vivienda'
   ↓
7. Si la operación es exitosa:
      Redirige a la siguiente vista del flujo (ejemplo: ../servicios_publicos/servicios_publicos.php)
   ↓
8. Si hay error:
      Muestra mensaje de error en la misma vista tipo_vivienda.php
```

### **5. REDIRECCIONES**
- **✅ Éxito**: Redirige a `../servicios_publicos/servicios_publicos.php` (o la siguiente vista definida en el flujo)
- **❌ Error**: Muestra mensaje de error en la misma vista

---

## 🏚️ ESTADO DE VIVIENDA

### **1. VISTA INICIAL**
- **Archivo**: `resources/views/evaluador/evaluacion_visita/visita/estado_vivienda/estado_vivienda.php`
- **Acción**: Formulario POST con datos sobre el estado físico y condiciones de la vivienda.

### **2. CONTROLADOR PRINCIPAL**
- **Controlador**: `App\Controllers\EstadoViviendaController`
- **Método**: `guardar()`
- **Parámetros**: Datos del formulario (ejemplo: estado_paredes, estado_techo, estado_pisos, iluminación, ventilación, observaciones, etc.)

### **3. BASE DE DATOS**
- **Tabla**: `estado_vivienda`
- **Campos**: `id_cedula`, `estado_paredes`, `estado_techo`, `estado_pisos`, `iluminacion`, `ventilacion`, `observaciones`, etc.

### **4. FLUJO COMPLETO**
```
1. Usuario accede a estado_vivienda.php
   ↓
2. Llena y envía el formulario (POST)
   ↓
3. POST → EstadoViviendaController::guardar()
   ↓
4. El controlador valida y sanitiza los datos
   ↓
5. Se verifica si existe registro para la cédula en 'estado_vivienda'
   ↓
6a. Si NO existe: INSERT en tabla 'estado_vivienda'
6b. Si existe: UPDATE en tabla 'estado_vivienda'
   ↓
7. Si la operación es exitosa:
      Redirige a la siguiente vista del flujo (ejemplo: ../servicios_publicos/servicios_publicos.php)
   ↓
8. Si hay error:
      Muestra mensaje de error en la misma vista estado_vivienda.php
```

### **5. REDIRECCIONES**
- **✅ Éxito**: Redirige a `../servicios_publicos/servicios_publicos.php` (o la siguiente vista definida en el flujo)
- **❌ Error**: Muestra mensaje de error en la misma vista

---

## 🪑 INVENTARIO DE ENSERES

### **1. VISTA INICIAL**
- **Archivo**: `resources/views/evaluador/evaluacion_visita/visita/inventario_enseres/inventario_enseres.php`
- **Acción**: Formulario POST con datos sobre los enseres y bienes del hogar.

### **2. CONTROLADOR PRINCIPAL**
- **Controlador**: `App\Controllers\InventarioEnseresController`
- **Método**: `guardar()`
- **Parámetros**: Datos del formulario (ejemplo: lista de enseres, cantidad, estado, observaciones, etc.)

### **3. BASE DE DATOS**
- **Tabla**: `inventario_enseres`
- **Campos**: `id_cedula`, `enser`, `cantidad`, `estado`, `observaciones`, etc.

### **4. FLUJO COMPLETO**
```
1. Usuario accede a inventario_enseres.php
   ↓
2. Llena y envía el formulario (POST)
   ↓
3. POST → InventarioEnseresController::guardar()
   ↓
4. El controlador valida y sanitiza los datos
   ↓
5. Se verifica si existe registro para la cédula en 'inventario_enseres'
   ↓
6a. Si NO existe: INSERT en tabla 'inventario_enseres'
6b. Si existe: UPDATE en tabla 'inventario_enseres'
   ↓
7. Si la operación es exitosa:
      Redirige a la siguiente vista del flujo (ejemplo: ../servicios_publicos/servicios_publicos.php)
   ↓
8. Si hay error:
      Muestra mensaje de error en la misma vista inventario_enseres.php
```

### **5. REDIRECCIONES**
- **✅ Éxito**: Redirige a `../servicios_publicos/servicios_publicos.php` (o la siguiente vista definida en el flujo)
- **❌ Error**: Muestra mensaje de error en la misma vista

---

## 💡 SERVICIOS PÚBLICOS

### **1. VISTA INICIAL**
- **Archivo**: `resources/views/evaluador/evaluacion_visita/visita/servicios_publicos/servicios_publicos.php`
- **Acción**: Formulario POST con datos sobre los servicios públicos disponibles en la vivienda.

### **2. CONTROLADOR PRINCIPAL**
- **Controlador**: `App\Controllers\ServiciosPublicosController`
- **Método**: `guardar()`
- **Parámetros**: Datos del formulario (ejemplo: agua, luz, gas, alcantarillado, recoleccion_basura, internet, observaciones, etc.)

### **3. BASE DE DATOS**
- **Tabla**: `servicios_publicos`
- **Campos**: `id_cedula`, `agua`, `luz`, `gas`, `alcantarillado`, `recoleccion_basura`, `internet`, `observaciones`, etc.

### **4. FLUJO COMPLETO**
```
1. Usuario accede a servicios_publicos.php
   ↓
2. Llena y envía el formulario (POST)
   ↓
3. POST → ServiciosPublicosController::guardar()
   ↓
4. El controlador valida y sanitiza los datos
   ↓
5. Se verifica si existe registro para la cédula en 'servicios_publicos'
   ↓
6a. Si NO existe: INSERT en tabla 'servicios_publicos'
6b. Si existe: UPDATE en tabla 'servicios_publicos'
   ↓
7. Si la operación es exitosa:
      Redirige a la siguiente vista del flujo (ejemplo: ../cuentas_bancarias/cuentas_bancarias.php)
   ↓
8. Si hay error:
      Muestra mensaje de error en la misma vista servicios_publicos.php
```

### **5. REDIRECCIONES**
- **✅ Éxito**: Redirige a `../cuentas_bancarias/cuentas_bancarias.php` (o la siguiente vista definida en el flujo)
- **❌ Error**: Muestra mensaje de error en la misma vista

---

## 🏦 CUENTAS BANCARIAS

### **1. VISTA INICIAL**
- **Archivo**: `resources/views/evaluador/evaluacion_visita/visita/cuentas_bancarias/cuentas_bancarias.php`
- **Acción**: Formulario POST con datos de cuentas bancarias del evaluado.

### **2. CONTROLADOR PRINCIPAL**
- **Controlador**: `App\Controllers\CuentasBancariasController`
- **Método**: `guardar()`
- **Parámetros**: Datos del formulario (ejemplo: banco, tipo_cuenta, numero_cuenta, saldo, observaciones, etc.)

### **3. BASE DE DATOS**
- **Tabla**: `cuentas_bancarias`
- **Campos**: `id_cedula`, `banco`, `tipo_cuenta`, `numero_cuenta`, `saldo`, `observaciones`, etc.

### **4. FLUJO COMPLETO**
```
1. Usuario accede a cuentas_bancarias.php
   ↓
2. Llena y envía el formulario (POST)
   ↓
3. POST → CuentasBancariasController::guardar()
   ↓
4. El controlador valida y sanitiza los datos
   ↓
5. Se verifica si existe registro para la cédula en 'cuentas_bancarias'
   ↓
6a. Si NO existe: INSERT en tabla 'cuentas_bancarias'
6b. Si existe: UPDATE en tabla 'cuentas_bancarias'
   ↓
7. Si la operación es exitosa:
      Redirige a la siguiente vista del flujo (ejemplo: ../tiene_pasivo/tiene_pasivo.php)
   ↓
8. Si hay error:
      Muestra mensaje de error en la misma vista cuentas_bancarias.php
```

### **5. REDIRECCIONES**
- **✅ Éxito**: Redirige a `../tiene_pasivo/tiene_pasivo.php`
- **❌ Error**: Muestra mensaje de error en la misma vista

---

## 📝 TIENE PASIVO

### **1. VISTA INICIAL**
- **Archivo**: `resources/views/evaluador/evaluacion_visita/visita/tiene_pasivo/tiene_pasivo.php`
- **Acción**: Formulario POST para indicar si el evaluado tiene pasivos.

### **2. CONTROLADOR PRINCIPAL**
- **Controlador**: `App\Controllers\TienePasivoController`
- **Método**: `guardar()`
- **Parámetros**: Datos del formulario (ejemplo: tiene_pasivo, observaciones)

### **3. BASE DE DATOS**
- **Tabla**: `tiene_pasivo`
- **Campos**: `id_cedula`, `tiene_pasivo`, `observaciones`

### **4. FLUJO COMPLETO**
```
1. Usuario accede a tiene_pasivo.php
   ↓
2. Llena y envía el formulario (POST)
   ↓
3. POST → TienePasivoController::guardar()
   ↓
4. El controlador valida y sanitiza los datos
   ↓
5. Se verifica si existe registro para la cédula en 'tiene_pasivo'
   ↓
6a. Si NO existe: INSERT en tabla 'tiene_pasivo'
6b. Si existe: UPDATE en tabla 'tiene_pasivo'
   ↓
7. Si la operación es exitosa:
      Redirige a la siguiente vista del flujo (ejemplo: ../pasivos/pasivos.php)
   ↓
8. Si hay error:
      Muestra mensaje de error en la misma vista tiene_pasivo.php
```

### **5. REDIRECCIONES**
- **✅ Éxito**: Redirige a `../pasivos/pasivos.php`
- **❌ Error**: Muestra mensaje de error en la misma vista

---

## 💳 PASIVOS

### **1. VISTA INICIAL**
- **Archivo**: `resources/views/evaluador/evaluacion_visita/visita/pasivos/pasivos.php`
- **Acción**: Formulario POST con datos de los pasivos del evaluado.

### **2. CONTROLADOR PRINCIPAL**
- **Controlador**: `App\Controllers\PasivosController`
- **Método**: `guardar()`
- **Parámetros**: Datos del formulario (ejemplo: tipo_pasivo, entidad, valor, saldo, observaciones, etc.)

### **3. BASE DE DATOS**
- **Tabla**: `pasivos`
- **Campos**: `id_cedula`, `tipo_pasivo`, `entidad`, `valor`, `saldo`, `observaciones`, etc.

### **4. FLUJO COMPLETO**
```
1. Usuario accede a pasivos.php
   ↓
2. Llena y envía el formulario (POST)
   ↓
3. POST → PasivosController::guardar()
   ↓
4. El controlador valida y sanitiza los datos
   ↓
5. Se verifica si existe registro para la cédula en 'pasivos'
   ↓
6a. Si NO existe: INSERT en tabla 'pasivos'
6b. Si existe: UPDATE en tabla 'pasivos'
   ↓
7. Si la operación es exitosa:
      Redirige a la siguiente vista del flujo (ejemplo: ../aportante/aportante.php)
   ↓
8. Si hay error:
      Muestra mensaje de error en la misma vista pasivos.php
```

### **5. REDIRECCIONES**
- **✅ Éxito**: Redirige a `../aportante/aportante.php`
- **❌ Error**: Muestra mensaje de error en la misma vista

---

## 👤 APORTANTE

### **1. VISTA INICIAL**
- **Archivo**: `resources/views/evaluador/evaluacion_visita/visita/aportante/aportante.php`
- **Acción**: Formulario POST con datos del aportante.

### **2. CONTROLADOR PRINCIPAL**
- **Controlador**: `App\Controllers\AportanteController`
- **Método**: `guardar()`
- **Parámetros**: Datos del formulario (ejemplo: nombre_aportante, parentesco, valor_aporte, observaciones, etc.)

### **3. BASE DE DATOS**
- **Tabla**: `aportante`
- **Campos**: `id_cedula`, `nombre_aportante`, `parentesco`, `valor_aporte`, `observaciones`, etc.

### **4. FLUJO COMPLETO**
```
1. Usuario accede a aportante.php
   ↓
2. Llena y envía el formulario (POST)
   ↓
3. POST → AportanteController::guardar()
   ↓
4. El controlador valida y sanitiza los datos
   ↓
5. Se verifica si existe registro para la cédula en 'aportante'
   ↓
6a. Si NO existe: INSERT en tabla 'aportante'
6b. Si existe: UPDATE en tabla 'aportante'
   ↓
7. Si la operación es exitosa:
      Redirige a la siguiente vista del flujo (ejemplo: ../data_credito/data_credito.php)
   ↓
8. Si hay error:
      Muestra mensaje de error en la misma vista aportante.php
```

### **5. REDIRECCIONES**
- **✅ Éxito**: Redirige a `../data_credito/data_credito.php`
- **❌ Error**: Muestra mensaje de error en la misma vista

---

## 🗂️ DATA CRÉDITO

### **1. VISTA INICIAL**
- **Archivo**: `resources/views/evaluador/evaluacion_visita/visita/data_credito/data_credito.php`
- **Acción**: Formulario POST con información de Data Crédito.

### **2. CONTROLADOR PRINCIPAL**
- **Controlador**: `App\Controllers\DataCreditoController`
- **Método**: `guardar()`
- **Parámetros**: Datos del formulario (ejemplo: estado_data_credito, observaciones)

### **3. BASE DE DATOS**
- **Tabla**: `data_credito`
- **Campos**: `id_cedula`, `estado_data_credito`, `observaciones`

### **4. FLUJO COMPLETO**
```
1. Usuario accede a data_credito.php
   ↓
2. Llena y envía el formulario (POST)
   ↓
3. POST → DataCreditoController::guardar()
   ↓
4. El controlador valida y sanitiza los datos
   ↓
5. Se verifica si existe registro para la cédula en 'data_credito'
   ↓
6a. Si NO existe: INSERT en tabla 'data_credito'
6b. Si existe: UPDATE en tabla 'data_credito'
   ↓
7. Si la operación es exitosa:
      Redirige a la siguiente vista del flujo (ejemplo: ../reportado/reportado.php)
   ↓
8. Si hay error:
      Muestra mensaje de error en la misma vista data_credito.php
```

### **5. REDIRECCIONES**
- **✅ Éxito**: Redirige a `../reportado/reportado.php`
- **❌ Error**: Muestra mensaje de error en la misma vista

---

## 🚩 REPORTADO

### **1. VISTA INICIAL**
- **Archivo**: `resources/views/evaluador/evaluacion_visita/visita/reportado/reportado.php`
- **Acción**: Formulario POST para indicar si el evaluado está reportado.

### **2. CONTROLADOR PRINCIPAL**
- **Controlador**: `App\Controllers\ReportadoController`
- **Método**: `guardar()`
- **Parámetros**: Datos del formulario (ejemplo: esta_reportado, observaciones)

### **3. BASE DE DATOS**
- **Tabla**: `reportado`
- **Campos**: `id_cedula`, `esta_reportado`, `observaciones`

### **4. FLUJO COMPLETO**
```
1. Usuario accede a reportado.php
   ↓
2. Llena y envía el formulario (POST)
   ↓
3. POST → ReportadoController::guardar()
   ↓
4. El controlador valida y sanitiza los datos
   ↓
5. Se verifica si existe registro para la cédula en 'reportado'
   ↓
6a. Si NO existe: INSERT en tabla 'reportado'
6b. Si existe: UPDATE en tabla 'reportado'
   ↓
7. Si la operación es exitosa:
      Redirige a la siguiente vista del flujo (ejemplo: ../ingresos_mensuales/ingresos_mensuales.php)
   ↓
8. Si hay error:
      Muestra mensaje de error en la misma vista reportado.php
```

### **5. REDIRECCIONES**
- **✅ Éxito**: Redirige a `../ingresos_mensuales/ingresos_mensuales.php`
- **❌ Error**: Muestra mensaje de error en la misma vista

---

## 💰 INGRESOS MENSUALES

### **1. VISTA INICIAL**
- **Archivo**: `resources/views/evaluador/evaluacion_visita/visita/ingresos_mensuales/ingresos_mensuales.php`
- **Acción**: Formulario POST con datos de ingresos mensuales.

### **2. CONTROLADOR PRINCIPAL**
- **Controlador**: `App\Controllers\IngresosMensualesController`
- **Método**: `guardar()`
- **Parámetros**: Datos del formulario (ejemplo: tipo_ingreso, valor, observaciones, etc.)

### **3. BASE DE DATOS**
- **Tabla**: `ingresos_mensuales`
- **Campos**: `id_cedula`, `tipo_ingreso`, `valor`, `observaciones`, etc.

### **4. FLUJO COMPLETO**
```
1. Usuario accede a ingresos_mensuales.php
   ↓
2. Llena y envía el formulario (POST)
   ↓
3. POST → IngresosMensualesController::guardar()
   ↓
4. El controlador valida y sanitiza los datos
   ↓
5. Se verifica si existe registro para la cédula en 'ingresos_mensuales'
   ↓
6a. Si NO existe: INSERT en tabla 'ingresos_mensuales'
6b. Si existe: UPDATE en tabla 'ingresos_mensuales'
   ↓
7. Si la operación es exitosa:
      Redirige a la siguiente vista del flujo (ejemplo: ../gasto/gasto.php)
   ↓
8. Si hay error:
      Muestra mensaje de error en la misma vista ingresos_mensuales.php
```

### **5. REDIRECCIONES**
- **✅ Éxito**: Redirige a `../gasto/gasto.php`
- **❌ Error**: Muestra mensaje de error en la misma vista

---

## 💸 GASTO

### **1. VISTA INICIAL**
- **Archivo**: `resources/views/evaluador/evaluacion_visita/visita/gasto/gasto.php`
- **Acción**: Formulario POST con datos de gastos mensuales.

### **2. CONTROLADOR PRINCIPAL**
- **Controlador**: `App\Controllers\GastoController`
- **Método**: `guardar()`
- **Parámetros**: Datos del formulario (ejemplo: tipo_gasto, valor, observaciones, etc.)

### **3. BASE DE DATOS**
- **Tabla**: `gasto`
- **Campos**: `id_cedula`, `tipo_gasto`, `valor`, `observaciones`, etc.

### **4. FLUJO COMPLETO**
```
1. Usuario accede a gasto.php
   ↓
2. Llena y envía el formulario (POST)
   ↓
3. POST → GastoController::guardar()
   ↓
4. El controlador valida y sanitiza los datos
   ↓
5. Se verifica si existe registro para la cédula en 'gasto'
   ↓
6a. Si NO existe: INSERT en tabla 'gasto'
6b. Si existe: UPDATE en tabla 'gasto'
   ↓
7. Si la operación es exitosa:
      Redirige a la siguiente vista del flujo (ejemplo: ../estudios/estudios.php)
   ↓
8. Si hay error:
      Muestra mensaje de error en la misma vista gasto.php
```

### **5. REDIRECCIONES**
- **✅ Éxito**: Redirige a `../estudios/estudios.php`
- **❌ Error**: Muestra mensaje de error en la misma vista

---

## 🎓 ESTUDIOS

### **1. VISTA INICIAL**
- **Archivo**: `resources/views/evaluador/evaluacion_visita/visita/estudios/estudios.php`
- **Acción**: Formulario POST con datos de estudios realizados.

### **2. CONTROLADOR PRINCIPAL**
- **Controlador**: `App\Controllers\EstudiosController`
- **Método**: `guardar()`
- **Parámetros**: Datos del formulario (ejemplo: nivel_estudio, institucion, año_finalizacion, observaciones, etc.)

### **3. BASE DE DATOS**
- **Tabla**: `estudios`
- **Campos**: `id_cedula`, `nivel_estudio`, `institucion`, `año_finalizacion`, `observaciones`, etc.

### **4. FLUJO COMPLETO**
```
1. Usuario accede a estudios.php
   ↓
2. Llena y envía el formulario (POST)
   ↓
3. POST → EstudiosController::guardar()
   ↓
4. El controlador valida y sanitiza los datos
   ↓
5. Se verifica si existe registro para la cédula en 'estudios'
   ↓
6a. Si NO existe: INSERT en tabla 'estudios'
6b. Si existe: UPDATE en tabla 'estudios'
   ↓
7. Si la operación es exitosa:
      Redirige a la siguiente vista del flujo (ejemplo: ../informacion_judicial/informacion_judicial.php)
   ↓
8. Si hay error:
      Muestra mensaje de error en la misma vista estudios.php
```

### **5. REDIRECCIONES**
- **✅ Éxito**: Redirige a `../informacion_judicial/informacion_judicial.php`
- **❌ Error**: Muestra mensaje de error en la misma vista

---

## ⚖️ INFORMACIÓN JUDICIAL

### **1. VISTA INICIAL**
- **Archivo**: `resources/views/evaluador/evaluacion_visita/visita/informacion_judicial/informacion_judicial.php`
- **Acción**: Formulario POST con datos judiciales del evaluado.

### **2. CONTROLADOR PRINCIPAL**
- **Controlador**: `App\Controllers\InformacionJudicialController`
- **Método**: `guardar()`
- **Parámetros**: Datos del formulario (ejemplo: antecedentes, procesos, observaciones, etc.)

### **3. BASE DE DATOS**
- **Tabla**: `informacion_judicial`
- **Campos**: `id_cedula`, `antecedentes`, `procesos`, `observaciones`, etc.

### **4. FLUJO COMPLETO**
```
1. Usuario accede a informacion_judicial.php
   ↓
2. Llena y envía el formulario (POST)
   ↓
3. POST → InformacionJudicialController::guardar()
   ↓
4. El controlador valida y sanitiza los datos
   ↓
5. Se verifica si existe registro para la cédula en 'informacion_judicial'
   ↓
6a. Si NO existe: INSERT en tabla 'informacion_judicial'
6b. Si existe: UPDATE en tabla 'informacion_judicial'
   ↓
7. Si la operación es exitosa:
      Redirige a la siguiente vista del flujo (ejemplo: ../experiencia_laboral/experiencia_laboral.php)
   ↓
8. Si hay error:
      Muestra mensaje de error en la misma vista informacion_judicial.php
```

### **5. REDIRECCIONES**
- **✅ Éxito**: Redirige a `../experiencia_laboral/experiencia_laboral.php`
- **❌ Error**: Muestra mensaje de error en la misma vista

---

## 💼 EXPERIENCIA LABORAL

### **1. VISTA INICIAL**
- **Archivo**: `resources/views/evaluador/evaluacion_visita/visita/experiencia_laboral/experiencia_laboral.php`
- **Acción**: Formulario POST con datos de experiencia laboral.

### **2. CONTROLADOR PRINCIPAL**
- **Controlador**: `App\Controllers\ExperienciaLaboralController`
- **Método**: `guardar()`
- **Parámetros**: Datos del formulario (ejemplo: empresa, cargo, tiempo, observaciones, etc.)

### **3. BASE DE DATOS**
- **Tabla**: `experiencia_laboral`
- **Campos**: `id_cedula`, `empresa`, `cargo`, `tiempo`, `observaciones`, etc.

### **4. FLUJO COMPLETO**
```
1. Usuario accede a experiencia_laboral.php
   ↓
2. Llena y envía el formulario (POST)
   ↓
3. POST → ExperienciaLaboralController::guardar()
   ↓
4. El controlador valida y sanitiza los datos
   ↓
5. Se verifica si existe registro para la cédula en 'experiencia_laboral'
   ↓
6a. Si NO existe: INSERT en tabla 'experiencia_laboral'
6b. Si existe: UPDATE en tabla 'experiencia_laboral'
   ↓
7. Si la operación es exitosa:
      Redirige a la siguiente vista del flujo (ejemplo: ../concepto_final_evaluador/concepto_final_evaluador.php)
   ↓
8. Si hay error:
      Muestra mensaje de error en la misma vista experiencia_laboral.php
```

### **5. REDIRECCIONES**
- **✅ Éxito**: Redirige a `../concepto_final_evaluador/concepto_final_evaluador.php`
- **❌ Error**: Muestra mensaje de error en la misma vista

---

## 📝 CONCEPTO FINAL DEL EVALUADOR

### **1. VISTA INICIAL**
- **Archivo**: `resources/views/evaluador/evaluacion_visita/visita/concepto_final_evaluador/concepto_final_evaluador.php`
- **Acción**: Formulario POST con el concepto final del evaluador.

### **2. CONTROLADOR PRINCIPAL**
- **Controlador**: `App\Controllers\ConceptoFinalEvaluadorController`
- **Método**: `guardar()`
- **Parámetros**: Datos del formulario (ejemplo: concepto, recomendaciones, observaciones, etc.)

### **3. BASE DE DATOS**
- **Tabla**: `concepto_final_evaluador`
- **Campos**: `id_cedula`, `concepto`, `recomendaciones`, `observaciones`, etc.

### **4. FLUJO COMPLETO**
```
1. Usuario accede a concepto_final_evaluador.php
   ↓
2. Llena y envía el formulario (POST)
   ↓
3. POST → ConceptoFinalEvaluadorController::guardar()
   ↓
4. El controlador valida y sanitiza los datos
   ↓
5. Se verifica si existe registro para la cédula en 'concepto_final_evaluador'
   ↓
6a. Si NO existe: INSERT en tabla 'concepto_final_evaluador'
6b. Si existe: UPDATE en tabla 'concepto_final_evaluador'
   ↓
7. Si la operación es exitosa:
      Redirige a la siguiente vista del flujo (ejemplo: ../registro_fotos/registro_fotos.php)
   ↓
8. Si hay error:
      Muestra mensaje de error en la misma vista concepto_final_evaluador.php
```

### **5. REDIRECCIONES**
- **✅ Éxito**: Redirige a `../registro_fotos/registro_fotos.php`
- **❌ Error**: Muestra mensaje de error en la misma vista

---

## 📷 REGISTRO DE FOTOS

### **1. VISTA INICIAL**
- **Archivo**: `resources/views/evaluador/evaluacion_visita/visita/registro_fotos/registro_fotos.php`
- **Acción**: Formulario POST para subir fotos de la visita.

### **2. CONTROLADOR PRINCIPAL**
- **Controlador**: `App\Controllers\RegistroFotosController`
- **Método**: `guardar()`
- **Parámetros**: Datos del formulario (ejemplo: archivos de imagen, descripción, fecha, etc.)

### **3. BASE DE DATOS**
- **Tabla**: `registro_fotos`
- **Campos**: `id_cedula`, `ruta_foto`, `descripcion`, `fecha`, etc.

### **4. FLUJO COMPLETO**
```
1. Usuario accede a registro_fotos.php
   ↓
2. Selecciona y sube las fotos (POST)
   ↓
3. POST → RegistroFotosController::guardar()
   ↓
4. El controlador valida y procesa los archivos
   ↓
5. Se verifica si existe registro para la cédula en 'registro_fotos'
   ↓
6a. Si NO existe: INSERT en tabla 'registro_fotos'
6b. Si existe: UPDATE en tabla 'registro_fotos'
   ↓
7. Si la operación es exitosa:
      Redirige a la vista final del flujo (ejemplo: ../finalizacion/finalizacion.php)
   ↓
8. Si hay error:
      Muestra mensaje de error en la misma vista registro_fotos.php
```

### **5. REDIRECCIONES**
- **✅ Éxito**: Redirige a `../finalizacion/finalizacion.php` (o la vista final definida en el flujo)
- **❌ Error**: Muestra mensaje de error en la misma vista

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

---

## Flujo de trabajo: Vista `tiene_pareja.php`

1. **Acceso a la vista**  
   El usuario accede a la página `tiene_pareja.php` desde el menú principal o tras completar un registro previo.

2. **Verificación de pareja**  
   El sistema verifica si el usuario tiene una pareja registrada en la base de datos.

3. **Despliegue de información**  
   - Si el usuario tiene pareja, se muestra la información relevante (nombre, datos de contacto, etc.).
   - Si no tiene pareja, se muestra un mensaje indicando que no hay pareja registrada y se ofrece la opción de registrar una nueva.

4. **Acciones disponibles**  
   - Registrar nueva pareja.
   - Editar información de la pareja existente.
   - Eliminar pareja.

5. **Redirección**  
   Tras realizar alguna acción, el sistema redirige al usuario a la vista correspondiente (confirmación, edición, etc.).

---
