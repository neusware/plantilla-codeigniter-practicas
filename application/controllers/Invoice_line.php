<?php

class Invoice_line extends MY_Controller
{

    public function __construct(){


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

        // valido los datos


        // extraigo datos en clave del request
        // las líneas d fact
        $invoice_lines_request = $this->post('invoice_lines_data') != null ? $this->post('invoice_lines_data') : $this->post();
        // el id de factura
        // todo podría sacarlo de alguna linea, puede pero más faácil así, si es un create no te viene
        $id_invoice_request = $this->post('id_invoice');

        // transpilo a array asociativo
        if(is_string($invoice_lines_request)) $invoice_lines_request = json_decode($invoice_lines_request,true);
        else $invoice_lines_request = json_decode(json_encode($invoice_lines_request),true);

        //!valido
        // con @params trasnpilados, si error de validación, retorna response deteniendo la ejecución, de lo contrario continuará
        $this->validar_request($invoice_lines_request, $id_invoice_request);

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
            'id_invoice'      => $id_invoice_request //! le asigno el id _invoice que viene por param en request
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

    public function _check_product_exists($id_product){

        // en caso de fallo, seteo el mensaje y retorno el false, lo manejará el set_rules donde se llama absolute este callback.

        //valido el valor del parámetro
        if(empty($id_product) ||!is_numeric($id_product) || (int)$id_product<=0){
            $this->form_validation->set_message('check_product_exists', 'El id de producto proporcionado no es válido.');
            return false;
        }

        //si es válido, cargo el modelo para consultar
        if(!isset($this->product)) $this->load->model('product_model', 'product');

        //compruebo si se ha seteado el modelo
        if(isset($this->product)){

            // para consultar y retornar si existe
            if ($this->product->get((int)$id_product)) {
                return true;
            } else {
                $this->form_validation->set_message('check_product_exists', 'El producto con el id proporcionado no existe.');
                return false;
            }

        }else{
            // no se ha cargado el modelo
            $this->form_validation->set_message('_check_product_exists', ' Error interno al validar el modelo');
        }
    }

    public function validar_request($invoice_lines_request, $id_invoice_request){

        // recibo por parámetro los datos del request extraidos de las claves y transpilados a array asoc.
        // evalúo que haya datos
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

        // he de recorrer cada clave array asociativo, y validar cada una de las claves de cada objeto.
        /*
            [
                0 => {'id_product' => x, 'unidades => x, 'precio_unitario => x, 'descuento'=>x },
                1 => {'id_product' => x, 'unidades => x, 'precio_unitario => x, 'descuento'=>x }
                (...)
            ]
        */

        // uso una flag
        $validated=true;
        // recorro
        foreach($invoice_lines_request as $key => $line){

            // seteo el data, si antes era para todo el objeto, ahora se setea en cada uno, en este caso la línea
            $this->form_validation->set_data($line);

            // defino las reglas
            $this->form_validation->set_rules(
                'id_product',
                'id_product',
                'required|integer|greater_than[0]|callback__check_product_exists',
                [ // seteo los mensajes
                    'required' => 'El {field} es obligatorio',
                    'integer' => 'El [field} debe ser un entero',
                    'greater_than' => 'El {field} debe ser un id válido'
                ]
            );

            $this->form_validation->set_rules(
                'unidades',
                'unidades',
                'required|numeric|greater_than[0]',
                [
                    'required' => 'Las {field} son obligatorias.',
                    'numeric' => 'Las {field} deben ser un valor numérico.',
                    'greater_than' => ' Las {field} deben ser mayor a 0.'
                ]
            );

            $this->form_validation->set_rules(
                'precio_unitario',
                'precio_unitario',
                'required|numeric|greater_than[0]',
                [
                    'required' => 'El {field} es obligatorio.',
                    'numeric' => 'Elabora {field} debe ser un valor numérico.',
                    'greater_than' => 'El {field} deben ser mayor a 0.'
                ]
            );

            $this->form_validation->set_rules(
                'subtotal',
                'subtotal',
                'required|numeric|greater_than[0]',
                [
                    'required' => 'El {field} es obligatorio.',
                    'numeric' => 'Elabora {field} debe ser un valor numérico.',
                    'greater_than' => 'El {field} deben ser mayor a 0.'
                ]
            );

            // el id
            $this->form_validation->set_rules(
                'id',
                'id',
                'permit_empty|integer|greater_than[0]',
                [
                    'integer' => 'El {field} ha de ser un entero',
                    'greater_than' => 'El {field} ha de ser mayor que 0',
                ]
            );

            if($this->form_validation->run() === false){
                // seteo flag
                $validated = false;
            }

            // si ha fallado
            if(!$validated){
                // elaboro y devuelvo response con los mensajes de error correspondientes
                return $this->response(
                array('message' => $this->form_validation->error_string()),
                self::HTTP_OK, self::CODE_SHOW_ERROR_MESSAGE);
            }

        } //foreach line

        // retorno el boolean, ha pasado la validación
        return true;

    }


}
