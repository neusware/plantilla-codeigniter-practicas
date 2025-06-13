<?php

class Invoice extends MY_Controller
{

    public function __construct()
    {

        // apunto al modelo
        $this->model = "invoice";

        // configuro language_tag
        $this->language_tag = "invoice";

        // configuro campos para dropdown
        $this->dropdownLabel = "codigo";
        $this->dropDownValue = "id";

        // restricciones _check_rol()
        // $this->restrictions = array(
        //     "getFilteredProducts" => array(
        //     "groups_allowed" => ["admin"]
        //     )
        // );

        parent::__construct();
    }

    /*

  "code": 20000,
  "data": [
    {                           //factura x
      "id": 1,
      "id_client": 1,
      "codigo": 252525,
      "fecha": "2025-05-12",
      "total": 246,
      "clients": {              //with clientes
        "id": 1,
        "email": "email@example.com",
        "nombre": "Ramón",
        "apellido": "Montoya",
        "direccion": "Córdoba"
      },
      "invoice_lines": [    //with invoice_lines
        {
          "id": 1,
          "id_invoice": 1,
          "id_product": 16,
          "unidades": 2,
          "precio_unitario": 25,
          "descuento": null,
          "subtotal": 50,
          "products": {             //cascade products
            "id": 16,
            "id_provider": 23,
            "nombre": "Auriculares Sony X432DS",
            "codigo": "S12312312",
            "stock": 97,
            "precio": 11,
            "is_hidden": 0
          }
        }
    }
    */
    // sobreescrito con relaciones directas + indirecta
    public function all_get()
    {
        // Carga las facturas con sus clientes relacionados
        // y con sus líneas de factura, incluyendo los productos de cada línea usando cascade.
        $elements = $this->{$this->model}
            ->with('clients')      // Carga la relación 'clients' definida en Invoice_model
            ->with('invoice_lines') // Carga la relación 'invoice_lines'
            ->cascade('products')   // Para cada invoice_line cargada, carga su relación 'products'
            ->get_all();

        if ($elements) {
            $this->response($elements, REST_Controller::HTTP_OK, self::CODE_OK);
        } else {
            // Devuelve un array vacío si no hay elementos, manteniendo el formato de respuesta esperado.
            $this->response([], REST_Controller::HTTP_OK, self::CODE_OK);
        }
    }

    // read, con filtro
    public function getFilteredInvoices_post(){

        // extraigo datos del request, como es un get (filtro y paginación)

        // extraigo el filtro
        $filtro = $this->post('filter')['buscador'] ?? null;

        // extraigo paginación
        $page = $this->post('page') ?? 1;

        // explode del string
        $filtro_exploded = $filtro ? explode('%20', $filtro) : null;

        // clausula OR, parcheo en query
        $separation = "OR";

        // clausula where - paginate() la espera
        $where = [
            'is_hidden' => false
        ];

        // consulta base, registros producto con los registros proovedor asociados
        $query = $this->invoice->with('clients');

        // todo caso de uso filtrar por campos del registro provider asociado, con un join?
        // evaluo filtro para aplicar
        if ($filtro!== null) {
            // or_like_where(campos, filtros, clausula)
            $query = $query->or_like_where([ 'id_client', 'codigo', 'fecha', 'total'], $filtro_exploded, $separation);
        }

        // pagino y filtro adicional
        $datos = $query->paginate($page, $where,  10);

        // evaluo y response
        if(!empty($datos['data'])){
            $this->response($datos, self::HTTP_OK, self::CODE_OK);
        }else{
            $this->response([
                'error' => 'No se encontraron facturas',
                'filter' => $filtro,
                'page' => $page
            ], self::HTTP_OK, self::CODE_BAD);
        }
    }

        /**
     * * Crear registro invoice- Validación previa
     *
     * Método para validar la presencia de los campos que se requieren para crear un registro.
     *
     * Operación:
     * 1. Obtengo los datos del request, en clave 'data' o request completo.
     * 2. Transpilo request a array asociativo.
     * 3. Procedo a validar los datos.
     * 3.1 Seteo el conjunto a validar y las reglas de validación. La regla de validación determina que el valor del conjunto de datos en la clave 'cif' es requerido y  será único en el campo cif de la tabla providers, compara.
     * 3.2 Ejecuto la validación y evalúo el resultado.
     * 3.2.1 Si la validación falla, elaboro una response tipo error (message+ http status + http code)
     * 3.2.2 Si la validación es existosa, el flujo no se interrumpe, y el request pasa al método create_post() de la clase padre, que se encarga de interactuar con la base de datos para crear el registro y devolver una response.
     */
    public function create_post()
    {
        //obtener datos
        $data = $this->post("data") ?? $this->post();
        // Decodificar el JSON si es un string en array asociativo
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        // Asegurarse de que $data es un array antes de proceder
        if (!is_array($data)) {
            $this->response(
                ["message" => "Datos de entrada no válidos para la factura."],
                self::HTTP_BAD_REQUEST,
                self::CODE_BAD
            );
            return;
        }

        // Seteo los datos a validar
        $this->form_validation->set_data($data);

        // Establezco las reglas de validación para la factura
        $this->form_validation->set_rules(
            'id_client',
            '(id) cliente',
            'trim|required|integer|greater_than[0]|callback__check_client_exists',
            array(
                'required' => 'El {field} es obligatorio.',
                'integer' => 'El {field} debe ser un número entero.',
                'greater_than' => 'El {field} debe ser un ID válido.',
                '_check_client_exists' => 'El cliente especificado no existe.' // Mensaje para el callback
            )
        );
        $this->form_validation->set_rules(
            'codigo',
            'c&oacute;digo de factura',
            'trim|required|numeric|greater_than_equal_to[0]|is_unique[invoices.codigo]',
            array(
            'required' => 'El {field} es obligatorio.',
            'numeric' => 'El {field} debe ser numérico.',
            'greater_than_equal_to' => 'El {field} no puede ser negativo.',
            'is_unique' => 'El {field} ya existe en el sistema.'
            )
        );
        $this->form_validation->set_rules(
            'fecha',
            'fecha',
            'trim|required', //añadir validación de formato: |regex_match[/^\d{4}-\d{2}-\d{2}$/]
            array(
                'required' => 'La {field} es obligatoria'
                // 'regex_match' => 'El formato de {field} debe ser YYYY-MM-DD.'
            )
        );
        $this->form_validation->set_rules(
            'total', //campo
            'total facturado', //para messag
            'trim|required|numeric|greater_than[0]', //rules
            array(
                // messages array
                'required' => 'El {field} es obligatorio.',
                'numeric' => 'El {field} debe ser numérico.',
                'greater_than' => 'El {field} debe ser mayor a cero' //si llega un total <=0 esq no se ha calculadoTotal o que no hay líneas de facturación, podría comprobarlo así indirectamente.
            )
        );
        // Ejecuto la validación
        if ($this->form_validation->run() === FALSE) {
            // Si la validación falla, elaboro una response de error
            $this->response(
                // con todos los mensajes de error del array que se den
                array("message" =>$this->form_validation->error_string()),
                self::HTTP_OK,
                self::CODE_SHOW_ERROR_MESSAGE
            );
            return;
        }

        // Si la validación es exitosa, el flujo continúa.
        // Asegúrate de que parent::create_post() maneje los datos correctamente.
        // Si MY_Controller::create_post() espera los datos procesados ($data),
        // podrías necesitar pasar $data o asegurar que los toma de $this->post()
        parent::create_post();
    }

    /**
     * Callback para validar la existencia de un cliente.
     * Utilizado en las reglas de form_validation.
     *
     * @param int $id_client El ID del cliente a verificar.
     * @return bool TRUE si el cliente existe, FALSE en caso contrario.
     */
       public function _check_client_exists($id_client)
    {
        // Validar que $id_client sea un entero positivo
        if (!is_numeric($id_client) || (int)$id_client <= 0) {
             // Este mensaje es genérico, el más específico se define en set_rules
            $this->form_validation->set_message('_check_client_exists', 'El ID de cliente proporcionado no es válido.');
            return FALSE;
        }

        // Cargar el modelo Client_model y asignarlo a $this->client
        // si no está ya cargado.
        if (!isset($this->client)) { 
            $this->load->model('Client_model', 'client');
        }

        // Verificar que el modelo se haya cargado correctamente y tenga el método 'get'
        if (isset($this->client) && method_exists($this->client, 'get')) {
            // Usar el método get() del Client_model para verificar la existencia
            if ($this->client->get((int)$id_client)) {
                return TRUE; // El cliente existe
            } else {
                // El cliente no existe
                // El mensaje de error específico se define en set_rules.
                return FALSE; 
            }
        } else {
            // Error: El modelo Client_model no se cargó o no tiene el método get().
            // Puedes loggear este error si es necesario.
            $this->form_validation->set_message('_check_client_exists', 'Error interno al validar el cliente.');
            return FALSE;
        }
    }

     /**
     * * Actualizar registro invoice - Validación previa
     *
     *
     *  */
    public function update_post()
    {
        // extraigo datos
        $data = $this->post("data") ?? $this->post();

        // transpilo a array asociativo
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        // Asegurarse de que $data es un array y contiene un 'id' antes de proceder
        if (!is_array($data) || !isset($data['id'])) {
            $this->response(
                ["message" => "Datos de entrada no válidos o ID de factura faltante."],
                self::HTTP_BAD_REQUEST, 
                self::CODE_BAD
            );
            return;
        }
        // Seteo los datos a validar
        $this->form_validation->set_data($data);

        // Establezco las reglas de validación para la factura
        $this->form_validation->set_rules(
            'id_client',
            'Cliente (ID)',
            'trim|required|integer|greater_than[0]|callback__check_client_exists',
            array(
                'required' => 'El campo {field} es obligatorio.',
                'integer' => 'El campo {field} debe ser un número entero.',
                'greater_than' => 'El campo {field} debe ser un ID válido.',
                '_check_client_exists' => 'El cliente especificado con ID {value} no existe.' // Mensaje para el callback
            )
        );
        $this->form_validation->set_rules(
            'codigo',
            'c&oacute;digo de factura',
            'trim|required|numeric|greater_than_equal_to[0]|is_unique[invoices.codigo.id.{$invoice_id}]',
            array(
            'required' => 'El {field} es obligatorio.',
            'numeric' => 'El {field} debe ser numérico.',
            'greater_than_equal_to' => 'El {field} no puede ser negativo.',
            'is_unique' => 'El {field} ya existe en el sistema.'
            )
        );
        $this->form_validation->set_rules(
            'fecha',
            'fecha',
            'trim|required', //añadir validación de formato: |regex_match[/^\d{4}-\d{2}-\d{2}$/]
            array(
                'required' => 'La {field} es obligatoria'
                // 'regex_match' => 'El formato de {field} debe ser YYYY-MM-DD.'
            )
        );
        $this->form_validation->set_rules(
            'total', //campo
            'total facturado', //para messag
            'trim|required|numeric|greater_than[0]', //rules
            array(
                // messages array
                'required' => 'El {field} es obligatorio.',
                'numeric' => 'El {field} debe ser numérico.',
                'greater_than' => 'El {field} debe ser mayor a cero' //si llega un total <=0 esq no se ha calculadoTotal o que no hay líneas de facturación, podría comprobarlo así indirectamente.
            )
        );
        // Ejecuto la validación
        if ($this->form_validation->run() === FALSE) {
            // Si la validación falla, elaboro una response de error
            $this->response(
                // con todos los mensajes de error del array que se den
                array("message" =>$this->form_validation->error_string()),
                self::HTTP_OK,
                self::CODE_SHOW_ERROR_MESSAGE
            );
            return;
        }
        // pasa la validación y el request sigue hacie el metodo de la clase padre, no hace falta request x parámetro, lo tiene el objeto controlador
        parent::update_post();
    }

}
