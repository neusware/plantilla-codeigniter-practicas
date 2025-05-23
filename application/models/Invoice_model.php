<?php

class Invoice_model extends MY_Model {


    public function __construct(){


            // instancio objeto clase padre
            parent::__construct();

            // apunto a la tabla
            $this->_table = "invoices";

            // config archivos -parent
            $this->upload_fields = ["imagen" => "imagenes"];
            $this->upload_file_config = ["allowed_types" => "jpg|png|jpeg"];

            // config mensajes checkRestrict()
            $this->singular_name = "invoice";
            $this->plural_name = "invoices";

            // metodo callback after_get
            $this->after_get[] = 'cleanFields';

            // relación fk id_client
            $this->belongs_to = array(
                // nombre relación
                "clients" => array(
                    // modelo relacionado, tipo clausula => campo
                    "model" => "client_model", "primary_key" => "id_client"
                    // campo id_client belongs to (fk) primary_key en el modelo clients_model
                )
            );

            // relación con productos, matriz asociativa
            $this->has_many = array(
                // nombre relación
                "invoice_lines" => array(
                    // modelo relacionada, tipo clausula => campo
                    "model" => "invoice_line_model", "primary_key" => "id_invoice"
                ));

    }

    // after_get unset campos no target
    public function cleanFields($invoice){
        unset($invoice->created_at);
        unset($invoice->updated_at);

        return $invoice;
    }

    public function cleanFieldsMany($invoices){
        foreach($invoices as $invoice){
            // unset($client->created_at);
            // unset($client->updated_at);
            $this->cleanFields($invoice);
        }

        return $invoices;
    }

}