<?php

/**
 * @author  Daniel Quero <daniel@signlab.info>
*/

defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('parseDropdownVue')) {
	function parseDropdownVue($array_data = [], $label = 'nombre', $value = 'id', $cascade_names = null) {
		/**
		 * @param array 	$array_data 	array of DB rows to make the dropdown
		 * @param string 	$label        		string name of the array attribute to get the label name
		 * @param string 	$value    				string name of the array attribute to get the value name
		 * @param array 	$cascade_names		array of strings in which yo should pass the name of the "with" and "cascade" attributes to get inside the $array_data
		 *
		 * @return array dropdown format array (['label' => 'X', 'value' => Y])
		 */

    $array_data = json_decode(json_encode($array_data), true);
    $dropdown = [];
    if ($cascade_names == null) {
      foreach ($array_data as $opcion) {
        $dropdown[] = ['label' => $opcion[$label], 'value' => $opcion[$value]];
      }
    } else {
			$aux_row = null;
			foreach ($array_data as $opcion) {
				for ($i=0; $i < count($cascade_names); $i++) {
					$aux_row = $opcion[$cascade_names[$i]];
					if ($i == (count($cascade_names) - 1)) {
						$dropdown[] = ['label' => $aux_row[$label], 'value' => $opcion[$value]];
					}
				}
			}
    }

		return $dropdown;
	}
}
