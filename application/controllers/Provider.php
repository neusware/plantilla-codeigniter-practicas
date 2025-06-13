<?php

defined('BASEPATH') or exit('No direct script access allowed');

// TODO, qué es el dropdown_get (creo q en user es formulario) | Son necesarias restricciones para métodos check_roles/restrict() | Si soft-delete, hay que filtrar las busquedas por is_hidden | comentar debidamente, presentar

// clase
class Provider extends MY_Controller
{
    // constructor
    public function __construct()
    {
        // configuro campos globales, propiedades, atributos

        // apunto al modelo que va gestionar, !cargarlo en autoload.php
        $this->model = "provider";

        // !que campos pongo en el dropdown
        $this->dropdownLabel = array("cif", "nombre");

        // archivos asociados
        $this->upload_fields = "imagen";

        // Configura el language_tag explícitamente
        $this->language_tag = "provider";

        // !procede
        // restricciones para usar metodos, en checkea _check_rol en parent::MY_Controller
        // $this->restrictions = array(
        //     "getFilteredProviders" => array(
        //     "groups_allowed" => ["admin"]
        //     )
        // );

        // inicializar la instancia con el constructor de la clase padre
        parent::__construct();

        // Tras inicializar constructor padre cargo bibliotecas adicionales, para hacer validaciones en este caso
        $this->load->library('form_validation');
    }

    // ------------------------------métodos personalizados-----------------------------

    /**
     ** Obtener los proveedores filtrados por el input que llega del request +clausula hardcodeada + restriccion en la consulta + paginación.
     *
     * Este es el método que va usar el front para hacer las lecturas, puede incluir o no un filtro (proveniente del input en front) y paginación, ya que cuenta con valores por defecto; por lo que igualmente va a devolver los datos paginados y  además pasados por un filtro (where) hardcodeado en el método.
     *
     * *Estructura request tipo:
     * {
     *  "filter": {
     *       "buscador": "Ybar"
     *   },
     *  "page": 1
     * }
     *
     * Proceso:
     * 1. Obtengo el valor del input que llega desde el front en el request, viene en la clave 'filter' del y subclave 'buscador', por defecto null si está vacío.
     * 2. Obtengo el valor de la paginación, que viene clave 'page' del input, por defecto 1 si
     * está vacío.
     * 3. Defino e inicializo una varibale para el filtro, si existe, llegara como un string, de forma
     * que lo divido en palabras clave (explode), usando el espacio (%20) para ello. Conviertiendo la
     * cadena en estructura de datos array.
     * 4. Defino e inicializo una variable para la clausula en el filtrado, en este caso OR (Buscando -> Like this OR like that)
     * 5. Defino un filtro hardcodeado, que es útil cuando se está utilizando el soft_delete, que no es el caso actualmente.
     * '6. Elaboro y ejecuto la consulta base a partir del modelo (sin filtros ni paginación)
     * 7. Evalúo la variable que contiene el filtro, si existe, cojo la consulta base ejecutada y le paso el or_like_where(campos, $explodedFiltro, $separation), para filtrar la consulta por los campos que cumplen el filtro y la cláusula.
     * 8. Independientemente de si hay filtro o no, pagino y agrego la última clausula hardcodeada $where a la query. Entonces la estructura de datos ya tiene el formato adecuada, con las claves correctas.
     * 9. Evaluola variable para elaborar una response u otra (data + http code) || (mesanje de error + filtro usado + paginación + http code)
     *
     *
     * */
    public function getFilteredProviders_post()
    {

        // evalúo el input con claves, para obtener valor  o x defecto
        $filtro = $this->post('filter')['buscador'] ?? null;
        // trato obtener paginación
        $page = $this->post('page') ?? 1;

        //divido el filtro (input), a partir de espacios (%20) en palabras clave para optimizar consulta
        $explodedFiltro = $filtro ? explode("%20", $filtro) : null;
        // clausula LIKE ... OR ... OR ... para cada palabra clave, como parámetro en el método or_like_where()
        $separation = "OR";

        //clausula hardcodeada, parametro en paginate() - is_hidden para soft_delete
        $where = [
            'is_hidden' => false // 0
        ];

        //elaboro y hago query base (sin filtros ni paginación)
        $query = $this->provider;

        // evalúo filtro, para filtrar la consulta base
        if ($filtro !== null) {

            //sobreescribo la query base para agregar el filtro
            $query = $query->or_like_where(["cif", "nombre", "email", "phone"], $explodedFiltro, $separation);
        }

        // indepentientemente de si hay filtro o no pagino y agrego clausula a la query
        $datos = $query->paginate($page, $where, 10);

        // en este punto $datos es un array asociativo con los datos de la consulta (filtrada) y la paginación
        /*
            $datos = [
    0           "data" => [
                    "id" => x,
                    "nombre" => x,
                    "cif" => x,
                    "email" => x,
                    "telefono" => x,
                    "imagen" => x,
                    (...)
                ],
    1            "counts" => [
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


    /**
     * * Crear registro proveedor - Validación previa CIF
     *
     * Método para validar la unicidad del valor CIF antes de crear un nuevo registro de proveedor.
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
        $data = $this->post("data") != null ? $this->post("data") : $this->post();
        // Decodificar el JSON si es un string en array asociativo
        if (is_string($data)) $data = json_decode($data, true);

        // Validación
        if (isset($data['cif'])) {

            // seteo los el conjunto de datos evaluar
            $this->form_validation->set_data($data);
            // establezco la regla de validación, requerido y único en el campo cif de la tabla providers
            $this->form_validation->set_rules('cif', 'CIF', 'required|is_unique[providers.cif]');

            // ejecuto la validación
            if ($this->form_validation->run() === FALSE) {
                // Si la validación falla, elaboro una response de error
                $this->response(
                    // mensajes parametrizados en form_valiadation_lang.php
                    array("message" => "El valor del campo CIF ha de ser unique"),
                    self::HTTP_OK,
                    self::CODE_SHOW_ERROR_MESSAGE

                );
                return;
            }

            // Si la validación es exitosa, el flujo continúa, ejecuto el create de la clase padre, no le paso el request, pq lo coge de la instancia del controlador.
            parent::create_post();
        }
    }

    /**
     * * Actualizar registro proveedor - validación previa CIF
     *
     *
     *  */
    public function update_post()
    {

        // extraigo datos
        $data = $this->post("data") != null ? $this->post("data") : $this->post();

        // transpilo a array asociativo
        if (is_string($data)) $data = json_decode($data, true);

        // validacion, he de excluir el registro actual
        // if(isset($data["id"]) && isset($data["cif"])){

        // seteo el conjunto de datos a validar
        $this->form_validation->set_data($data);

        // id required
        $this->form_validation->set_rules('id', 'ID', 'required', ['required' => 'El campo {field} es obligatorio']);

        // cif unique - regla excluyendo el registro a actualizar (caso de no se actualiza el CIF) - SELECT * FROM providers WHERE cif = 'A12345678' AND id != 1;
        $this->form_validation->set_rules(
            'cif',
            'CIF',
            'required|is_unique[providers.cif.id.' . $data['id'] . ']',
            // mensajes fb custom
            [
                'required' => 'El campo {field} es obligatorio.',
                'is unique' => 'El {field} ya existe en el sistema'
            ]
        );

        // ejecuto la validación y evalúo
        if ($this->form_validation->run() === FALSE) {
            // Si la validación falla, response error con mensajes custom en set_rules() usar error_array() |mensajes custom form_valiadation_lang.php usar error_string(),
            $this->response(
                array("message" =>$this->form_validation->error_string()),
                self::HTTP_OK,
                self::CODE_SHOW_ERROR_MESSAGE
            );
            return;
        }
        // }
        // pasa la validación y el request sigue hacie el metodo de la clase padre, no hace falta request x parámetro, lo tiene el objeto controlador
        parent::update_post();
    }


    /**
     ** Obtener todos los proveedores + paginación  + clausula a piñon
     *
     *  Es similar al metodo all_get() de la clase padre, con la única diferencia de agregar el filtro a piñon.
     *
     *  1. Defino e inicializo una clausula a piñon, en este caso preparada para los soft-deletes
     *  1. Elaboro y ejecuto una consulta base pasándole la paginación y la cláusula establecida
     *  1. Evalúo la variable $datos en clave 'data' para elaborar un mensaje de respuesta u otro
     *
     */
    // public function getAllProviders_get($pagina)
    // {
    //     // Filtros opcionales
    //     $where = [
    //         'is_hidden' => false
    //     ];

    //     // Consulta filtrada (opt) + paginación
    //     $datos = $this->provider->paginate($pagina, $where, 10);

    //     // Evalúo el resultado de la consulta para reponder
    //     if (!empty($datos['data'])) {
    //         //  response (datos + code)
    //         $this->response($datos, self::HTTP_OK, self::CODE_OK);
    //     } else {
    //         //response code-error
    //         $this->response(["error" => "No se encontraron proveedores"], self::HTTP_NOT_FOUND, self::CODE_BAD);
    //     }
    // }

    //-
    /**
     ** Obtener proovedor por id
     *
     *  Obtener sólo un registro a partir del id. Este método es redundante, ya que es similar al data_get() de la clase padre.
     *
     * 1. Elaboro una consulta base filtrada por el id del proveedor, la alamceno en una variable.
     * 2. Evalúo el resultado de la consulta para elaborar una respuesta u otra. Sin utilizar los lang, elaboro la response de forma manual.
     *
     * */
    // public function getProvider_get($provider_id)
    // {

    //     // consulto y almaceno respuesta
    //     $datos = $this->provider->get($provider_id);

    //     // evaluo el resultado para controlar la response
    //     if (!empty($datos)) {
    //         // response (datos + code)
    //         $this->response($datos, self::HTTP_OK, self::CODE_OK);
    //     } else {
    //         // response code-error, se podría meter un lang->line
    //         $this->response(["error" => "No se encontraron proveedores"], self::HTTP_NOT_FOUND, self::CODE_BAD);
    //     }
    // }

    //- Crear proveedor checkando el cif (consulta) sin método parent
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

    /**
     * * Obtener un proveedor por su CIF.
     */
    // public function getByCIF($cif)
    // {
    //     // Consultar la base de datos para buscar un registro con el CIF proporcionado
    //     // $query= $this->provider->get();
    //     // $record = $query->where('cif', $cif);

    // $record = $this->provider->get_by(['cif' => $cif]);

    //     // Si encuentra un registro, devolverlo; si no, devolver null
    //     return $record !=null ? $record : null;
    // }



}
