<?php

class Invoice_line_model extends MY_Model{

    public function __construct()
    {
        parent::__construct(); // <-- ASEGÚRATE DE QUE ESTA LÍNEA ESTÉ PRESENTE
        // apunto a la tabla
        $this->_table = "invoice_lines";

        // config archivos - parent
        $this->upload_fields = ["imagen" => "imagenes"];
        $this-> upload_file_config = ["allowed_types" => "jpg|png|jpeg"];

        // config mensajes checkRestrict()
        $this->singular_name = "invoice_line";
        $this->plural_name = "invoice_lines";

        // metodo callback after_get
        $this->after_get[] = 'cleanFields';

        // Definir relaciones belongs_to correctamente
        $this->belongs_to = array(
            // relación fk en id_invoice
            "invoices" => array(
                // modelo relacionado, tipo clausula => campo
                "model" => "invoice_model", "primary_key" => "id_invoice"
            ),
            // relación fk id_product
            "products" => array(
                // modelo relacionado, tipo clausula => campo
                "model" => "product_model", "primary_key" => "id_product"
            )
        );

    }

        // after_get unset campos no target
        public function cleanFields($invoice_line){
            unset($invoice_line->created_at);
            unset($invoice_line->updated_at);

            return $invoice_line;
        }

        public function cleanFieldsMany($invoice_lines){
            foreach($invoice_lines as $invoice_line){
                // unset($client->created_at);
                // unset($client->updated_at);
                $this->cleanFields($invoice_line);
            }

            return $invoice_lines;
        }

}