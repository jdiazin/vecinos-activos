<x-app-layout>
    <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6" x-data="censusForm()">
        <!-- Encabezado -->
        <div class="text-center mb-8">
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Censo Socioeconómico Familiar</h2>
            <p class="text-sm text-slate-500 mt-1">Consejo Comunal • Registro Comunal de Venezuela</p>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl mb-6 text-sm font-medium shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl mb-6 text-sm shadow-sm">
                <p class="font-bold">Por favor corrige los siguientes errores antes de continuar:</p>
                <ul class="list-disc pl-5 mt-1 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Alerta de validación de campos obligatorios por sección -->
        <div x-show="errorMessage" x-transition class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl mb-6 text-sm shadow-sm flex justify-between items-center">
            <span x-text="errorMessage"></span>
            <button type="button" @click="errorMessage = ''" class="text-rose-500 font-bold text-xs hover:underline">✕</button>
        </div>

        <!-- Barra de Progreso Superior -->
        <div class="mb-8">
            <div class="flex justify-between text-xs font-semibold text-slate-600 mb-2">
                <span x-text="stepTitle()"></span>
                <span x-text="`Paso ${step} de 7`"></span>
            </div>
            <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                <div class="bg-indigo-600 h-full transition-all duration-300" :style="`width: ${(step / 7) * 100}%`"></div>
            </div>
        </div>

        <form action="{{ route('census.store') }}" method="POST" class="space-y-6" @submit="validateForm($event)">
            @csrf

            <!-- ================= SECCIÓN I: UBICACIÓN ================= -->
            <div x-show="step === 1" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-xl space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-lg font-bold text-slate-800">Ubicación Geográfica</h3>
                    <p class="text-xs text-slate-400">Datos territoriales del registro comunal.</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <!-- Consejo Comunal con opción Otro -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Consejo Comunal <span class="text-rose-500">*</span></label>
                        <select name="consejo_comunal" x-model="form.consejo_comunal" class="w-full text-sm border-slate-200 rounded-xl p-2.5" required>
                            <option value="">Seleccione...</option>
                            <option value="Consejo Comunal “El Libertador del Siglo XXI”">Consejo Comunal “El Libertador del Siglo XXI”</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>

                    <!-- Especifique Consejo Comunal (Condicional) -->
                    <div x-show="form.consejo_comunal === 'Otro'" x-transition>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Especifique el Consejo Comunal <span class="text-rose-500">*</span></label>
                        <input type="text" name="otro_consejo_comunal" x-model="form.otro_consejo_comunal" :required="form.consejo_comunal === 'Otro'" placeholder="Nombre del consejo comunal" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                    </div>

                    <!-- Estado (Select Dinámico) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Estado <span class="text-rose-500">*</span></label>
                        <select name="estado" x-model="form.estado" @change="cambiarEstado" class="w-full text-sm border-slate-200 rounded-xl p-2.5" required>
                            <option value="">Seleccione...</option>
                            <template x-for="(munList, estName) in veData" :key="estName">
                                <option :value="estName" x-text="estName"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Municipio (Select Dinámico) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Municipio <span class="text-rose-500">*</span></label>
                        <select name="municipio" x-model="form.municipio" @change="cambiarMunicipio" :disabled="!form.estado" class="w-full text-sm border-slate-200 rounded-xl p-2.5 disabled:bg-slate-50 disabled:text-slate-400" required>
                            <option value="">Seleccione...</option>
                            <template x-for="(parList, munName) in municipiosDisponibles" :key="munName">
                                <option :value="munName" x-text="munName"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Parroquia (Select Dinámico) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Parroquia <span class="text-rose-500">*</span></label>
                        <select name="parroquia" x-model="form.parroquia" :disabled="!form.municipio" class="w-full text-sm border-slate-200 rounded-xl p-2.5 disabled:bg-slate-50 disabled:text-slate-400" required>
                            <option value="">Seleccione...</option>
                            <template x-for="parName in parroquiasDisponibles" :key="parName">
                                <option :value="parName" x-text="parName"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Sector, Avenida o Calle <span class="text-rose-500">*</span></label>
                        <input type="text" name="sector_calle" x-model="form.sector_calle" placeholder="Ej: Calle Principal" class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 p-2.5">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nro. de Casa, Edificio o Apt. <span class="text-rose-500">*</span></label>
                        <input type="text" name="numero_vivienda_dir" x-model="form.numero_vivienda_dir" placeholder="Ej: Casa #12" class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 p-2.5">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Fecha del Censo <span class="text-rose-500">*</span></label>
                        <input type="date" name="fecha_censo" x-model="form.fecha_censo" class="w-full text-sm border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 p-2.5">
                    </div>
                </div>
                <div class="flex justify-end pt-4 border-t border-slate-100">
                    <button type="button" @click="nextStep()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-md transition">Siguiente paso →</button>
                </div>
            </div>

          <!-- ================= SECCIÓN II: JEFE DE FAMILIA ================= -->
            <div x-show="step === 2" style="display: none;" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-xl space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-lg font-bold text-slate-800">Datos del Jefe(a) de Familia</h3>
                    <p class="text-xs text-slate-400">Información personal, contacto y documentación del responsable del hogar.</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nombres y Apellidos Completos <span class="text-rose-500">*</span></label>
                        <input type="text" name="jefe_nombre" x-model="form.jefe_nombre" @keypress="if(!/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]$/.test(event.key)) event.preventDefault()" @input="$event.target.value = $event.target.value.replace(/[^A-Za-zñÑáéíóúÁÉÍÓÚ\s]/g, '')" placeholder="Nombre completo" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Cédula de Identidad <span class="text-rose-500">*</span></label>
                        <div class="flex gap-2">
                            <select name="jefe_nacionalidad" x-model="form.jefe_nacionalidad" class="text-xs border-slate-200 rounded-xl w-20 p-2.5 bg-slate-50">
                                <option value="V">V</option>
                                <option value="E">E</option>
                            </select>
                            <input type="text" name="jefe_cedula" x-model="form.jefe_cedula" maxlength="8" @keypress="if(!/[0-9]/.test(event.key)) event.preventDefault()" @input="$event.target.value = $event.target.value.replace(/\D/g, '')" placeholder="Ej: 12345678" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Fecha de Nacimiento <span class="text-rose-500">*</span></label>
                        <input type="date" name="jefe_fecha_nacimiento" x-model="form.jefe_fecha_nacimiento" @change="calcularEdad" :max="new Date(new Date().setFullYear(new Date().getFullYear() - 15)).toISOString().split('T')[0]" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Edad Actual</label>
                        <input type="text" name="jefe_edad" x-model="jefeEdad" readonly class="w-full text-sm bg-slate-50 border-slate-200 rounded-xl text-slate-500 p-2.5">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Sexo <span class="text-rose-500">*</span></label>
                        <select name="jefe_sexo" x-model="form.jefe_sexo" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="">Seleccione</option>
                            <option value="Masculino">Masculino</option>
                            <option value="Femenino">Femenino</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Estado Civil <span class="text-rose-500">*</span></label>
                        <select name="jefe_estado_civil" x-model="form.jefe_estado_civil" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="">Seleccione</option>
                            <option value="Soltero">Soltero(a)</option>
                            <option value="Casado">Casado(a)</option>
                            <option value="Concubinato">Concubinato</option>
                            <option value="Divorciado">Divorciado(a)</option>
                            <option value="Viudo">Viudo(a)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Teléfono Celular Principal <span class="text-rose-500">*</span></label>
                        <input type="text" name="jefe_telefono" x-model="form.jefe_telefono" maxlength="11" @keypress="if(!/[0-9]/.test(event.key)) event.preventDefault()" @input="$event.target.value = $event.target.value.replace(/\D/g, '')" placeholder="04141234567" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Teléfono Local / Alternativo</label>
                        <input type="text" name="jefe_telefono_alt" x-model="form.jefe_telefono_alt" maxlength="11" @keypress="if(!/[0-9]/.test(event.key)) event.preventDefault()" @input="$event.target.value = $event.target.value.replace(/\D/g, '')" placeholder="Opcional" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nivel de Instrucción <span class="text-rose-500">*</span></label>
                        <select name="jefe_instruccion" x-model="form.jefe_instruccion" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="">Seleccione</option>
                            <option value="Ninguno">Ninguno</option>
                            <option value="Primaria">Primaria</option>
                            <option value="Bachillerato">Bachillerato</option>
                            <option value="Técnico">Técnico</option>
                            <option value="Universitario">Universitario</option>
                            <option value="Postgrado">Postgrado</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Profesión, Oficio u Ocupación Actual <span class="text-rose-500">*</span></label>
                        <select name="jefe_ocupacion" x-model="form.jefe_ocupacion" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="">Seleccione una profesión u oficio</option>
                            <option value="Abogado">Abogado</option>
                            <option value="Administrador">Administrador</option>
                            <option value="Agricultor">Agricultor</option>
                            <option value="Albañil">Albañil</option>
                            <option value="Agrónomo">Agrónomo</option>
                            <option value="Ama de casa / Amo de casa">Ama de casa / Amo de casa</option>
                            <option value="Arquitecto">Arquitecto</option>
                            <option value="Artesano">Artesano</option>
                            <option value="Asesor de ventas">Asesor de ventas</option>
                            <option value="Asistente administrativo">Asistente administrativo</option>
                            <option value="Barbero">Barbero</option>
                            <option value="Bioanalista">Bioanalista</option>
                            <option value="Bombero">Bombero</option>
                            <option value="Cajero">Cajero</option>
                            <option value="Carpintero">Carpintero</option>
                            <option value="Chef / Cocinero">Chef / Cocinero</option>
                            <option value="Civil / Militar">Civil / Militar</option>
                            <option value="Comerciante / Dueño de negocio">Comerciante / Dueño de negocio</option>
                            <option value="Community Manager">Community Manager</option>
                            <option value="Conductor / Transportista">Conductor / Transportista</option>
                            <option value="Contador público">Contador público</option>
                            <option value="Costurero / Sastre">Costurero / Sastre</option>
                            <option value="Creador de contenido">Creador de contenido</option>
                            <option value="Desempleado">Desempleado</option>
                            <option value="Diseñador gráfico">Diseñador gráfico</option>
                            <option value="Docente / Profesor">Docente / Profesor</option>
                            <option value="Delivery / Repartidor">Delivery / Repartidor</option>
                            <option value="Electricista">Electricista</option>
                            <option value="Empleado público">Empleado público</option>
                            <option value="Enfermero">Enfermero</option>
                            <option value="Especialista en Recursos Humanos">Especialista en Recursos Humanos</option>
                            <option value="Estudiante">Estudiante</option>
                            <option value="Esteticista / Manicurista">Esteticista / Manicurista</option>
                            <option value="Farmacéutico">Farmacéutico</option>
                            <option value="Herrero">Herrero</option>
                            <option value="Ingeniero civil">Ingeniero civil</option>
                            <option value="Ingeniero de sistemas / computación">Ingeniero de sistemas / computación</option>
                            <option value="Ingeniero industrial">Ingeniero industrial</option>
                            <option value="Ingeniero mecánico">Ingeniero mecánico</option>
                            <option value="Ingeniero de petróleo">Ingeniero de petróleo</option>
                            <option value="Jubilado / Pensionado">Jubilado / Pensionado</option>
                            <option value="Mecánico automotriz">Mecánico automotriz</option>
                            <option value="Médico / Cirujano">Médico / Cirujano</option>
                            <option value="Mesero">Mesero</option>
                            <option value="Militar">Militar</option>
                            <option value="Odontólogo">Odontólogo</option>
                            <option value="Paramédico">Paramédico</option>
                            <option value="Peluquero">Peluquero</option>
                            <option value="Periodista / Comunicador Social">Periodista / Comunicador Social</option>
                            <option value="Personal de limpieza / mantenimiento">Personal de limpieza / mantenimiento</option>
                            <option value="Pescador">Pescador</option>
                            <option value="Plomero">Plomero</option>
                            <option value="Policía">Policía</option>
                            <option value="Productor agropecuario">Productor agropecuario</option>
                            <option value="Programador / Desarrollador de software">Programador / Desarrollador de software</option>
                            <option value="Psicólogo">Psicólogo</option>
                            <option value="Secretaria">Secretaria</option>
                            <option value="Soporte técnico digital">Soporte técnico digital</option>
                            <option value="Técnico en computación">Técnico en computación</option>
                            <option value="Técnico en electricidad">Técnico en electricidad</option>
                            <option value="Técnico en enfermería">Técnico en enfermería</option>
                            <option value="Técnico en mecánica">Técnico en mecánica</option>
                            <option value="Técnico en turismo">Técnico en turismo</option>
                            <option value="Terapeuta / Trabajador social">Terapeuta / Trabajador social</option>
                            <option value="Vendedor">Vendedor</option>
                            <option value="Veterinario">Veterinario</option>
                            <option value="Vigilante / Personal de seguridad">Vigilante / Personal de seguridad</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">¿Posee Carnet de la Patria? <span class="text-rose-500">*</span></label>
                        <select name="posee_carnet_patria" x-model="tieneCarnet" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="No">No</option>
                            <option value="Sí">Sí</option>
                        </select>
                    </div>
                    <template x-if="tieneCarnet === 'Sí'">
                        <div class="sm:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-indigo-50/50 border border-indigo-100 rounded-xl">
                            <div>
                                <label class="block text-xs font-bold text-indigo-900 mb-1">Código del Carnet <span class="text-rose-500">*</span></label>
                                <input type="text" name="codigo_carnet" x-model="form.codigo_carnet" maxlength="10" @keypress="if(!/[0-9]/.test(event.key)) event.preventDefault()" @input="$event.target.value = $event.target.value.replace(/\D/g, '')" class="w-full text-sm bg-white border-slate-200 rounded-xl p-2.5">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-indigo-900 mb-1">Serial del Carnet <span class="text-rose-500">*</span></label>
                                <input type="text" name="serial_carnet" x-model="form.serial_carnet" maxlength="10" @keypress="if(!/[0-9]/.test(event.key)) event.preventDefault()" @input="$event.target.value = $event.target.value.replace(/\D/g, '')" class="w-full text-sm bg-white border-slate-200 rounded-xl p-2.5">
                            </div>
                        </div>
                    </template>
                </div>
                <div class="flex justify-between pt-4 border-t border-slate-100">
                    <button type="button" @click="step = 1; errorMessage = ''" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-6 py-2.5 rounded-xl text-xs font-bold transition">← Anterior</button>
                    <button type="button" @click="nextStep()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-md transition">Siguiente paso →</button>
                </div>
            </div>

            <!-- ================= SECCIÓN III: VIVIENDA ================= -->
            <div x-show="step === 3" style="display: none;" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-xl space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-lg font-bold text-slate-800">Características de la Vivienda y Servicios</h3>
                    <p class="text-xs text-slate-400">Infraestructura habitacional y acceso a servicios públicos.</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tipo de Vivienda <span class="text-rose-500">*</span></label>
                        <select name="tipo_vivienda" x-model="form.tipo_vivienda" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="">Seleccione</option>
                            <option value="Casa">Casa</option>
                            <option value="Apartamento">Apartamento</option>
                            <option value="Habitación">Habitación</option>
                            <option value="Rancho">Rancho</option>
                            <option value="Quinta">Quinta</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Condición Jurídica <span class="text-rose-500">*</span></label>
                        <select name="condicion_juridica" x-model="form.condicion_juridica" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="">Seleccione</option>
                            <option value="Propia">Propia</option>
                            <option value="Alquilada">Alquilada</option>
                            <option value="Prestada">Prestada</option>
                            <option value="Compartida">Compartida</option>
                            <option value="Adjudicada/GMVV">Adjudicada / GMVV</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Estado de Infraestructura <span class="text-rose-500">*</span></label>
                        <select name="estado_infraestructura" x-model="form.estado_infraestructura" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="">Seleccione</option>
                            <option value="Excelente">Excelente</option>
                            <option value="Buena">Buena</option>
                            <option value="Regular">Regular</option>
                            <option value="Deteriorada">Deteriorada</option>
                            <option value="En riesgo">En riesgo</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Material Predominante en Paredes <span class="text-rose-500">*</span></label>
                        <select name="material_paredes" x-model="form.material_paredes" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="">Seleccione</option>
                            <option value="Bloque/Ladrillo">Bloque / Ladrillo</option>
                            <option value="Adobe/Bahareque">Adobe / Bahareque</option>
                            <option value="Madera">Madera</option>
                            <option value="Zinc/Láminas">Zinc / Láminas</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Material Predominante en Techo <span class="text-rose-500">*</span></label>
                        <select name="material_techo" x-model="form.material_techo" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="">Seleccione</option>
                            <option value="Platabanda">Platabanda</option>
                            <option value="Acerolit/Zinc">Acerolit / Zinc</option>
                            <option value="Asbesto">Asbesto</option>
                            <option value="Tejas">Tejas</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Abastecimiento de Agua Potable <span class="text-rose-500">*</span></label>
                        <select name="abastecimiento_agua" x-model="form.abastecimiento_agua" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="">Seleccione</option>
                            <option value="Tubería interna">Tubería interna</option>
                            <option value="Tubería comunal">Tubería comunal</option>
                            <option value="Camión cisterna">Camión cisterna</option>
                            <option value="Pozo">Pozo</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Sistema de Aguas Servidas <span class="text-rose-500">*</span></label>
                        <select name="aguas_servidas" x-model="form.aguas_servidas" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="">Seleccione</option>
                            <option value="Red de cloacas">Red de cloacas</option>
                            <option value="Pozo séptico">Pozo séptico</option>
                            <option value="Letrina">Letrina</option>
                            <option value="No posee">No posee</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Acceso al Gas Doméstico <span class="text-rose-500">*</span></label>
                        <select name="acceso_gas" x-model="form.acceso_gas" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="">Seleccione</option>
                            <option value="Bombona comunal">Bombona comunal</option>
                            <option value="Bombona comercial">Bombona comercial</option>
                            <option value="Gas directo por tubería">Gas directo por tubería</option>
                            <option value="No posee">No posee</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Empresa Proveedora de Gas <span class="text-rose-500">*</span></label>
                        <select name="empresa_gas" x-model="form.empresa_gas" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="">Seleccione</option>
                            <option value="Pdvsa Gas Comunal">Pdvsa Gas Comunal</option>
                            <option value="Empresa privada">Empresa privada</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Conexión Eléctrica <span class="text-rose-500">*</span></label>
                        <select name="conexion_electrica" x-model="form.conexion_electrica" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="">Seleccione</option>
                            <option value="Sistema legal con medidor">Sistema legal con medidor</option>
                            <option value="Toma ilegal/pájaro">Toma ilegal / "pájaro"</option>
                            <option value="Planta eléctrica">Planta eléctrica</option>
                            <option value="No posee">No posee</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Recolección de Aseo Urbano <span class="text-rose-500">*</span></label>
                        <select name="aseo_urbano" x-model="form.aseo_urbano" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="">Seleccione</option>
                            <option value="Recolección pública domiciliaria">Recolección pública domiciliaria</option>
                            <option value="Contenedor comunitario">Contenedor comunitario</option>
                            <option value="Quema/Entierra">Quema / Entierra</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-between pt-4 border-t border-slate-100">
                    <button type="button" @click="step = 2; errorMessage = ''" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-6 py-2.5 rounded-xl text-xs font-bold transition">← Anterior</button>
                    <button type="button" @click="nextStep()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-md transition">Siguiente paso →</button>
                </div>
            </div>

            <!-- ================= SECCIÓN IV: SOCIOECONÓMICO ================= -->
            <div x-show="step === 4" style="display: none;" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-xl space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-lg font-bold text-slate-800">Situación Socioeconómica General</h3>
                    <p class="text-xs text-slate-400">Beneficios sociales, ingresos familiares y condiciones alimentarias.</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">¿Recibe beneficio CLAP? <span class="text-rose-500">*</span></label>
                        <select name="recibe_clap" x-model="recibeClap" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="No">No</option>
                            <option value="Sí">Sí</option>
                        </select>
                    </div>
                    <template x-if="recibeClap === 'Sí'">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Frecuencia de entrega del CLAP <span class="text-rose-500">*</span></label>
                            <select name="frecuencia_clap" x-model="form.frecuencia_clap" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                                <option value="">Seleccione</option>
                                <option value="Mensual">Mensual</option>
                                <option value="Cada dos meses">Cada dos meses</option>
                                <option value="Cada tres meses o más">Cada tres meses o más</option>
                            </select>
                        </div>
                    </template>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Rango de Ingreso Familiar <span class="text-rose-500">*</span></label>
                        <select name="ingreso_familiar" x-model="form.ingreso_familiar" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="">Seleccione</option>
                            <option value="Menos de un salario mínimo">Menos de un salario mínimo</option>
                            <option value="Entre 1 y 2 salarios mínimos">Entre 1 y 2 salarios mínimos</option>
                            <option value="Más de 2 salarios mínimos">Más de 2 salarios mínimos</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">¿Recibe remesas del exterior? <span class="text-rose-500">*</span></label>
                        <select name="recibe_remesas" x-model="recibeRemesas" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="No">No</option>
                            <option value="Sí">Sí</option>
                        </select>
                    </div>
                    <template x-if="recibeRemesas === 'Sí'">
                        <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-indigo-50/50 border border-indigo-100 rounded-xl">
                            <div>
                                <label class="block text-xs font-bold text-indigo-900 mb-1">Monto aproximado ($) <span class="text-rose-500">*</span></label>
                                <input type="text" name="monto_remesas" x-model="form.monto_remesas" @keypress="if(!/[0-9]/.test(event.key)) event.preventDefault()" @input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '')" placeholder="Ej: 50" class="w-full text-sm bg-white border-slate-200 rounded-xl p-2.5">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-indigo-900 mb-1">Frecuencia <span class="text-rose-500">*</span></label>
                                <select name="frecuencia_remesas" x-model="form.frecuencia_remesas" class="w-full text-sm bg-white border-slate-200 rounded-xl p-2.5">
                                    <option value="">Seleccione</option>
                                    <option value="Semanal">Semanal</option>
                                    <option value="Quincenal">Quincenal</option>
                                    <option value="Mensual">Mensual</option>
                                    <option value="Ocasional">Ocasional</option>
                                </select>
                            </div>
                        </div>
                    </template>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">¿Dificultades para adquirir canasta básica? <span class="text-rose-500">*</span></label>
                        <select name="dificultad_canasta" x-model="dificultadCanasta" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="No">No</option>
                            <option value="Sí">Sí</option>
                        </select>
                    </div>
                    <template x-if="dificultadCanasta === 'Sí'">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Especifique el motivo principal <span class="text-rose-500">*</span></label>
                            <input type="text" name="motivo_dificultad_canasta" x-model="form.motivo_dificultad_canasta" placeholder="Ej: Altos precios, falta de empleo..." class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                        </div>
                    </template>
                </div>
                <div class="flex justify-between pt-4 border-t border-slate-100">
                    <button type="button" @click="step = 3; errorMessage = ''" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-6 py-2.5 rounded-xl text-xs font-bold transition">← Anterior</button>
                    <button type="button" @click="nextStep()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-md transition">Siguiente paso →</button>
                </div>
            </div>

            <!-- ================= SECCIÓN V: CARGA FAMILIAR ================= -->
            <div x-show="step === 5" style="display: none;" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-xl space-y-6">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Carga Familiar</h3>
                        <p class="text-xs text-slate-400">Registro detallado de los integrantes que habitan en el hogar.</p>
                    </div>
                    <button type="button" @click="miembros.push({nombre: '', nacion: 'V', cedula: '', menor: false, parentesco: '', sexo: '', fecha_nac: '', edad: '', nivel_ed: '', ocupacion: '', tiene_discapacidad: 'No', discapacidad: '', otra_discapacidad: ''})" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2 rounded-xl shadow transition">
                        + Añadir Integrante
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(miembro, index) in miembros" :key="index">
                        <div class="p-5 bg-slate-50/70 border border-slate-200/80 rounded-2xl space-y-4 relative">
                            <button type="button" @click="miembros.splice(index, 1)" class="absolute top-4 right-4 text-rose-500 font-bold text-xs hover:underline">Eliminar</button>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Nombres y Apellidos <span class="text-rose-500">*</span></label>
                                    <input type="text" x-model="miembro.nombre" :name="'familiares['+index+'][nombre]'" @keypress="if(!/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]$/.test(event.key)) event.preventDefault()" @input="$event.target.value = $event.target.value.replace(/[^A-Za-zñÑáéíóúÁÉÍÓÚ\s]/g, '')" class="w-full text-sm bg-white border-slate-200 rounded-xl p-2.5">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Cédula / Menor de edad <span class="text-rose-500">*</span></label>
                                    <div class="flex gap-2 items-center">
                                        <template x-if="!miembro.menor">
                                            <div class="flex gap-1 w-full">
                                                <select x-model="miembro.nacion" :name="'familiares['+index+'][nacionalidad]'" class="text-xs bg-white border-slate-200 rounded-xl w-16 p-2.5">
                                                    <option value="V">V</option>
                                                    <option value="E">E</option>
                                                </select>
                                                <input type="text" x-model="miembro.cedula" :name="'familiares['+index+'][cedula]'" maxlength="8" @keypress="if(!/[0-9]/.test(event.key)) event.preventDefault()" @input="$event.target.value = $event.target.value.replace(/\D/g, '')" placeholder="Ej: 12345678" class="w-full text-sm bg-white border-slate-200 rounded-xl p-2.5">
                                            </div>
                                        </template>
                                        <template x-if="miembro.menor">
                                            <input type="text" value="Menor sin cédula" readonly class="w-full text-sm bg-slate-200 text-slate-500 border-slate-200 rounded-xl p-2.5">
                                        </template>
                                    </div>
                                    <label class="flex items-center gap-1.5 mt-1.5 text-xs text-slate-600 font-medium">
                                        <input type="checkbox" x-model="miembro.menor" @change="if(miembro.menor && !['Hijo', 'Nieto', 'Hermano'].includes(miembro.parentesco)) miembro.parentesco = ''" :name="'familiares['+index+'][es_menor]'" value="1" class="rounded text-indigo-600 focus:ring-indigo-500"> Es menor de edad
                                    </label>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Parentesco <span class="text-rose-500">*</span></label>
                                    <select x-model="miembro.parentesco" :name="'familiares['+index+'][parentesco]'" class="w-full text-sm bg-white border-slate-200 rounded-xl p-2.5">
                                        <option value="">Seleccione</option>
                                        <template x-if="miembro.menor">
                                            <template x-for="p in ['Hijo', 'Nieto', 'Hermano']">
                                                <option :value="p" x-text="p"></option>
                                            </template>
                                        </template>
                                        <template x-if="!miembro.menor">
                                            <template x-for="p in ['Hijo', 'Cónyuge', 'Madre', 'Padre', 'Nieto', 'Hermano', 'Otro']">
                                                <option :value="p" x-text="p"></option>
                                            </template>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Sexo <span class="text-rose-500">*</span></label>
                                    <select x-model="miembro.sexo" :name="'familiares['+index+'][sexo]'" class="w-full text-sm bg-white border-slate-200 rounded-xl p-2.5">
                                        <option value="">Seleccione</option>
                                        <option value="Masculino">Masculino</option>
                                        <option value="Femenino">Femenino</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Fecha de Nacimiento <span class="text-rose-500">*</span></label>
                                    <input type="date" x-model="miembro.fecha_nac" @change="calcularEdadMiembro(index)" :max="new Date().toISOString().split('T')[0]" :name="'familiares['+index+'][fecha_nacimiento]'" class="w-full text-sm bg-white border-slate-200 rounded-xl p-2.5">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Edad Actual</label>
                                    <input type="text" x-model="miembro.edad" :name="'familiares['+index+'][edad]'" readonly class="w-full text-sm bg-slate-100 border-slate-200 rounded-xl text-slate-500 p-2.5">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Nivel Educativo <span class="text-rose-500">*</span></label>
                                    <select x-model="miembro.nivel_ed" :name="'familiares['+index+'][nivel_educativo]'" class="w-full text-sm bg-white border-slate-200 rounded-xl p-2.5">
                                        <option value="">Seleccione</option>
                                        <option value="Ninguno">Ninguno</option>
                                        <option value="Preescolar">Preescolar</option>
                                        <option value="Primaria">Primaria</option>
                                        <option value="Secundaria">Secundaria</option>
                                        <option value="Superior">Superior</option>
                                        <option value="Postgrado">Postgrado</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Ocupación / Si estudia</label>
                                    <select x-model="miembro.ocupacion" :name="'familiares['+index+'][ocupacion]'" class="w-full text-sm bg-white border-slate-200 rounded-xl p-2.5">
                                        <option value="">Seleccione una ocupación</option>
                                        <option value="No aplica">No aplica</option>
                                        <option value="Abogado">Abogado</option>
                                        <option value="Administrador">Administrador</option>
                                        <option value="Agricultor">Agricultor</option>
                                        <option value="Albañil">Albañil</option>
                                        <option value="Agrónomo">Agrónomo</option>
                                        <option value="Ama de casa / Amo de casa">Ama de casa / Amo de casa</option>
                                        <option value="Arquitecto">Arquitecto</option>
                                        <option value="Artesano">Artesano</option>
                                        <option value="Asesor de ventas">Asesor de ventas</option>
                                        <option value="Asistente administrativo">Asistente administrativo</option>
                                        <option value="Barbero">Barbero</option>
                                        <option value="Bioanalista">Bioanalista</option>
                                        <option value="Bombero">Bombero</option>
                                        <option value="Cajero">Cajero</option>
                                        <option value="Carpintero">Carpintero</option>
                                        <option value="Chef / Cocinero">Chef / Cocinero</option>
                                        <option value="Civil / Militar">Civil / Militar</option>
                                        <option value="Comerciante / Dueño de negocio">Comerciante / Dueño de negocio</option>
                                        <option value="Community Manager">Community Manager</option>
                                        <option value="Conductor / Transportista">Conductor / Transportista</option>
                                        <option value="Contador público">Contador público</option>
                                        <option value="Costurero / Sastre">Costurero / Sastre</option>
                                        <option value="Creador de contenido">Creador de contenido</option>
                                        <option value="Desempleado">Desempleado</option>
                                        <option value="Diseñador gráfico">Diseñador gráfico</option>
                                        <option value="Docente / Profesor">Docente / Profesor</option>
                                        <option value="Delivery / Repartidor">Delivery / Repartidor</option>
                                        <option value="Electricista">Electricista</option>
                                        <option value="Empleado público">Empleado público</option>
                                        <option value="Enfermero">Enfermero</option>
                                        <option value="Especialista en Recursos Humanos">Especialista en Recursos Humanos</option>
                                        <option value="Estudiante">Estudiante</option>
                                        <option value="Esteticista / Manicurista">Esteticista / Manicurista</option>
                                        <option value="Farmacéutico">Farmacéutico</option>
                                        <option value="Herrero">Herrero</option>
                                        <option value="Ingeniero civil">Ingeniero civil</option>
                                        <option value="Ingeniero de sistemas / computación">Ingeniero de sistemas / computación</option>
                                        <option value="Ingeniero industrial">Ingeniero industrial</option>
                                        <option value="Ingeniero mecánico">Ingeniero mecánico</option>
                                        <option value="Ingeniero de petróleo">Ingeniero de petróleo</option>
                                        <option value="Jubilado / Pensionado">Jubilado / Pensionado</option>
                                        <option value="Mecánico automotriz">Mecánico automotriz</option>
                                        <option value="Médico / Cirujano">Médico / Cirujano</option>
                                        <option value="Mesero">Mesero</option>
                                        <option value="Militar">Militar</option>
                                        <option value="Odontólogo">Odontólogo</option>
                                        <option value="Paramédico">Paramédico</option>
                                        <option value="Peluquero">Peluquero</option>
                                        <option value="Periodista / Comunicador Social">Periodista / Comunicador Social</option>
                                        <option value="Personal de limpieza / mantenimiento">Personal de limpieza / mantenimiento</option>
                                        <option value="Pescador">Pescador</option>
                                        <option value="Plomero">Plomero</option>
                                        <option value="Policía">Policía</option>
                                        <option value="Productor agropecuario">Productor agropecuario</option>
                                        <option value="Programador / Desarrollador de software">Programador / Desarrollador de software</option>
                                        <option value="Psicólogo">Psicólogo</option>
                                        <option value="Secretaria">Secretaria</option>
                                        <option value="Soporte técnico digital">Soporte técnico digital</option>
                                        <option value="Técnico en computación">Técnico en computación</option>
                                        <option value="Técnico en electricidad">Técnico en electricidad</option>
                                        <option value="Técnico en enfermería">Técnico en enfermería</option>
                                        <option value="Técnico en mecánica">Técnico en mecánica</option>
                                        <option value="Técnico en turismo">Técnico en turismo</option>
                                        <option value="Terapeuta / Trabajador social">Terapeuta / Trabajador social</option>
                                        <option value="Vendedor">Vendedor</option>
                                        <option value="Veterinario">Veterinario</option>
                                        <option value="Vigilante / Personal de seguridad">Vigilante / Personal de seguridad</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">¿Padece enfermedad o discapacidad? <span class="text-rose-500">*</span></label>
                                    <select x-model="miembro.tiene_discapacidad" :name="'familiares['+index+'][tiene_discapacidad]'" class="w-full text-sm bg-white border-slate-200 rounded-xl p-2.5">
                                        <option value="No">No</option>
                                        <option value="Sí">Sí</option>
                                    </select>
                                </div>

                                <template x-if="miembro.tiene_discapacidad === 'Sí'">
                                    <div class="sm:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-white border border-indigo-100 rounded-xl">
                                        <div>
                                            <label class="block text-xs font-bold text-indigo-900 mb-1">Patología o Condición <span class="text-rose-500">*</span></label>
                                            <select x-model="miembro.discapacidad" :name="'familiares['+index+'][discapacidad]'" class="w-full text-sm bg-slate-50 border-slate-200 rounded-xl p-2.5">
                                                <option value="">Seleccione</option>
                                                <option value="Diabetes">Diabetes</option>
                                                <option value="Hipertensión arterial">Hipertensión arterial</option>
                                                <option value="Asma">Asma</option>
                                                <option value="Discapacidad Motora">Discapacidad Motora</option>
                                                <option value="Discapacidad Intelectual">Discapacidad Intelectual</option>
                                                <option value="Insuficiencia Renal">Insuficiencia Renal</option>
                                                <option value="Cardiopatía">Cardiopatía</option>
                                                <option value="Otra">Otra (especificar)</option>
                                            </select>
                                        </div>
                                        <template x-if="miembro.discapacidad === 'Otra'">
                                            <div>
                                                <label class="block text-xs font-bold text-indigo-900 mb-1">Especifique cuál <span class="text-rose-500">*</span></label>
                                                <input type="text" x-model="miembro.otra_discapacidad" :name="'familiares['+index+'][otra_discapacidad]'" placeholder="Describa la condición" class="w-full text-sm bg-slate-50 border-slate-200 rounded-xl p-2.5">
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="miembros.length === 0" class="text-center py-8 text-slate-400 text-xs border-2 border-dashed border-slate-200 rounded-2xl">
                    No hay carga familiar agregada. Puedes continuar si vives solo o añadir integrantes arriba.
                </div>

                <div class="flex justify-between pt-4 border-t border-slate-100">
                    <button type="button" @click="step = 4; errorMessage = ''" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-6 py-2.5 rounded-xl text-xs font-bold transition">← Anterior</button>
                    <button type="button" @click="nextStep()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-md transition">Siguiente paso →</button>
                </div>
            </div>

            <!-- ================= SECCIÓN VI: SALUD Y VULNERABILIDAD ================= -->
            <div x-show="step === 6" style="display: none;" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-xl space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-lg font-bold text-slate-800">Salud y Grupos Vulnerables</h3>
                    <p class="text-xs text-slate-400">Identificación de sectores prioritarios o casos médicos delicados en el hogar.</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">¿Hay mujeres embarazadas en el hogar? <span class="text-rose-500">*</span></label>
                        <select name="embarazadas_status" x-model="embarazadas" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="No">No</option>
                            <option value="Sí">Sí</option>
                        </select>
                    </div>
                    <template x-if="embarazadas === 'Sí'">
                        <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-indigo-50/50 border border-indigo-100 rounded-xl">
                            <div>
                                <label class="block text-xs font-bold text-indigo-900 mb-1">Cantidad de embarazadas <span class="text-rose-500">*</span></label>
                                <input type="number" name="embarazadas_cantidad" x-model="form.embarazadas_cantidad" min="1" class="w-full text-sm bg-white border-slate-200 rounded-xl p-2.5">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-indigo-900 mb-1">¿Cuentan con control médico? <span class="text-rose-500">*</span></label>
                                <select name="embarazadas_control" x-model="form.embarazadas_control" class="w-full text-sm bg-white border-slate-200 rounded-xl p-2.5">
                                    <option value="">Seleccione</option>
                                    <option value="Sí">Sí</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                        </div>
                    </template>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">¿Niños lactantes (0 a 2 años)? <span class="text-rose-500">*</span></label>
                        <select name="lactantes_status" x-model="lactantes" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="No">No</option>
                            <option value="Sí">Sí</option>
                        </select>
                    </div>
                    <template x-if="lactantes === 'Sí'">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Cantidad de lactantes <span class="text-rose-500">*</span></label>
                            <input type="number" name="lactantes_cantidad" x-model="form.lactantes_cantidad" min="1" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                        </div>
                    </template>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">¿Adultos mayores de 60 años? <span class="text-rose-500">*</span></label>
                        <select name="adultos_status" x-model="adultos" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="No">No</option>
                            <option value="Sí">Sí</option>
                        </select>
                    </div>
                    <template x-if="adultos === 'Sí'">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Cantidad de adultos mayores <span class="text-rose-500">*</span></label>
                            <input type="number" name="adultos_cantidad" x-model="form.adultos_cantidad" min="1" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                        </div>
                    </template>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">¿Personas encamadas o con movilidad reducida? <span class="text-rose-500">*</span></label>
                        <select name="encamados_status" x-model="encamados" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="No">No</option>
                            <option value="Sí">Sí</option>
                        </select>
                    </div>
                    <template x-if="encamados === 'Sí'">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Cantidad de personas <span class="text-rose-500">*</span></label>
                            <input type="number" name="encamados_cantidad" x-model="form.encamados_cantidad" min="1" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                        </div>
                    </template>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">¿Enfermedades crónicas generales? <span class="text-rose-500">*</span></label>
                        <select name="enfermedades_cronicas_status" x-model="cronicas" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="No">No</option>
                            <option value="Sí">Sí</option>
                        </select>
                    </div>
                    <template x-if="cronicas === 'Sí'">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Especifique patologías <span class="text-rose-500">*</span></label>
                            <input type="text" name="enfermedades_cronicas_detalle" x-model="form.enfermedades_cronicas_detalle" placeholder="Diabetes, Hipertensión..." class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                        </div>
                    </template>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">¿Registrados en CONAPDIS? <span class="text-rose-500">*</span></label>
                        <select name="conapdis" x-model="form.conapdis" class="w-full text-sm border-slate-200 rounded-xl p-2.5">
                            <option value="">Seleccione</option>
                            <option value="No aplica">No aplica</option>
                            <option value="Sí">Sí</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-between pt-4 border-t border-slate-100">
                    <button type="button" @click="step = 5; errorMessage = ''" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-6 py-2.5 rounded-xl text-xs font-bold transition">← Anterior</button>
                    <button type="button" @click="nextStep()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-md transition">Siguiente paso →</button>
                </div>
            </div>

            <!-- ================= SECCIÓN VII: CIERRE ================= -->
            <div x-show="step === 7" style="display: none;" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-xl space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-lg font-bold text-slate-800">Cierre y Validación</h3>
                    <p class="text-xs text-slate-400">Observaciones finales por parte del empadronador o vocero.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Observaciones (Extrema vulnerabilidad, emergencias o comentarios)</label>
                    <textarea name="observaciones" rows="4" class="w-full text-sm border-slate-200 rounded-xl p-3 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Indique detalles relevantes sobre la infraestructura, situación crítica o notas adicionales..."></textarea>
                </div>

                <div class="flex justify-between pt-4 border-t border-slate-100">
                    <button type="button" @click="step = 6; errorMessage = ''" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-6 py-2.5 rounded-xl text-xs font-bold transition">← Anterior</button>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3 rounded-xl text-xs font-bold shadow-lg transition">
                        Guardar Censo Completo ✓
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Script de lógica Alpine.js con los 24 estados y sus municipios/parroquias representativos -->
    <script>
        function censusForm() {
            return {
                step: 1,
                errorMessage: '',
                jefeEdad: '',
                tieneCarnet: 'No',
                recibeClap: 'No',
                recibeRemesas: 'No',
                dificultadCanasta: 'No',
                embarazadas: 'No',
                lactantes: 'No',
                adultos: 'No',
                encamados: 'No',
                cronicas: 'No',
                miembros: [],
                form: {
                    consejo_comunal: 'Consejo Comunal “El Libertador del Siglo XXI”',
                    otro_consejo_comunal: '',
                    estado: 'Distrito Capital',
                    municipio: 'Libertador',
                    parroquia: 'Sucre',
                    sector_calle: '',
                    numero_vivienda_dir: '',
                    fecha_censo: '{{ date("Y-m-d") }}',
                    jefe_nombre: '',
                    jefe_nacionalidad: 'V',
                    jefe_cedula: '',
                    jefe_fecha_nacimiento: '',
                    jefe_sexo: '',
                    jefe_estado_civil: '',
                    jefe_telefono: '',
                    jefe_telefono_alt: '',
                    jefe_instruccion: '',
                    jefe_ocupacion: '',
                    codigo_carnet: '',
                    serial_carnet: '',
                    tipo_vivienda: '',
                    condicion_juridica: '',
                    estado_infraestructura: '',
                    material_paredes: '',
                    material_techo: '',
                    abastecimiento_agua: '',
                    aguas_servidas: '',
                    acceso_gas: '',
                    empresa_gas: '',
                    conexion_electrica: '',
                    aseo_urbano: '',
                    frecuencia_clap: '',
                    ingreso_familiar: '',
                    monto_remesas: '',
                    frecuencia_remesas: '',
                    motivo_dificultad_canasta: '',
                    embarazadas_cantidad: '',
                    embarazadas_control: '',
                    lactantes_cantidad: '',
                    adultos_cantidad: '',
                    encamados_cantidad: '',
                    enfermedades_cronicas_detalle: '',
                    conapdis: ''
                },
                // Data completa de los 24 estados, municipios y parroquias de Venezuela
                veData: {
                    "Amazonas": {
                        "Atabapo": ["San Fernando de Atabapo", "Anabaru", "Loe", "Macuruco"],
                        "Atures": ["Fernando Girón Tovar", "Luis Alberto Gómez", "Parhueña", "Playa de Mapure"],
                        "Autana": ["Isla Ratón", "Guayapo", "Munduapo", "Samariapo"],
                        "Manapiare": ["San Juan de Manapiare", "Alto Ventuari", "Bajo Ventuari", "Concepción"],
                        "Maroa": ["Maroa", "Victorino"],
                        "Río Negro": ["San Carlos de Río Negro", "Solano", "Casiquiare", "Cocui"],
                        "Simón Rodríguez": ["San Fernando de Atabapo"]
                    },
                    "Anzoátegui": {
                        "Anaco": ["Anaco", "San Joaquín"],
                        "Aragua": ["Aragua de Barcelona", "Cachipo"],
                        "Bolívar": ["Barcelona", "El Carmen", "San Cristóbal", "Bergantín", "Caigua", "El Pilar", "Naricual"],
                        "Bruzual": ["Clarines", "Guanape", "Sabana de Uchire"],
                        "Carvajal": ["Valle de Guanape", "Santa Bárbara"],
                        "Cajigal": ["Onoto", "Sereure"],
                        "Freites": ["Cantaura", "Libertador", "Santa Rosa", "Urica"],
                        "Guanipa": ["San José de Guanipa"],
                        "Guanta": ["Guanta", "Chorrerón"],
                        "Independencia": ["Soledad", "Mamo"],
                        "McGregor": ["El Chaparro", "Tigrito", "Tomas Alfaro Calatrava"],
                        "Miranda": ["Pariaguán", "Atapirire", "El Pao", "Mantecal"],
                        "Monagas": ["Mapire", "Piar", "Santa Clara", "San Diego de Cabrutica"],
                        "Peñalver": ["Puerto Píritu", "San Miguel", "Sucre"],
                        "Píritu": ["Píritu", "San Francisco"],
                        "Rincón": ["Aparedo"],
                        "San Juan de Capistrano": ["Boca de Uchire", "Boca de Chávez"],
                        "Santa Ana": ["Santa Ana", "Pueblo Nuevo"],
                        "Simón Bolívar": ["El Carmen", "San Cristóbal", "Bergantín", "Caigua", "El Pilar", "Naricual"],
                        "Sir Arthur McGregor": ["El Chaparro", "Tomas Alfaro Calatrava"],
                        "Sotillo": ["Puerto La Cruz", "Pozuelos"],
                        "Guanipa": ["San José de Guanipa"],
                        "Piar": ["El Macal", "Casigua"],
                        "Sucre": ["Boca de Uchire"],
                        "Valera": ["Valera"],
                        "Zaraza": ["Zaraza"],
                        "Lechería (Urbaneja)": ["Lechería", "El Morro"]
                    },
                    "Apure": {
                        "Achaguas": ["Achaguas", "Apurito", "El Yagual", "Guachara", "Mucuritas", "Queseras del Medio"],
                        "Biruaca": ["Biruaca"],
                        "Muñoz": ["M trục", "El Amparo", "San Vicente", "Rómulo Gallegos"],
                        "Páez": ["Guasdualito", "Aramendi", "El Amparo", "San Camilo", "Urdaneta"],
                        "Pedro Camejo": ["San Juan de Payara", "Codazzi", "Cunaviche"],
                        "Rómulo Gallegos": ["Elorza", "La Trinidad"],
                        "San Fernando": ["San Fernando", "El Recreo", "Peñalver", "San Rafael de Atamaica"]
                    },
                    "Aragua": {
                        "Bolívar": ["San Mateo"],
                        "Camatagua": ["Camatagua", "Carmen de Cura"],
                        "Girardot": ["Maracay", "Choroní", "Madre María de San José", "Joaquín Crespo", "José Casanova Godoy", "Andrés Eloy Blanco", "Los Tacariguas"],
                        "José Ángel Lamas": ["Santa Cruz"],
                        "José Félix Ribas": ["La Victoria", "Castor Nieves Ríos", "Las Guacamayas", "Pao de Zárate", "Zuata"],
                        "José Rafael Revenga": ["El Consejo"],
                        "Libertador": ["Palo Negro", "San Martín de Porres"],
                        "Mario Briceño Iragorry": ["El Limón", "Caña de Azúcar"],
                        "Ocumare de la Costa de Oro": ["Ocumare de la Costa"],
                        "San Casimiro": ["San Casimiro", "Güiripa", "Onoto", "Valle Morín"],
                        "San Sebastián": ["San Sebastián"],
                        "Santiago Mariño": ["Turmero", "Arevalo Aponte", "Chuao", "Samán de Güere", "Alfredo Pacheco Miranda"],
                        "Tovar": ["Colonia Tovar"],
                        "Urdaneta": ["Barbacoas", "Las Peñitas", "San Francisco de Asís", "Tamanaco"],
                        "Zamora": ["Villa de Cura", "Magdalena", "San Francisco de Asís", "Valles de Tucutunemo", "Pino Caimán"]
                    },
                    "Barinas": {
                        "Alberto Arvelo Torrealba": ["Sabaneta", "Rodriguez Domínguez"],
                        "Andrés Eloy Blanco": ["El Cantón", "Santa Cruz de Guacas", "Puerto Vivas"],
                        "Antonio José de Sucre": ["Ticoporo", "Andrés Bello", "Nicolás Pulido"],
                        "Arismendi": ["Arismendi", "Guadarrama", "La Unión", "San Antonio"],
                        "Barinas": ["Barinas", "Alto Barinas", "Corazón de Jesús", "Rómulo Betancourt", "San Silvestre", "Santa Inés", "Santa Lucía", "TDS"],
                        "Bolívar": ["Barinitas", "Altamira de Cáceres", "Calderas"],
                        "Cruz Paredes": ["Barrancas", "El Socorro", "Masparro"],
                        "Ezequiel Zamora": ["Santa Bárbara", "Encontrados", "Pedro Briceño Méndez", "San Ramón de Guanay"],
                        "Obispos": ["Obispos", "Guadarrama", "El Real", "La Luz"],
                        "Pedraza": ["Ciudad Bolivia", "Ignacio Briceño", "Paez", "José Felix Ribas"],
                        "Rojas": ["Libertad", "Dolores", "Santa Rosa", "Palacio Fajardo", "Simón Rodríguez"],
                        "Sosa": ["Ciudad de Nutrias", "El Nula", "Puerto Nutrias", "El Regalo"]
                    },
                    "Bolívar": {
                        "Caroní": ["Puerto Ordaz", "San Félix", "Cachamay", "Chirica", "Dalla Costa", "Unare", "Simón Bolívar", "Yocoima", "Pozo Verde"],
                        "Cedeño": ["Caicara del Orinoco", "Ascensión Farreras", "Altagracia", "Pijiguaos", "La Urbana"],
                        "El Callao": ["El Callao"],
                        "Gran Sabana": ["Santa Elena de Uairén", "Ikabarú"],
                        "Heres": ["Ciudad Bolívar", "Catedral", "Zea", "Orinoco", "José Antonio Páez", "Marhuanta", "Agua Salada", "Vista Hermosa", "Panapana"],
                        "Padre Pedro Chien": ["El Palmar", "Andrés Eloy Blanco"],
                        "Piar": ["Upata", "Andrés Eloy Blanco", "Chima", "Pedro Cova"],
                        "Ravelo": ["Angostura"],
                        "Rosario": ["Guasipati"],
                        "Sifontes": ["Tumeremo", "Dalla Costa", "San Isidro"],
                        "Sucre": ["Maripa", "Aripao", "Guarataro", "las Majadas", "Moitaco"]
                    },
                    "Carabobo": {
                        "Bejuma": ["Bejuma", "Canoabo", "Simón Bolívar"],
                        "Carlos Arvelo": ["Güigüe", "Tacarigua", "Belén"],
                        "Diego Ibarra": ["Mariara", "Aguas Calientes"],
                        "Guacara": ["Guacara", "Yagua", "Ciudad Alianza"],
                        "Montalban": ["Montalbán"],
                        "Naguanagua": ["Naguanagua"],
                        "Puerto Cabello": ["Puerto Cabello", "Borburata", "Patanemo", "Democracia", "Fraternidad", "Goaigoaza", "Juan José Flores", "Bartolomé Salom"],
                        "San Diego": ["San Diego"],
                        "San Joaquín": ["San Joaquín"],
                        "Valencia": ["Candelaria", "Catedral", "El Socorro", "Miguel Peña", "San Blas", "Santa Rosa", "Rafael Urdaneta", "Naguanagua"],
                        "Miranda": ["Miranda"]
                    },
                    "Cojedes": {
                        "Anzoátegui": ["Cojedeño", "El Baúl"],
                        "Falcón": ["Tinaquillo"],
                        "Girardot": ["El Pao"],
                        "Lima Blanco": ["Macapo", "La Pica"],
                        "Ricaurte": ["Libertad", "El Amparo"],
                        "Rómulo Gallegos": ["Las Vegas"],
                        "San Carlos": ["San Carlos de Cojedes", "Juan Ángel Bravo", "Manuel Manrique"],
                        "Tinaco": ["Tinaco", "General en Jefe José Laurencio Silva"]
                    },
                    "Delta Amacuro": {
                        "Antonio Díaz": ["Curiapo", "Amacuro", "Barra de Cuyuni", "Cusubeni", "Manuel Renaud", "San José", "Sucre"],
                        "Casacoima": ["Sierra Imataca", "Juan Bautista Arismendi", "Imataca", "Rómulo Gallegos"],
                        "M<bos>5": ["Pedernales", "Curiapo"],
                        "Tucupita": ["Tucupita", "San José", "San Rafael", "Virgen del Valle", "Altagracia", "Monseñor Argüello", "Tres Mártires", "San Juan de Unare"]
                    },
                    "Distrito Capital": {
                        "Libertador": ["Sucre", "23 de Enero", "Altagracia", "Candelaria", "Catedral", "El Recreo", "El Valle", "Coche", "La Pastora", "San Bernardino", "San José", "San Juan", "San Pedro", "Santa Rosalía", "Santa Teresa", "Antímano", "Caricuao", "El Junquito", "Macarao"]
                    },
                    "Falcón": {
                        "Acosta": ["San Juan de los Cayos", "Capadare", "La Pastora", "El Charal"],
                        "Bolívar": ["San Luis", "Aracua", "El Paují"],
                        "Buchivacoa": ["Capatárida", "Bariro", "Borojó", "Seque", "Guajiro", "Pueblo Acarigua", "Vistabella"],
                        "Carirubana": ["Punta Cardón", "Carirubana", "Nuestra Señora de Coromoto", "Santa Ana", "San Gabriel"],
                        "Cura": ["Cura"],
                        "Dabajuro": ["Dabajuro"],
                        "Democracia": ["Pedregal", "Agua Clara", "Avaria", "Piedra Grande", "Purureche"],
                        "Falcón": ["Pueblo Nuevo", "Adaure", "Adícora", "Baraived", "Buena Vista", "Jadacaquiva", "El Vínculo", "El Hato", "Moruy"],
                        "Federación": ["Churuguara", "Agua Larga", "El Paují", "Independencia", "Mapararí"],
                        "Jacura": ["Jacura", "Agua Linda", "Araurima"],
                        "Los Taques": ["Santa Cruz de Bucaral", "El Negrito", "El Paraíso"],
                        "Mauroa": ["Mene de Mauroa", "San Félix", "Casigua"],
                        "Miranda": ["Coro", "Guzmán Guillermo", "Mitare", "Río Seco", "Sabaneta", "San Antonio", "San Gabriel"],
                        "Monseñor Iturriza": ["Chichiriviche", "Boca de Tocuyo", "Tocopure"],
                        "Palmasola": ["Palmasola"],
                        "Petit": ["Cabimas", "Colina", "Curimagua"],
                        "Piritu": ["Píritu", "San José de la Costa"],
                        "San Francisco": ["San Francisco"],
                        "Silva": ["Tucacas", "Boca de Aroa"],
                        "Sucre": ["La Vela de Coro", "Acurigua", "Guaibacoa", "Las Calderas", "Macucuar", "Pecaya"],
                        "Tocuyo": ["Tocuyo de la Costa"],
                        "Unión": ["Santa Cruz de Bucaral", "El Charal", "Las Vegas del Tuy"],
                        "Urumaco": ["Urumaco", "Bruzual"],
                        "Zamora": ["Puerto Cumarebo", "La Cienaga", "La Soledad", "Pueblo Cumarebo", "Zazarida"]
                    },
                    "Guárico": {
                        "Camaguán": ["Camaguán", "Puerto Miranda", "Uverito"],
                        "Chaguaramas": ["Chaguaramas"],
                        "El Socorro": ["El Socorro"],
                        "Francisco de Miranda": ["Calabozo", "El Rastro", "Guardatinajas", "Zaraza"],
                        "José Félix Ribas": ["Tucupido", "San Rafael de Laya"],
                        "José Tadeo Monagas": ["Altagracia de Orituco", "San Francisco de Macaira", "San Rafael de Orituco", "Paso Real de Macaira", "Lezama", "Libertad de Orituco", "Cantagallo"],
                        "Juan Germán Roscio": ["San Juan de los Morros", "Cantagallo", "Parapara"],
                        "Julián Mellado": ["El Sombrero", "Sosa"],
                        "Las Mercedes": ["Las Mercedes del Llano", "Cabruta", "Laguas de Unare"],
                        "Ortíz": ["Ortíz", "San Francisco de Tiznados", "El Pao de Tiznados", "Aguas Negras"],
                        "Pedro Zaraza": ["Zaraza", "San José de Unare"],
                        "San Gerónimo de Guayabal": ["Guayabal", "Cazorla"],
                        "San José de Guaribe": ["San José de Guaribe"],
                        "Santa María de Ipire": ["Santa María de Ipire", "Altamira"]
                    },
                    "Lara": {
                        "Andrés Eloy Blanco": ["Sanare", "Pio Tamayo"],
                        "Crespo": ["Duaca", "Freitez", "El Eneal"],
                        "Iribarren": ["Barquisimeto", "Aguedo Felipe Alvarado", "Catedral", "Concepción", "El Cují", "Juan de Villegas", "Mata de Guadua", "Santa Rosa", "Tamaca"],
                        "Jiménez": ["Quíbor", "Catedral", "Cuara", "Diego de Lozada", "Paraíso de Charaima", "San Miguel", "Tintorero", "José Bernardo Dorante"],
                        "Morán": ["El Tocuyo", "Anzoátegui", "Bolívar", "Guárico", "Hilario Luna y Luna", "La Candelaria", "Morán", "San José"],
                        "Palavecino": ["Cabudare", "José Gregorio Bastidas", "Aguanegra"],
                        "Simón Planas": ["Sarare", "Buría", "Gustavo Vega"],
                        "Torres": ["Carora", "Aguedo Felipe Alvarado", "Antonio Díaz", "Camacaro", "Castañeda", "Cecilio Zubillaga", "Chiquinquirá", "El Blanco", "Espinoza de los Monteros", "Heriberto Arroyo", "Lara", "Manuel Morillo", "Montaña Verde", "Torres", "Trinidad Samuel"],
                        "Urdaneta": ["Siquisique", "Moroturo", "San Miguel", "Xaguas"]
                    },
                    "Mérida": {
                        "Alberto Adriani": ["El Vigía", "Presidente Betancourt", "Presidente Páez", "Presidente Rómulo Gallegos", "Gabriel Picón González", "Pulido Méndez", "Rómulo Betancourt", "Héctor Amable Mora"],
                        "Andrés Bello": ["La Azulita"],
                        "Antonio Pinto Salinas": ["Santa Cruz de Mora", "Mesa Bolivar", "Mesa de Las Palmas"],
                        "Aricagua": ["Aricagua", "San Antonio"],
                        "Arzobispo Chacón": ["Canagua", "Capurí", "Chacantá", "El Molino", "Guaimaral", "Mucutuy", "Mucuchachí"],
                        "Bailadores": ["Bailadores"],
                        "Caracciolo Parra Olmedo": ["Tucaní", "Florencio Ramírez"],
                        "Cardenal Quintero": ["Santo Domingo", "Las Piedras"],
                        "Guaraque": ["Guaraque", "Mesa de Quintero", "Río Negro"],
                        "Julio César Salas": ["Arapuey", "Palmira"],
                        "Justo Briceño": ["San Cristóbal de Torondoy", "Torondoy"],
                        "Libertador": ["Mérida", "Antonio Spinetti Dini", "Arias", "Caracciolo Parra Pérez", "Domingo Peña", "El Llano", "Gonzalo Picón Febres", "Jacinto Plaza", "Milla", "Osuna Rodríguez", "Sagrario", "Thelmo Ignacio Morales", "Mariano Picón Salas"],
                        "Miranda": ["Timotes", "La Venta", "Piñango", "Andrés Eloy Blanco"],
                        "Obispo Ramos de Lora": ["Santa Elena de Arenales", "Eloy Paredes", "San Rafael de Alcázar"],
                        "Padre Noguera": ["Santa María de Caparo"],
                        "Pueblo Llano": ["Pueblo Llano"],
                        "Rangel": ["Mucuchíes", "Cacute", "La Toma", "Mucurubá", "San Rafael"],
                        "Rivas Dávila": ["Bailadores", "Gerónimo Maldonado"],
                        "Santos Marquina": ["Tabay"],
                        "Sucre": ["Lagunillas", "Chiguará", "Estánquez", "La Trampa", "Punta de Piedras", "San Juan"],
                        "Tovar": ["Tovar", "El Llano", "San Francisco", "El Amparo"],
                        "Tulio Febres Cordero": ["Nueva Bolivia", "Independencia", "María Concepción Palacios", "Santa Apolonia"],
                        "Zea": ["Zea", "Caño El Tigre"]
                    },
                    "Miranda": {
                        "Acevedo": ["Caucete", "Aragüita", "Arévalo González", "Capaya", "Caucagua", "Panaquire", "San Antonio"],
                        "Andrés Bello": ["San José de Barlovento", "Cumbo"],
                        "Baruta": ["Baruta", "El Cafetal", "Las Minas de Baruta"],
                        "Brión": ["Higuerote", "Curiepe", "Tacarigua de Brión"],
                        "Buroz": ["Mamporal"],
                        "Carrizal": ["Carrizal"],
                        "Chacao": ["Chacao"],
                        "Cristóbal Rojas": ["Cúa", "Túkam", "Pueblo Nuevo"],
                        "El Hatillo": ["El Hatillo"],
                        "Guaicaipuro": ["Los Teques", "Altagracia de la Montaña", "Cecilio Acosta", "El Jarillo", "San Pedro", "Tácata", "Paracotos"],
                        "Independencia": ["Santa Teresa del Tuy", "El Cartanal"],
                        "Lander": ["Ocumare del Tuy", "La Democracia", "Bეთvío"],
                        "Los Salias": ["San Antonio de los Altos"],
                        "Páez": ["Río Chico", "El Guapo", "Marizapa", "San Fernando del Guapo", "Paparo"],
                        "Paz Castillo": ["Santa Lucía"],
                        "Pedro Gual": ["Cúpira", "Machurucuto"],
                        "Plaza": ["Guarenas"],
                        "Simón Bolívar": ["San Antonio de Yare", "Yare"],
                        "Sucre": ["Petare", "Caucagüita", "Fila de Mariches", "La Dolorita", "Marizapa"],
                        "Urdaneta": ["Cúa", "Nueva Cúa"],
                        "Zamora": ["Guatire", "Bolívar"]
                    },
                    "Monagas": {
                        "Acosta": ["San Antonio de Maturín", "San Francisco de Maturín"],
                        "Aguasay": ["Aguasay"],
                        "Bolívar": ["Caripito"],
                        "Caripe": ["Caripe", "El Guácharo", "La Guanota", "Sabana de Piedra", "San Agustín", "Teresen"],
                        "Cedeño": ["Caicara de Maturín", "Areo", "San Félix", "Viento Fresco"],
                        "Ezequiel Zamora": ["Punta de Mata", "El Tejero"],
                        "Libertador": ["Temblador", "Chaguaramas", "Las Alhuacas", "Tabasca"],
                        "Maturín": ["Maturín", "Alto de Los Godos", "Boquerón", "Las Cocuizas", "San Simón", "Santa Cruz", "El Corozo", "El Furrial", "Jusepín", "La Pica", "San Vicente"],
                        "Piar": ["Aragua de Maturín", "Aparicio", "Chaguaramal", "El Pinto", "Guanaguana", "La Tosca", "Taguaya"],
                        "Punceres": ["Cachipo", "Areo"],
                        "Quiamare": ["Quiriquire"],
                        "Sotillo": ["Barrancas del Orinoco", "Los Barrancos de Fajardo"],
                        "Uracoa": ["Uracoa"]
                    },
                    "Nueva Esparta": {
                        "Antolín del Campo": ["La Plaza de Paraguachí", "El Tirano", "Avenida 31 de Julio"],
                        "Arismendi": ["La Asunción", "Chivacoa"],
                        "Díaz": ["San Juan Bautista", "Zabala"],
                        "García": ["El Valle del Espíritu Santo", "Francisco Fajardo"],
                        "Gómez": ["Santa Ana", "Bolívar", "Guevara", "Matasiete", "Sucre"],
                        "Maneiro": ["Pampatar", "Aguirre"],
                        "Marcano": ["Juan Griego", "Adrian"],
                        "Mariño": ["Porlamar"],
                        "Península de Macanao": ["Boca de Río", "San Francisco"],
                        "Tubores": ["Punta de Piedras", "Los Barales", "San Vicente"],
                        "Villalba": ["San Pedro de Coche", "Vicente Fuentes"]
                    },
                    "Portuguesa": {
                        "Agua Blanca": ["Agua Blanca"],
                        "Araure": ["Araure", "Río Acarigua"],
                        "Esteller": ["Píritu", "Uveral"],
                        "Guanare": ["Guanare", "Coronel Cedeño", "San José de la Montaña", "Guanarito Viejo", "La Misión"],
                        "Guanarito": ["Guanarito", "Trinidad de la Capilla", "Divina Pastora"],
                        "Monseñor José Vicente de Unda": ["Chabasquén", "Peña Blanca"],
                        "Ospino": ["Ospino", "Aparición", "La Estación"],
                        "Páez": ["Acarigua", "Zaraza", "Payara"],
                        "Papelón": ["Papelón", "Caño Delgadito"],
                        "San Genaro de Boconoíto": ["Boconoíto", "Antolín Tovar Anzola"],
                        "San Rafael de Onoto": ["San Rafael de Onoto", "Santa Fe", "Thelmo Rodríguez"],
                        "Turén": ["Villa Bruzual", "Píritu", "San Rafael de Palo Alzado", "Acarigua", "Canaguá"]
                    },
                    "Sucre": {
                        "Andrés Eloy Blanco": ["Casanay", "Río Caribe"],
                        "Andrés Mata": ["San José de Aerocuar", "Taveras"],
                        "Arismendi": ["Río Caribe", "Antonio José de Sucre", "El Morro de Puerto Santo", "Irapa", "San Juan de las Galdas"],
                        "Benítez": ["El Pilar", "El Rincón", "General Benítez", "Guaraunos", "San Vicente", "El Paujil"],
                        "Bermúdez": ["Carúpano", "Santa Rosa", "Bolívar", "Macarapana", "Serrano"],
                        "Bolívar": ["Marigüitar"],
                        "Cajigal": ["Yaguaraparo", "El Paujil", "Cruz Salmerón Acosta"],
                        "Cruz Salmerón Acosta": ["Araya", "Chacopata", "Manicuare"],
                        "Libertador": ["Tunapuy", "Campo Elías"],
                        "Mariño": ["Irapó", "Soro", "Marigüitar"],
                        "Mejía": ["San Antonio del Golfo"],
                        "Montes": ["Cumanacoa", "Aricagua", "Cachipo", "San Fernando"],
                        "Píritu": ["Píritu", "Spiro"],
                        "Ribero": ["Cariaco", "Catuaro", "Guarapiche", "Santa Cruz", "Aricagua"],
                        "Sucre": ["Cumaná", "Altagracia", "Santa Inés", "Valentín Valiente", "Ayacucho", "San Juan", "Raúl Leoni"]
                    },
                    "Táchira": {
                        "Andrés Bello": ["Cordero"],
                        "Antonio Rómulo Costa": ["Las Mesas"],
                        "Ayacucho": [" Colón", "San Pedro del Río", "Rivas Dávila"],
                        "Bolívar": ["San Antonio del Táchira", "El Palotal", "Isabel Pumar", "Juan Vicente Gómez"],
                        "Cárdenas": ["Táriba", "Amenodoro Ángel Lamus", "La Florida"],
                        "Córdoba": ["Santa Ana del Táchira"],
                        "Fernández Feo": ["San Rafael del Piñal", "El Nula", "Alberto Adriani", "San Joaquín de Navay"],
                        "Francisco de Miranda": ["San José de Bolívar"],
                        "García de Hevia": ["La Fría", "Boca de Grita", "José Antonio Páez"],
                        "Guásimos": ["Palmira"],
                        "Independencia": ["Capacho Nuevo", "Cipriano Castro", "Javier García"],
                        "Jáuregui": ["La Grita", "Emilio Constantino Guerrero", "Monseñor Miguel Antonio Salas"],
                        "José María Vargas": ["El Cobre"],
                        "Junín": ["Rubio", "Bramón", "La Petrólea", "Quinimarí"],
                        "Libertad": ["Capacho Viejo", "Cobre", "Sucre"],
                        "Libertador": ["Abejales", "Doradas", "Emeterio Ochoa", "San Joaquín de Navay"],
                        "Lobatera": ["Lobatera", "Constitución"],
                        "Michelena": ["Michelena"],
                        "Panamericano": ["Coloncito", "La Palmita", "Pblo Hondo"],
                        "Pedro María Ureña": ["Ureña", "Nueva Rosa Inés"],
                        "Páez": ["El Nula"],
                        "Pinar del Río": ["Pinar"],
                        "Rafael Urdaneta": ["Delicias"],
                        "Samuel Darío Maldonado": ["La Tendida", "Boconó", "Hernández"],
                        "San Cristóbal": ["San Cristóbal", "La Concordia", "San Juan Bautista", "Catedral", "Pedro María Morantes", "Lauro Guerrero"],
                        "Seboruco": ["Seboruco"],
                        "Simón Rodríguez": ["San Simón"],
                        "Sucre": ["Queniquea", "San Pablo"],
                        "Torbes": ["San Josecito"],
                        "Uribante": ["Pregonero", "Cardenal Quintero", "Juan Pablo Peñalosa", "San Juaquín de Navay"]
                    },
                    "Trujillo": {
                        "Andrés Bello": ["Santa Isabel", "Araguay", "El Jaguito"],
                        "Boconó": ["Boconó", "Ayacucho", "Burbusay", "El Carmen", "General Rivas", "Guaramacal", "Hato Corozal", "Monseñor Jáuregui", "Mosquey", "Tupano", "San Rafael", "El Progreso", "Vega de Guaramacal"],
                        "Candelaria": ["Candelaria", "El Amparo", "San José", "Gral. Vicente Campo Elías"],
                        "Carache": ["Carache", "Cuicas", "La Concepción", "Panamericana", "Sabaneta"],
                        "Escuque": ["Escuque", "La Sabana", "Sabaneta", "El Socorro"],
                        "José Felipe Márquez Cañizalez": ["El Socorro", "Los Cedros", "La Puerta"],
                        "Juan Vicente Campo Elías": ["Mwesen", "Campo Elías", "Arnoldo Gabaldón"],
                        "La Ceiba": ["Santa Apolonia", "El Progreso", "Tres de Febrero", "La Ceiba"],
                        "Miranda": ["El Dividive", "Agua Caliente", "El Cenizo", "Valerita"],
                        "Monte Carmelo": ["Monte Carmelo", "Buena Vista", "San José"],
                        "Motatán": ["Motatán", "El Baño", "Jalisco"],
                        "Pampán": ["Pampán", "Flor de Patria", "La Paz", "Pampanito II"],
                        "Pampanito": ["Pampanito", "La Conquista", "Pampanito II"],
                        "Rafael Rangel": ["Betijoque", "Zea", "La Pueblita", "Los Cedros"],
                        "San Rafael de Carvajal": ["Carvajal", "Campo Alegre", "Antonio Nicolás Briceño", "José Leonardo Suárez"],
                        "Sucre": ["Sabana de Mendoza", "El Paraíso", "Junín", "Andrés Eloy Blanco"],
                        "Trujillo": ["Trujillo", "Andrés Linares", "Chiquinquirá", "Matriz", "Monseñor Carrillo", "Tres Esquinas"],
                        "Urdaneta": ["La Quebrada", "Cabimbú", "Jajó", "La Mesa", "San José", "Cruz Carrillo"]
                    },
                    "La Guaira": {
                        "Vargas": ["Caraballeda", "Carayaca", "Catia La Mar", "El Junko", "La Guaira", "Macuto", "Maiquetía", "Naiguatá", "Caruao", "Urimare"]
                    },
                    "Yaracuy": {
                        "Arístides Bastidas": ["San Pablo"],
                        "Bolívar": ["Aroa"],
                        "Bruzual": ["Chivacoa", "Campo Elías"],
                        "Cocorote": ["Cocorote"],
                        "Independencia": ["Independencia"],
                        "Antonio José de Sucre": ["Guama"],
                        "Nirgua": ["Nirgua", "Salom", "Temerla"],
                        "Peña": ["Yaritagua", "San Andrés"],
                        "San Felipe": ["San Felipe", "Albarico", "San Javier"],
                        "Trinidad": ["Boraure"],
                        "Urachiche": ["Urachiche"],
                        "Veroes": ["Farriar", "El Guayabo"]
                    },
                    "Zulia": {
                        "Almirante Padilla": ["El Toro", "Islas de Toas", "Monagas"],
                        "Baralt": ["San Timoteo", "General Urdaneta", "Libertador", "Pueblo Nuevo", "Mene Grande", "Marcelino Briceño"],
                        "Cabimas": ["Cabimas", "Ambrosio", "Carmen Herrera", "Germán Ríos Linares", "La Rosa", "Rómulo Betancourt", "San Benito", "Arístides Calvani"],
                        "Catatumbo": ["Encontrados", "Udón Pérez"],
                        "Colón": ["San Carlos del Zulia", "Santa Bárbara", "Moralito", "El Moralito"],
                        "Guajira": ["Sinamaica", "Alta Guajira", "Elías Sánchez Rubio", "Guajira"],
                        "Jesús Enrique Lossada": ["La Concepción", "San José", "Mariano Parra León", "José Ramón Yépez"],
                        "Jesús María Semprún": ["Casigüa El Cubo", "Barí"],
                        "La Cañada de Urdaneta": ["Concepción", "Chiquinquirá", "El Carmelo", "Potreritos"],
                        "Lagunillas": ["Ciudad Ojeda", "Alonso de Ojeda", "Vargas", "Libertad", "Venezuela", "Eleazar López Contreras"],
                        "Machiques de Perijá": ["Chiquinquirá", "Bartolomé de las Casas", "Rio Negro", "San José de Perijá"],
                        "Mara": ["San Rafael del Moján", "La Sierrita", "Las Parcelas", "Luis de Vicente", "Monseñor Marcos Sergio Godoy", "Ricaurte"],
                        "Maracaibo": ["Bolívar", "Cacique Mara", "Caracciolo Parra Pérez", "Cecilio Acosta", "Chiquinquirá", "Coquivacoa", "Cristo de Aranza", "Idelfonso Vásquez", "Juana de Ávila", "Luis Hurtado Higuera", "Manuel Dagnino", "Olegario Villalobos", "San Isidro", "Vencemos", "Venancio Pulgar"],
                        "Miranda": ["Al Altagracia", "Ana María Campos", "Faría", "San Antonio", "Ildefonso Vásquez"],
                        "Rosario de Perijá": ["La Villa del Rosario", "El Amparo", "Sixto Zambrano"],
                        "San Francisco": ["San Francisco", "El Bajo", "Domitila Flores", "Los Cortijos", "Marcial Hernández", "Alemán"],
                        "Santa Rita": ["Santa Rita", "El Mene", "Pedro Lucas Urribarrí", "José Cenobio Urribarrí"],
                        "Simón Bolívar": ["Tía Juana", "Manuel Manrique", "Rafael Maria Baralt"],
                        "Sucre": ["Bobures", "Gibraltar", "El Batey", "Heras", "Monseñor Arturo Álvarez"],
                        "Valmore Rodríguez": ["Bachaquero", "La Victoria", "General J. L. Falcón"]
                    }
                },
                municipiosDisponibles: {
                    "Libertador": ["Sucre", "Altagracia", "Candelaria", "Catedral", "El Recreo", "El Valle", "Coche", "La Pastora", "San Bernardino", "San José", "San Juan", "San Pedro", "Santa Rosalía", "Santa Teresa", "Antímano", "Caricuao", "El Junquito", "Macarao"]
                },
                parroquiasDisponibles: ["Sucre", "Altagracia", "Candelaria", "Catedral", "El Recreo", "El Valle", "Coche", "La Pastora", "San Bernardino", "San José", "San Juan", "San Pedro", "Santa Rosalía", "Santa Teresa", "Antímano", "Caricuao", "El Junquito", "Macarao"],

                cambiarEstado() {
                    this.form.municipio = '';
                    this.form.parroquia = '';
                    this.parroquiasDisponibles = [];
                    this.municipiosDisponibles = this.form.estado ? this.veData[this.form.estado] || {} : {};
                },

                cambiarMunicipio() {
                    this.form.parroquia = '';
                    this.parroquiasDisponibles = (this.form.estado && this.form.municipio) ? this.veData[this.form.estado][this.form.municipio] || [] : [];
                },

                stepTitle() {
                    const titles = [
                        'I. Ubicación Geográfica',
                        'II. Datos del Jefe(a) de Hogar',
                        'III. Vivienda y Servicios',
                        'IV. Situación Socioeconómica',
                        'V. Carga Familiar',
                        'VI. Salud y Vulnerabilidad',
                        'VII. Cierre y Envío'
                    ];
                    return titles[this.step - 1];
                },
                validateStep() {
                    this.errorMessage = '';
                    if (this.step === 1) {
                        if (!this.form.consejo_comunal || (this.form.consejo_comunal === 'Otro' && !this.form.otro_consejo_comunal) || !this.form.estado || !this.form.municipio || !this.form.parroquia || !this.form.sector_calle || !this.form.numero_vivienda_dir || !this.form.fecha_censo) {
                            this.errorMessage = 'Por favor completa todos los campos obligatorios (*) de la sección de Ubicación Geográfica.';
                            return false;
                        }
                    } else if (this.step === 2) {
                        if (!this.form.jefe_nombre || !this.form.jefe_cedula || !this.form.jefe_fecha_nacimiento || !this.form.jefe_sexo || !this.form.jefe_estado_civil || !this.form.jefe_telefono || !this.form.jefe_instruccion || !this.form.jefe_ocupacion) {
                            this.errorMessage = 'Por favor completa todos los campos obligatorios (*) de la sección de Datos del Jefe(a) de Familia.';
                            return false;
                        }
                        if (this.form.jefe_cedula.length < 6 || this.form.jefe_cedula.length > 8) {
                            this.errorMessage = 'La cédula del jefe de familia debe tener entre 6 y 8 dígitos.';
                            return false;
                        }
                        if (this.tieneCarnet === 'Sí' && (!this.form.codigo_carnet || !this.form.serial_carnet)) {
                            this.errorMessage = 'Por favor completa los datos del Carnet de la Patria.';
                            return false;
                        }
                    } else if (this.step === 3) {
                        if (!this.form.tipo_vivienda || !this.form.condicion_juridica || !this.form.estado_infraestructura || !this.form.material_paredes || !this.form.material_techo || !this.form.abastecimiento_agua || !this.form.aguas_servidas || !this.form.acceso_gas || !this.form.empresa_gas || !this.form.conexion_electrica || !this.form.aseo_urbano) {
                            this.errorMessage = 'Por favor completa todos los campos obligatorios (*) de la sección de Vivienda y Servicios.';
                            return false;
                        }
                    } else if (this.step === 4) {
                        if (!this.form.ingreso_familiar || (this.recibeClap === 'Sí' && !this.form.frecuencia_clap) || (this.recibeRemesas === 'Sí' && (!this.form.monto_remesas || !this.form.frecuencia_remesas)) || (this.dificultadCanasta === 'Sí' && !this.form.motivo_dificultad_canasta)) {
                            this.errorMessage = 'Por favor completa todos los campos obligatorios (*) de la sección Socioeconómica.';
                            return false;
                        }
                    } else if (this.step === 5) {
                        for (let i = 0; i < this.miembros.length; i++) {
                            let m = this.miembros[i];
                            if (!m.nombre || (!m.menor && (!m.cedula || m.cedula.length < 6 || m.cedula.length > 8)) || !m.parentesco || !m.sexo || !m.fecha_nac || !m.nivel_ed) {
                                this.errorMessage = `Por favor completa todos los campos obligatorios (*) del integrante de carga familiar #${i + 1}. Recuerda que la cédula debe tener entre 6 y 8 dígitos si no es menor.`;
                                return false;
                            }
                            if (m.tiene_discapacidad === 'Sí') {
                                if (!m.discapacidad || (m.discapacidad === 'Otra' && !m.otra_discapacidad)) {
                                    this.errorMessage = `Por favor especifica la condición de discapacidad del integrante #${i + 1}.`;
                                    return false;
                                }
                            }
                        }
                    } else if (this.step === 6) {
                        if (!this.form.conapdis || (this.embarazadas === 'Sí' && (!this.form.embarazadas_cantidad || !this.form.embarazadas_control)) || (this.lactantes === 'Sí' && !this.form.lactantes_cantidad) || (this.adultos === 'Sí' && !this.form.adultos_cantidad) || (this.encamados === 'Sí' && !this.form.encamados_cantidad) || (this.cronicas === 'Sí' && !this.form.enfermedades_cronicas_detalle)) {
                            this.errorMessage = 'Por favor completa todos los campos obligatorios (*) de la sección de Salud y Vulnerabilidad.';
                            return false;
                        }
                    }
                    return true;
                },
                nextStep() {
                    if (this.validateStep()) {
                        this.step++;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },
                validateForm(event) {
                    if (!this.validateStep()) {
                        event.preventDefault();
                    }
                },
                calcularEdad() {
                    if (!this.form.jefe_fecha_nacimiento) {
                        this.jefeEdad = '';
                        return;
                    }
                    const hoy = new Date();
                    const nacimiento = new Date(this.form.jefe_fecha_nacimiento);
                    let edad = hoy.getFullYear() - nacimiento.getFullYear();
                    const m = hoy.getMonth() - nacimiento.getMonth();
                    if (m < 0 || (m === 0 && hoy.getDate() < nacimiento.getDate())) {
                        edad--;
                    }
                    this.jefeEdad = edad >= 0 ? edad + ' años' : 'Fecha inválida';
                },
                calcularEdadMiembro(index) {
                    let miembro = this.miembros[index];
                    if (!miembro.fecha_nac) {
                        miembro.edad = '';
                        return;
                    }
                    const hoy = new Date();
                    const nacimiento = new Date(miembro.fecha_nac);
                    let edad = hoy.getFullYear() - nacimiento.getFullYear();
                    const m = hoy.getMonth() - nacimiento.getMonth();
                    if (m < 0 || (m === 0 && hoy.getDate() < nacimiento.getDate())) {
                        edad--;
                    }
                    miembro.edad = edad >= 0 ? edad + ' años' : 'Fecha inválida';
                }
            }
        }
    </script>
</x-app-layout>