<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Format class
 * Help convert between various formats such as XML, JSON, CSV, etc.
 *
 * @author    Phil Sturgeon, Chris Kacerguis, @softwarespot
 * @license   http://www.dbad-license.org/
 */

require_once APPPATH.'third_party/FPDI/fpdf.php';
require_once APPPATH.'third_party/FPDI/autoload.php';

class Pdf extends setasign\Fpdi\Fpdi{

  public function test() {
    echo "asdasd";
  }


  public function pasteSignatures($path_origen, $path_destino, $nombre_firmado, $imagen) {

    //Set the source PDF file
    $pageCount = $this->setSourceFile($path_origen);

    // var_dump($pageCount);

    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
      // import a page
      $templateId = $this->importPage($pageNo);

      // get the size of the imported page
      $size = $this->getTemplateSize($templateId);

      // create a page (landscape or portrait depending on the imported page size)
      if ($size[0] > $size[1]) {
          $this->AddPage('L', array($size[0], $size[1]));
      } else {
          $this->AddPage('P', array($size[0], $size[1]));
      }

      // use the imported page
      $this->useTemplate($templateId);

      // Image(file, x, y, w ,h )
      $this->Image($imagen, 25,278,25,12);
    }

    $this->Output('F', $path_destino.$nombre_firmado);

    return true;

  }

}
