<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Planilla de Censo Familiar - Consejo Comunal</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        h2, h3 {
            text-align: center;
            color: #1a202c;
            margin-bottom: 5px;
        }
        .subtitle {
            text-align: center;
            font-size: 10px;
            color: #666;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #cbd5e0;
            padding: 5px 8px;
            text-align: left;
        }
        th {
            background-color: #edf2f7;
            color: #2d3748;
        }
        .section-title {
            background-color: #2b6cb0;
            color: white;
            padding: 6px;
            font-weight: bold;
            font-size: 12px;
            margin-top: 15px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

    <h2>{{ $censo->consejo_comunal }}</h2>
    <div class="subtitle">Estado: {{ $censo->estado }} | Municipio: {{ $censo->municipio }} | Parroquia: {{ $censo->parroquia }}</div>

    <!-- SECCIÓN I Y II: DATOS DE UBICACIÓN Y JEFE DE FAMILIA -->
    <div class="section-title">I Y II. DATOS DE UBICACIÓN Y JEFE(A) DE FAMILIA</div>
    <table>
        <tr>
            <th>Sector / Calle:</th>
            <td>{{ $censo->sector_calle }}</td>
            <th>Nro. Casa / Edif:</th>
            <td>{{ $censo->numero_vivienda_dir }}</td>
        </tr>
        <tr>
            <th>Fecha del Censo:</th>
            <td>{{ $censo->fecha_censo }}</td>
            <th>Estado Civil:</th>
            <td>{{ $censo->jefe_estado_civil }}</td>
        </tr>
        <tr>
            <th>Nombres y Apellidos:</th>
            <td colspan="3">{{ $censo->jefe_nombre }}</td>
        </tr>
        <tr>
            <th>Cédula de Identidad:</th>
            <td>{{ $censo->jefe_nacionalidad }}-{{ $censo->jefe_cedula }}</td>
            <th>Edad / Sexo:</th>
            <td>{{ $censo->jefe_edad }} años / {{ $censo->jefe_sexo }}</td>
        </tr>
        <tr>
            <th>Teléfono Celular:</th>
            <td>{{ $censo->jefe_telefono }}</td>
            <th>Teléfono Alternativo:</th>
            <td>{{ $censo->jefe_telefono_alt ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Nivel de Instrucción:</th>
            <td>{{ $censo->jefe_instruccion }}</td>
            <th>Ocupación:</th>
            <td>{{ $censo->jefe_ocupacion }}</td>
        </tr>
        <tr>
            <th>¿Posee Carnet de la Patria?:</th>
            <td>{{ $censo->posee_carnet_patria }}</td>
            <th>Código / Serial:</th>
            <td>{{ $censo->codigo_carnet ?? 'N/A' }} / {{ $censo->serial_carnet ?? 'N/A' }}</td>
        </tr>
    </table>

    <!-- SECCIÓN III: CARACTERÍSTICAS DE LA VIVIENDA Y SERVICIOS -->
    <div class="section-title">III. CARACTERÍSTICAS DE LA VIVIENDA Y SERVICIOS</div>
    <table>
        <tr>
            <th>Tipo de Vivienda:</th>
            <td>{{ $censo->tipo_vivienda }}</td>
            <th>Condición Jurídica:</th>
            <td>{{ $censo->condicion_juridica }}</td>
        </tr>
        <tr>
            <th>Infraestructura:</th>
            <td>{{ $censo->estado_infraestructura }}</td>
            <th>Paredes / Techo:</th>
            <td>{{ $censo->material_paredes }} / {{ $censo->material_techo }}</td>
        </tr>
        <tr>
            <th>Agua Potable:</th>
            <td>{{ $censo->abastecimiento_agua }}</td>
            <th>Aguas Servidas:</th>
            <td>{{ $censo->aguas_servidas }}</td>
        </tr>
        <tr>
            <th>Gas Doméstico:</th>
            <td>{{ $censo->acceso_gas }} ({{ $censo->empresa_gas }})</td>
            <th>Conexión Eléctrica:</th>
            <td>{{ $censo->conexion_electrica }}</td>
        </tr>
        <tr>
            <th>Aseo Urbano:</th>
            <td colspan="3">{{ $censo->aseo_urbano }}</td>
        </tr>
    </table>

    <!-- SECCIÓN IV: SITUACIÓN SOCIOECONÓMICA -->
    <div class="section-title">IV. SITUACIÓN SOCIOECONÓMICA</div>
    <table>
        <tr>
            <th>¿Recibe CLAP?:</th>
            <td>{{ $censo->recibe_clap }} ({{ $censo->frecuencia_clap ?? 'N/A' }})</td>
            <th>Ingreso Familiar:</th>
            <td>{{ $censo->ingreso_familiar }}</td>
        </tr>
        <tr>
            <th>¿Recibe Remesas?:</th>
            <td>{{ $censo->recibe_remesas }} @if($censo->recibe_remesas == 'Sí') (${{ $censo->monto_remesas }} - {{ $censo->frecuencia_remesas }}) @endif</td>
            <th>Dificultad Canasta:</th>
            <td>{{ $censo->dificultad_canasta }}</td>
        </tr>
        @if($censo->dificultad_canasta == 'Sí')
        <tr>
            <th>Motivo Dificultad:</th>
            <td colspan="3">{{ $censo->motivo_dificultad_canasta }}</td>
        </tr>
        @endif
    </table>

    <!-- SECCIÓN V: CARGA FAMILIAR -->
    <div class="section-title">V. CARGA FAMILIAR (INTEGRANTES)</div>
    <table>
        <thead>
            <tr>
                <th>Nombres y Apellidos</th>
                <th>Parentesco</th>
                <th>Cédula / Edad</th>
                <th>Nivel Educativo</th>
                <th>Discapacidad</th>
            </tr>
        </thead>
        <tbody>
            @forelse($censo->integrantes as $integrante)
            <tr>
                <td>{{ $integrante->nombre }}</td>
                <td>{{ $integrante->parentesco }}</td>
                <td>
                    @if($integrante->es_menor)
                        Menor de edad
                    @else
                        {{ $integrante->nacionalidad }}-{{ $integrante->cedula }}
                    @endif
                    <br><small>{{ $integrante->edad }}</small>
                </td>
                <td>{{ $integrante->nivel_educativo }}</td>
                <td>{{ $integrante->tiene_discapacidad }} @if($integrante->tiene_discapacidad == 'Sí') ({{ $integrante->discapacidad }}) @endif</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">No se registraron integrantes adicionales.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- SECCIÓN VI Y VII: SALUD Y OBSERVACIONES -->
    <div class="section-title">VI Y VII. SALUD, VULNERABILIDAD Y OBSERVACIONES</div>
    <table>
        <tr>
            <th>Embarazadas:</th>
            <td>{{ $censo->embarazadas_status }} @if($censo->embarazadas_status == 'Sí') (Cant: {{ $censo->embarazadas_cantidad }}, Control: {{ $censo->embarazadas_control }}) @endif</td>
            <th>Lactantes (0-2 años):</th>
            <td>{{ $censo->lactantes_status }} @if($censo->lactantes_status == 'Sí') (Cant: {{ $censo->lactantes_cantidad }}) @endif</td>
        </tr>
        <tr>
            <th>Adultos Mayores:</th>
            <td>{{ $censo->adultos_status }} @if($censo->adultos_status == 'Sí') (Cant: {{ $censo->adultos_cantidad }}) @endif</td>
            <th>Personas Encamadas:</th>
            <td>{{ $censo->encamados_status }} @if($censo->encamados_status == 'Sí') (Cant: {{ $censo->encamados_cantidad }}) @endif</td>
        </tr>
        <tr>
            <th>Enfermedades Crónicas:</th>
            <td>{{ $censo->enfermedades_cronicas_status }} @if($censo->enfermedades_cronicas_status == 'Sí') - {{ $censo->enfermedades_cronicas_detalle }} @endif</td>
            <th>CONAPDIS:</th>
            <td>{{ $censo->conapdis }}</td>
        </tr>
        <tr>
            <th>Observaciones:</th>
            <td colspan="3">{{ $censo->observaciones ?? 'Ninguna' }}</td>
        </tr>
    </table>

</body>
</html>