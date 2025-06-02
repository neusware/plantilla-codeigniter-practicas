<?php

class Client extends MY_Controller {

    public function __construct()
    {
        // campos globales

        // apunto modelo
        $this->model="client";

        // configuro language_tag
        $this->language_tag = "client";

        // Configurar los campos para el dropdown - select
        $this->dropdownLabel = ["nombre", "apellido"]; // label/clave al front
        $this->dropDownValue = "id";    // value  al back

        // configuro archivos asociados
        $this->upload_fields = "imagen";

        // restricciones _check_rol()
        // $this->restrictions = array(
        //     "getFilteredProducts" => array(
        //     "groups_allowed" => ["admin"]
        //     )
        // );

        parent::__construct();

    }


    public function getFilteredClients_post(){

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
        $query = $this->client;

        // todo caso de uso filtrar por campos del registro provider asociado, con un join?
        // evaluo filtro para aplicar
        if ($filtro!== null) {
            // or_like_where(campos, filtros, clausula)
            $query = $query->or_like_where([ 'email', 'nombre', 'apellido', 'direccion'], $filtro_exploded, $separation);
        }

        // pagino y filtro adicional
        $datos = $query->paginate($page, $where,  10);

        // evaluo y response
        if(!empty($datos['data'])){
            $this->response($datos, self::HTTP_OK, self::CODE_OK);
        }else{
            $this->response([
                'error' => 'No se encontraron clientes',
                'filter' => $filtro,
                'page' => $page
            ], self::HTTP_OK, self::CODE_BAD);
        }
    }
















}