<?php

class Client_model extends MY_Model {


    public function __construct(){


            // instancio objeto clase padre
            parent::__construct();

            // apunto a la tabla
            $this->_table = "clients";

            // config archivos -parent
            $this->upload_fields = ["imagen" => "imagenes"];
            $this->upload_file_config = ["allowed_types" => "jpg|png|jpeg"];

            // config mensajes checkRestrict()
            $this->singular_name = "cliente";
            $this->plural_name = "clientes";

            // metodo callback after_get
            $this->after_get[] = 'cleanFields';

            // relación fk en invoices
            $this->has_many = array(
                // nombre relación
                "invoices" => array(
                    // modelo relacionado, tipo clausula => campo
                    "model" => "invoice_model", "primary_key" => "id_client"
                )
            );

    }

    // after_get unset campos no target
    public function cleanFields($client){
        unset($client->created_at);
        unset($client->updated_at);

        return $client;
    }

    public function cleanFieldsMany($clients){
        foreach($clients as $client){
            // unset($client->created_at);
            // unset($client->updated_at);
            $this->cleanFields($client);
        }

        return $clients;
    }

}