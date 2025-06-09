<?php

class Invoice_line extends MY_Controller
{

    public function __construct()
    {


        // apunto al modelo
        $this->model = "invoice_line";

        // configuro el tag
        $this->language_tag = "invoice_line";

        // configuro campos para dropdown
        $this->dropdownLabel = ["id_invoice", "id_product"];
        $this->dropDownValue = "id";

        // config campo archivo
        $this->uploadFields = "imagen";

        // restricciones _check_rol() (...)

        parent::__construct();
    }

    // todo caso la factura debe contar al menos con una linea de facturación, o si no elimina la factura
    public function handler_invoice_lines_post(){

        // extraigo datos en clave del request
        // las líneas d fact
        $invoice_lines_request = $this->post('invoice_lines_data') != null ? $this->post('invoice_lines_data') : $this->post();
        // el id de factura
        // todo podría sacarlo de alguna linea
        $id_invoice_request = $this->post('id_invoice');

        //!
        if(empty($invoice_lines_request) ||empty($id_invoice_request)){
            return
            $this->response([
                'message' => 'No se han recibido líneas de facturación o id_invoice',
            ], self::HTTP_OK, self::CODE_SHOW_ERROR_MESSAGE);
        }
        // else{
        //     return $this->response([
        //         'success' => true,
        //         'data' => $invoice_lines
        //     ], self::HTTP_OK, self::CODE_OK);
        // }

        // Transpilo a array asociativo
        if(is_string($invoice_lines_request)) $invoice_lines_request = json_decode($invoice_lines_request,true);
        else $invoice_lines_request = json_decode(json_encode($invoice_lines_request),true);

        //* delete, update y create 

        // leo los registros con el id de factura asociado
        $invoice_lines_db = $this->invoice_line->get_many_by(['id_invoice' => $id_invoice_request]);
        
        // de la lectura, extraigo los ids de los registros en db
        // array_colum()returns single value of array on colum, (array, campo) = array valores en campo
        $invoice_lines_db_ids = array_column($invoice_lines_db, 'id');

        // obtengo ids de los registros request
        $invoice_lines_request_ids = [];
        foreach ($invoice_lines_request as $line) {
            $invoice_lines_request_ids[] = $line['id'];
        }

        // ya tengo los registros del request, sus ids y registros existentes en la base de datos y sis ids, los comparo, para clasificar por operación

        /* ex.
                [1, 4, 6, 8] <- ids registros db
                [1,4, null]  <- ids registros request
        */

        //determinos los registros a eliminar, ids no presentes en el request pero si en la db ||presentes en db y no en request
        $invoice_lines_ids_to_delete = array_diff($invoice_lines_db_ids, $invoice_lines_request_ids);         // ex.  = [6,8] -> como no llegan en el request, y están en la db, se eliminan



        // recorro el array eliminando x id
        foreach ($invoice_lines_ids_to_delete as $id) {
             // ! delete
             $this->invoice_line->delete($id);
        }

        // con los registros eliminados, quedan registros a actualizar (id incluido en request y presente en db) y a crear (id=null en request y ausente en db)

        //! create o update líneas
        // recorro las lineas del request
        foreach ($invoice_lines_request as $line) {

            // todo tiene que haber alguna forma de hacer esto de otra manera, manejando si id viene en null o no, editando solo el id_invoice o mirando si el id es null q es create
            // modelo una estructura tipo registro con los datos
            $data = [
            'id_product'      => $line['id_product'] ?? null,
            'unidades'        => $line['unidades'] ?? null,
            'precio_unitario' => $line['precio_unitario'] ?? null,
            'descuento'       => $line['descuento'] ?? null,
            'subtotal'        => $line['subtotal'] ?? null,
            'id_invoice'      => $id_invoice_request
        ];

        // evalúo, si el id esta presente, no está vacío y existe en la db
        if(isset($line['id']) && !empty($line['id']) && in_array($line['id'], $invoice_lines_db_ids)) {

            // update, id + datos
            $this->invoice_line->update($line['id'], $data);
        } else {
            // de lo contrario es un create
            $this->invoice_line->insert($data);
        }
    }

        // response, consulto y devuelvo las líneas existentes en db
        $updated_lines = $this->invoice_line->get_many_by(['id_invoice' => $id_invoice_request]);
        return $this->response([
            'success' => true,
            'invoice_lines' => $updated_lines 
        ], self::HTTP_OK, self::CODE_OK);

    }
}
