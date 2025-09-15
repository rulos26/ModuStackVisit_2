# 🔄 Diagrama del Flujo de Validación de Documentos

## Flujo Optimizado Implementado

```mermaid
flowchart TD
    A[👤 Usuario ingresa N° de Documento] --> B{🔍 Validar Formato}
    
    B -->|❌ No numérico o ≤ 0| C[❌ Error: Documento inválido]
    B -->|❌ < 7 o > 10 dígitos| C
    B -->|✅ Válido| D[🔍 Buscar en tabla EVALUADOS]
    
    D -->|✅ Existe| E[📋 Cargar datos del evaluado]
    E --> F[✅ Mensaje: Evaluado encontrado]
    F --> G[➡️ Redirigir a Información Personal]
    
    D -->|❌ No existe| H[🔍 Buscar en tabla AUTORIZACIONES]
    
    H -->|✅ Existe| I[📝 Crear evaluado desde autorización]
    I --> J[✅ Mensaje: Evaluado creado desde autorización]
    J --> K[➡️ Redirigir a Información Personal]
    
    H -->|❌ No existe| L[❌ Error: No se encontró cédula]
    L --> M[➡️ Redirigir a Carta de Autorización]
    
    C --> N[🔄 Volver al formulario]
    
    style A fill:#e1f5fe
    style C fill:#ffebee
    style E fill:#e8f5e8
    style F fill:#e8f5e8
    style G fill:#e8f5e8
    style I fill:#fff3e0
    style J fill:#fff3e0
    style K fill:#fff3e0
    style L fill:#ffebee
    style M fill:#ffebee
    style N fill:#f3e5f5
```

## Casos de Uso Detallados

### ✅ Caso 1: Evaluado Existente
```mermaid
sequenceDiagram
    participant U as Usuario
    participant F as Formulario
    participant S as Servidor
    participant DB as Base de Datos
    participant V as Vista
    
    U->>F: Ingresa documento válido
    F->>S: Envía POST con cédula
    S->>DB: SELECT FROM evaluados WHERE id_cedula = ?
    DB-->>S: Retorna datos del evaluado
    S-->>F: Success + datos
    F->>V: Redirige a Información Personal
    V-->>U: Muestra datos existentes
```

### ✅ Caso 2: Crear desde Autorización
```mermaid
sequenceDiagram
    participant U as Usuario
    participant F as Formulario
    participant S as Servidor
    participant DB as Base de Datos
    participant V as Vista
    
    U->>F: Ingresa documento válido
    F->>S: Envía POST con cédula
    S->>DB: SELECT FROM evaluados WHERE id_cedula = ?
    DB-->>S: No existe
    S->>DB: SELECT FROM autorizaciones WHERE cedula = ?
    DB-->>S: Retorna datos de autorización
    S->>DB: INSERT INTO evaluados (datos de autorización)
    DB-->>S: Evaluado creado
    S-->>F: Success + datos creados
    F->>V: Redirige a Información Personal
    V-->>U: Muestra formulario con datos precargados
```

### ❌ Caso 3: No Encontrado
```mermaid
sequenceDiagram
    participant U as Usuario
    participant F as Formulario
    participant S as Servidor
    participant DB as Base de Datos
    participant CA as Carta Autorización
    
    U->>F: Ingresa documento válido
    F->>S: Envía POST con cédula
    S->>DB: SELECT FROM evaluados WHERE id_cedula = ?
    DB-->>S: No existe
    S->>DB: SELECT FROM autorizaciones WHERE cedula = ?
    DB-->>S: No existe
    S-->>F: Error: No encontrado
    F->>CA: Redirige a Carta de Autorización
    CA-->>U: Muestra formulario de autorización
```

## Arquitectura del Sistema

```mermaid
graph TB
    subgraph "Frontend"
        A[index.php - Formulario]
        B[JavaScript - Validación]
    end
    
    subgraph "Backend"
        C[session.php - Procesador]
        D[DocumentoValidatorController]
    end
    
    subgraph "Base de Datos"
        E[(evaluados)]
        F[(autorizaciones)]
    end
    
    subgraph "Vistas"
        G[informacion_personal.php]
        H[carta_visita/index_carta.php]
    end
    
    A --> B
    A --> C
    C --> D
    D --> E
    D --> F
    D --> G
    D --> H
    
    style A fill:#e3f2fd
    style B fill:#e3f2fd
    style C fill:#f3e5f5
    style D fill:#f3e5f5
    style E fill:#e8f5e8
    style F fill:#e8f5e8
    style G fill:#fff3e0
    style H fill:#fff3e0
```

## Validaciones Implementadas

```mermaid
graph LR
    A[Documento Ingresado] --> B{¿Es numérico?}
    B -->|No| C[❌ Error: No numérico]
    B -->|Sí| D{¿Mayor que 0?}
    D -->|No| E[❌ Error: Debe ser > 0]
    D -->|Sí| F{¿7-10 dígitos?}
    F -->|No| G[❌ Error: Longitud inválida]
    F -->|Sí| H[✅ Documento válido]
    
    style C fill:#ffebee
    style E fill:#ffebee
    style G fill:#ffebee
    style H fill:#e8f5e8
```

## Mensajes del Sistema

```mermaid
graph TD
    A[Resultado de Validación] --> B{¿Éxito?}
    
    B -->|Sí| C{¿Acción?}
    C -->|evaluado_existente| D[✅ Evaluado encontrado. Redirigiendo...]
    C -->|evaluado_creado| E[✅ Evaluado creado desde autorización. Continúe...]
    
    B -->|No| F{¿Acción?}
    F -->|error| G[❌ Número de documento inválido. Ingrese una cédula válida (7-10 dígitos).]
    F -->|no_encontrado| H[❌ No se encontró ninguna cédula asociada con carta de autorización.]
    
    D --> I[➡️ Información Personal]
    E --> I
    G --> J[🔄 Volver al formulario]
    H --> K[➡️ Carta de Autorización]
    
    style D fill:#e8f5e8
    style E fill:#fff3e0
    style G fill:#ffebee
    style H fill:#ffebee
    style I fill:#e3f2fd
    style J fill:#f3e5f5
    style K fill:#f3e5f5
```
