<?php


class Product extends MY_Controller{

    public function __construct()
    {
        // campos globales
        $this->model = "product";

        // campos dropdow
        $this->dropdownLabel = array("id", "id_proveedor");

        // archivos asociados
        $this->upload_fields = "imagen";

        // configuro language tag
        $this->language_tag = "product";

        // restricciones _check_rol()
        // $this->restrictions = array(
        //     "getFilteredProducts" => array(
        //     "groups_allowed" => ["admin"]
        //     )
        // );

        // Configurar los campos para el dropdown - select
        $this->dropdownLabel = "nombre"; // label/clave al front
        $this->dropDownValue = "id";    // value  al back

        parent::__construct();

    }


    // ------------------ métodos personalizados ------------------------



    public function getFilteredProducts_post(){

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
        $query = $this->product->with('provider');

        // todo caso de uso filtrar por campos del registro provider asociado, con un join?
        // evaluo filtro para aplicar
        if ($filtro!== null) {
            // or_like_where(campos, filtros, clausula)
            $query = $query->or_like_where([ 'nombre', 'codigo', 'precio', 'stock'], $filtro_exploded, $separation);
        }

        // pagino y filtro adicional
        $datos = $query->paginate($page, $where,  10);

        // evaluo y response
        if(!empty($datos['data'])){
            $this->response($datos, self::HTTP_OK, self::CODE_OK);
        }else{
            $this->response([
                'error' => 'No se encontraron productos',
                'filter' => $filtro,
                'page' => $page
            ], self::HTTP_OK, self::CODE_BAD);
        }
    }



}