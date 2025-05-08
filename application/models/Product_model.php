<?php

class Product_model extends MY_Model {



    public function __construct()
    {
        // ejecuto el constructor padre
        parent::__construct();

        // defino el nombre de la tabla asociada
        $this->_table = "products";

        /*
        {
            "id":1,
            "id_proveedor":1,
            "nombre":"Televisión",
            "codigo":"ABCDE",
            "stock":1,
            "precio":150
        }

        */

        // config para archivos - parent
        $this->upload_fields = ["imagen" => "imagenes"];
        $this->upload_file_config = ["allowed_types" => "jpg|png|jpeg"];

        // config mensajes checkRestrict()
        $this->singular_name = "producto";
        $this->plural_name = "productos";

        // metodo callback after_get
        $this->after_get[] = 'cleanFields';

        $this->belongs_to = array(
            "provider" => array(
                "model" => "provider_model",
                "primary_key" => "id_provider"
            ));


    }

    // after_get unset campos no interesantes
    public function cleanFields($product){

        // elimino los pares que no interesan, como propiedad del objeto
        unset($product->created_at);
        unset($product->updated_at);

        // retorno
        return $product;

    }

    public function cleanFieldsMany($products){

        // recorro
        foreach($products as $product){
            // limpiando - clase.metodo.param
            $this->cleanFields($product);
        }
    }

}