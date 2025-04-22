<?php

defined('BASEPATH') or exit('No direct script access allowed');

// TODO, que campos pongo el el dropdown | es necesario las restriccioens para usar metodos como el getFilteredProviders |Deberia llevar clausulas el getAll

// clase
class Provider extends MY_Controller
{


    public function __construct()
    {
        // parecen ser campos globales, propiedades, atributos

        // apunto al modelo que va gestionar
        $this->model = "provider";
        // $this->load->model('Provider_model', 'provider'); no me cargaba por autoload.php

        // ?que campos pongo en el dropdown
        $this->dropdownLabel = array("cif", "nombre");

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

    /**
     * Obtener todos los proveedores + paginación  + clausula a piñon
     *
     *  1. Defino e inicializo una clausula a piñon, en este caso preparada para los soft-deletes
     *  1. Elaboro y ejecuto una consulta base pasándole la paginación y la cláusula establecida
     *  1. Evalúo la variable $datos en clave 'data' para elaborar un mensaje de respuesta u otro
     *
     *  Es similar al metodo all_get() de la clase padre, simplemente se le agrega el filtro a piñon.
     */
    public function getAllProviders_get($pagina)
    {
        // Filtros opcionales
        $where = [
            'is_hidden' => false
        ];

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
    /**
     *  Obtener sólo un registro a partir del id. Este método es redundante, ya que es similar al data_get() de la clase padre.
     *
     * 1. Elaboro una consulta base filtrada por el id del proveedor, la alamceno en una variable.
     * 2. Evalúo el resultado de la consulta para elaborar una respuesta u otra. Sin utilizar los lang, elaboro la response de forma manual.
     *
     * */
    public function getProvider_get($provider_id)
    {

        // consulto y almaceno respuesta
        $datos = $this->provider->get($provider_id);

        // evaluo el resultado para controlar la response
        if (!empty($datos)) {
            // response (datos + code)
            $this->response($datos, self::HTTP_OK, self::CODE_OK);
        } else {
            // response code-error, se podría meter un lang->line
            $this->response(["error" => "No se encontraron proveedores"], self::HTTP_NOT_FOUND, self::CODE_BAD);
        }
    }

    // Crear proveedor checkando el cif
    // public function createProvider_post(){

    //     // extraigo el valor del requesst
    //     $data = $this->post("data") != null ? $this->post("data") : $this->post();

    // //     // transpila datos a array asocitivo
    // //   if(is_string($data)) $element = json_decode($data);
    // //   else $data = json_decode(json_encode($data));

    //     // Decodificar el JSON si es un string
    //     if (is_string($data)) {
    //         $data = json_decode($data, true); // Decodificar como array asociativo
    //     }

    //     // pruebo que entra al metodo
    //     // extraigo el cif para devolverlo
    //     $cif = isset($data['cif']) ? $data['cif'] : null;

    //     // fb para verificar que entra
    //     // $this->response([
    //     //     "cif" => $cif ?? null,
    //     //     "code" => self::CODE_OK
    //     // ], self::HTTP_OK, self::CODE_OK);

    //     $record = $this->getByCIF($cif);
    //     // Si devuelve un registro, significa que el CIF ya existe, elaboro response BAD
    //     if ($record) {
    //         $this->response(array("message" => $this->lang->line('error_crear_'.$this->language_tag)),self::HTTP_OK,self::CODE_BAD);
    //     } else {
    //         // Si devuelve null, significa que el CIF no existe, hago uso del metodo base para hacer el insert
    //         parent::create_post($this->post());
    //     }
    // }

    // public function getByCIF($cif)
    // {
    //     // Consultar la base de datos para buscar un registro con el CIF proporcionado
    //     // $query= $this->provider->get();
    //     // $record = $query->where('cif', $cif);

    // $record = $this->provider->get_by(['cif' => $cif]);

    //     // Si encuentra un registro, devolverlo; si no, devolver null
    //     return $record !=null ? $record : null;
    // }

    //Obtener proveedor/es por filtro (input)
    /* Estructura request tipo:
        {
            "filter": {
                "buscador": "Ybar"
            },
            "page": 1
        }
    */
    /**
     * Método para obtener los proveedores filtrados por el input que llega del request +cclausula hardcodeada + restriccion en la consulta ` paginación.
     *
     * Este es el metodo que va usar el front para hacer las lecturas, puede incluir un filtro del input o no. Igualmente va a devolver los datos paginados y pasados por el filtro hardcodeado.
     *
     * 1. Obtengo el valor del input que llega del front, viene en la clave 'filter' y subclave 'buscador', por defecto null si está vacío.
     * 2. Obtengo el valor de la paginación, que viene en la clave 'page' del input, por defecto 1 su está vacío.
     * 3. Defino e inicializo una varibale para el filtro, si existe, llegara coomo un string, de forma que lo divido en plabras clave, usando el espacio para ello. La estructura de datos pasa a ser un array.
     * 4. Defino e inicializo una variable para la clausula en el filtrado, en este caso OR (Buscando -> Like this or like that)
     * 5. Defino un filtro hardcodeado, que es util cuando se está utilizando el soft_delete, que no es el caso actualmente.
     * '6. Elaboro y ejecuto la consulta base (sin filtros ni paginación)
     * 7. Evalúo la variable que tiene el filtro, si existe, cojo la consulta base ejecutada y le paso el or_like_where(), para filtrar la consulta por los campos que cumplen el filtro.
     * 8. Independientemente de si hay filtro o no pagino y agrego la clausula hardcodeada a la query. Entonces la estructura de datos y atiene la forma adecuada con las claves correctas.
     * 9. Evaluo si la variable no esta vacía para elaborar una response u otra (data + http code) || (mesanje de error + filtro usado + paginación + http code)
     *
     *  */
    public function getFilteredProviders_post()
    {

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

        //elaboro y hago query base (sin filtros ni paginación)
        $query = $this->provider;

        // evalúo filtro, para filtrar la consulta hecha
        if ($filtro !== null) {

            //sobreescribo la query base para agregar el filtro
            $query = $query->or_like_where(["cif", "nombre", "email", "phone"], $explodedFiltro, $separation);
        }

        // indepentientemente de si hay filtro o no pagino y agrego clausula a la query
        $datos = $query->paginate($page, $where, 10);

        // en este punto $datos es un array asociativo con los datos de la consulta (filtrada) y la paginación
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
        if (!empty($datos['data'])) {
            // response parseada JSON (datos + clave "code" con el CODE)
            $this->response($datos, self::HTTP_OK, self::CODE_OK);
        } else {
            $this->response([
                "error" => "No se encontraron proveedores",
                "filter" => $filtro,
                "page" => $page
            ], self::HTTP_NOT_FOUND, self::CODE_BAD);
        }
    }
}
