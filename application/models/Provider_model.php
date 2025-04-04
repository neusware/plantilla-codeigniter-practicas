<?php

class Provider_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        // Nombre de la tabla asociada
        $this->_table = "providers";

        // Configuración para campos de subida de archivos
        $this->upload_fields = ["imagen" => "imagenes"];
        $this->upload_file_config['allowed_types'] = "jpg|png|jpeg";

        // Configuración de nombres para mensajes de respuesta
        $this->singular_name = 'proveedor';
        $this->plural_name = 'proveedores';

        // Limpieza de campos sensibles o innecesarios
        $this->after_get[] = 'cleanFields';
    }

    /**
     * Limpia los campos sensibles o innecesarios antes de devolver los datos.
     */
    public function cleanFields($provider)
    {
        unset($provider->created_at);
        unset($provider->updated_at);
        // Agrega más campos a eliminar si es necesario
        return $provider;
    }

    /**
     * Limpia múltiples registros de proveedores.
     */
    public function cleanFieldsMany($providers)
    {
        foreach ($providers as $provider) {
            $this->cleanFields($provider);
        }
        return $providers;
    }
}