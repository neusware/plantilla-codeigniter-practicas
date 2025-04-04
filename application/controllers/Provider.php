<?php

defined('BASEPATH') or exit('No direct script access allowed');

// TODO, que campos pongo el el dropdown | es necesario las restriccioens para usar metodos como el getFilteredProviders |Deberia llevar clausulas el getAll

// clase
class Provider extends MY_Controller
{


    public function __construct()
    {

        // parecen ser campos globales

        // ?que campos pongo en el dropdown
        $this->dropdownLabel = array("cif", "nombre");

        // apunto al modelo que va gestionar
        $this->model = "provider";
        // $this->load->model('Provider_model', 'provider');

        // archivos asociados
        $this->upload_fields = "imagen";

         // Configura el language_tag explícitamente
         $this->language_tag = "provider";

        // restricciones para usar en metodos
        // [.....]

        // inicializar la instancia con el constructor de la clase padre
        parent::__construct();

    }

    // -----------métodos personalizados de la clase-------------

     // Obtener todos los proveedores + paginación (all_get MY_Controller)
     public function getAllProviders_get($pagina)
     {
         // Filtros opcionales
         $where = [];

         // Consulta filtrada (opt) + paginación
         $datos = $this->provider->paginate($pagina, $where, 10);

         // Evalúo el resultado de la consulta para reponder
         if (!empty($datos['data'])) {
            //  response (datos + code)
             $this->response($datos, self::HTTP_OK, self::CODE_OK);
         } else {
            //response code-error
             $this->response(["error" => "No se encontraron proveedores"], self::HTTP_NOT_FOUND, self::CODE_BAD);
         }
     }

     // Obtener proovedor por id (data_get en MY_Controller y controller User)
     public function getProvider_get($provider_id){

        // consulto y almaceno respuesta
        $datos = $this->provider->get($provider_id);

        // evaluo el resultado para controlar la response
        if(!empty($datos)){
            // response (datos + code)
            $this->response($datos, self::HTTP_OK, self::CODE_OK);
        }else{
            // response code-error, se podría meter un lang->line
            $this->response(["error" => "No se encontraron proveedores"], self::HTTP_NOT_FOUND, self::CODE_BAD);
        }
    }

    //Obtener proveedor/es por filtro (input)
    /* 
        Request tipo:
        {
            "filter": {
                "buscador": "Ybar"
            },
            "page": 1
        }
    */
    public function getFilteredProviders_post(){

        // trato obtener el input, o valor x defecto
        $filtro = $this->post('filter')['buscador'] ?? null;
        // trato obtener paginación
        $page = $this->post('page') ?? 1;

        //divido el filtro en palabras clave para optimizar consulta
        $explodedFiltro = $filtro ? explode("%20", $filtro) : null;
        // clausula LIKE ... OR ... OR ... para cada palabra clave
        $separation = "OR";
        //clausula de consulta para soft-deletes
        $where = [
            'is_hidden' => false // 0
        ];

        //elaboro query base (sin filtros ni paginación)
        $query = $this->provider;

        // evalúo filtro
        if($filtro !== null){

            //sobreescribo la query base para agregar el filtro
            $query = $query->or_like_where(["cif", "nombre", "email", "phone"], $explodedFiltro, $separation);

        }

        // igualmente pagino y agrego clausula a la query
        $datos = $query->paginate($page, $where, 10);

        // en este punto $datos es un array asociativo con los datos de la consulta y la paginación
        /*
            $datos = [
                "data" => [
                    "id" => x,
                    "nombre" => x,
                    "cif" => x,
                    "email" => x,
                    "telefono" => x,
                    "imagen" => x,
                    (...)
                ],
                "counts" => [
                "from_num" => 0,       // Índice inicial de los resultados en la página actual
                "to_num" => 0,         // Índice final de los resultados en la página actual
                "total_num" => 0,      // Total de registros encontrados
                "curr_page" => 1,      // Página actual
                "total_pages" => 1,    // Total de páginas disponibles
                "limit" => 10          // Límite de registros por página
                ]
            ]
        */

        // evalúo el resultado de la consulta para reponder
        if(!empty($datos['data'])){
            // response parseada JSON (datos + clave "code" con el CODE)
            $this->response($datos, self::HTTP_OK, self::CODE_OK);
        }else{
            $this->response(["error" => "No se encontraron proveedores",
            "filter" => $filtro,
            "page" => $page], self::HTTP_NOT_FOUND, self::CODE_BAD);
        }
    }





}
