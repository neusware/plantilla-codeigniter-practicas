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

}
