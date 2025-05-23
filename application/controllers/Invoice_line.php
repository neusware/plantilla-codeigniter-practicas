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

        // restricciones _check_rol()
        // $this->restrictions = array(
        //     "getFilteredProducts" => array(
        //     "groups_allowed" => ["admin"]
        //     )
        // );

        parent::__construct();
    }

    // todo getFilteredInvoice_lines
}
