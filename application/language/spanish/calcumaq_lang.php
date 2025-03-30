<?php

/*
 * Spanish language
 */

//generales
$lang['error_material_id'] = 'No existe ningun material con ese identificador.';
$lang['error_gama_id'] = 'No existe ninguna gama con ese id.';
$lang["no_hay_caja"] = 'No hay caja que satisfaga los requisitos.';


//reductores
$lang['error_potencia_reductor'] = 'La potencia del reductor no se corresponde con la potencia calculada.';
$lang['error_seleccion_reductor'] = 'El motor reductor seleccionado no se encuentra entre los disponibles';
$lang['error_sin_reductor'] = 'No hay reductores que satisfaga el requisito de la potencia.';


//elevador
$lang['error_elevador_seleccionado'] = 'El elevador seleccionado no da el rendimiento que se solicita.';
$lang['error_elevador_fuera_rango'] = 'Ningún elevador puede satisfacer la producción solicitada.';
$lang['error_elevador_campos_obligatorios'] = 'Error: debe pasar id_gama,produccion,id_material y altura_ejes';
$lang['altura_no_valido'] = 'La altura debe ser mayor de cero';
$lang['elevador_excede_altura'] = 'El elevador excede la altura de diseño (%.2f m)';
$lang['elevador_no_supera_altura_minima'] = 'El elevador no cumple la altura mínima de diseño (%.2f m)';

//transportadores de cadena
$lang['error_no_material'] = 'El material seleccionado no existe en la base de datos';
$lang['error_inclinacion_negativa'] = 'La inclinación no puede ser negativa';
$lang['error_no_caja'] = 'La caja seleccionada no está entre las cajas que cumplen la producción';
$lang['error_no_reductor'] = 'El reductor seleccionado no existe en la base de datos';
$lang['long_no_valida'] = 'La longitud debe ser mayor de cero';
$lang['error_fuerza_cadena'] = 'La cadena no aguanta la fuerza de rotura %.2f < %.2f';
$lang['long_piquera_no_valida'] = 'La longitud de la piquera debe ser inferior a 15 m.';
$lang['long_calculo_no_valida'] = 'La longitud de cálculo excesiva (ls + 3 x lp + 2 x ldf) > %.2f.';
$lang['long_calculo_aviso'] = 'Longitudes elevadas se aconseja simplificar.';

//transportador de rosca
$lang['error_longitud_no_valida'] = 'El valor de la longitud no es válido';
$lang['error_velocidad_no_valida'] = 'La velocidad debe ser mayor de cero';
$lang['longitud_obligatoria'] = 'La longitud es un campo obligatorio';
$lang['longitud_recomendada_superada'] = 'Se ha superado la longitud máxima recomendada (%d)';
$lang['longitud_maxima_superada'] = 'Se ha superado la longitud máxima (%d)';
$lang['material_obligatorio'] = 'El material es un campo obligatorio';
$lang['produccion_obligatorio'] = 'La producción es un campo obligatorio';
$lang['produccion_no_valida'] = 'La producción no puede ser negativa';
$lang['inclinacion_obligatorio'] = 'La inclinación es un campo obligatorio';
$lang['inclinacion_maxima_superada'] = 'Se ha superado la inclincaión máxima (%d)';
$lang['velocidad_obligatorio'] = 'La velocidad es un campo obligatorio';
$lang['error_rosca_fuera_rango'] = 'Ningún transportador de rosca puede satisfacer la producción solicitada.';

//transportador de Banda
$lang['long_ejes_obligatorio'] = 'El campo de longitud de ejes es obligatorio';
$lang['long_ejes_no_valida'] = 'La longitud de ejes debe ser mayor que 0';
$lang['long_corta'] = 'La longitud debe ser mayor para albergar ese número de trippers';
$lang['error_banda_fuera_rango'] = 'Ningún transportador de banda puede satisfacer la producción solicitada.';

//barredoras
$lang['modelo_obligatorio'] = 'El campo de modelo de la barredora es obligatorio';
$lang['modelo_no_valido'] = 'No existe una barredora con ese modelo';
$lang['silo_obligatorio'] = 'El campo de diametro del silo es obligatorio';
$lang['silo_no_valido'] = 'El valor del diámetro debe ser mayor de cero';
$lang['ninguna_barredora_disponible'] = 'Ninguna barredora disponible para los parámetros introducidos';
