<?php

class Provider_model extends MY_Model
{
    // constructor de la clase
    public function __construct()
    {
        // ejecuto el constructor de la clase padre
        parent::__construct();

        // defino el nombre de la tabla asociada
        $this->_table = "providers";

        // Configuración para campos de subida de archivos, controlado en clase padre
        $this->upload_fields = ["imagen" => "imagenes"];
        $this->upload_file_config['allowed_types'] = "jpg|png|jpeg";

        // Configuración de nombres para mensajes de respuesta en checkRestrict()
        $this->singular_name = 'proveedor';
        $this->plural_name = 'proveedores';

        // propiedad configurada para ejecutar a un método a modo de callback con la clase padre, cuando ejecuta determinados tipos de consulta
        $this->after_get[] = 'cleanFields';
    }

    /**
     * Limpia los campos sensibles o innecesarios antes de devolver los datos, y que no lleguen al front.
     *
     * Aplica para un solo registro, en este caso de uso elimina los campos 'created_at' y 'updated_at'
     * creados por defecto en la tabla.
     */
    public function cleanFields($provider)
    {
        unset($provider->created_at);
        unset($provider->updated_at);

        return $provider;
    }

    /**
     * Limpia múltiples registros de proveedores, iterando sobre el array que recibe como parámetro.
     */
    public function cleanFieldsMany($providers)
    {
        foreach ($providers as $provider) {
            $this->cleanFields($provider);
        }
        return $providers;
    }
}