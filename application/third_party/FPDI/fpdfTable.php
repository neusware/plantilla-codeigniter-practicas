<?php
/*******************************************************************************
* FPDF                                                                         *
*                                                                              *
* Version: 1.81                                                                *
* Date:    2020-09-25                                                          *
* Author:  Antonio Gálvez Ruz                                                  *
*******************************************************************************/

require_once 'fpdf.php';

class PDF_MC_Table extends FPDF
{
	var $widths;
	var $aligns;

	function SetWidths($w)
	{
		//Set the array of column widths
		$this->widths=$w;
	}

	function SetAligns($a)
	{
		//Set the array of column alignments
		$this->aligns=$a;
	}

	function Row($data, $line_height = 5, $a = 'J', $style = "D", $action = false)
	{
		//Calculate the height of the row
		$nb=0;
		for($i=0;$i<count($data);$i++)
				$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
		$h=$line_height*$nb;
		//Issue a page break first if needed
		$this->CheckPageBreak($h);
		//Draw the cells of the row
		for($i=0;$i<count($data);$i++)
		{
				$w=$this->widths[$i];
				//Save the current position
				$x=$this->GetX();
				$y=$this->GetY();
				//Draw the border
				$this->SetLineWidth(0.1);
				$this->Rect($x,$y,$w,$h,$style);
				//Print the text
				$this->SetFillColor(255,255,255);
				$this->MultiCell($w,$line_height,utf8_decode($data[$i]),0,$a);
				//Put the position to the right of the cell
				$this->SetXY($x+$w,$y);
		}
		//Go to the next line
		$this->Ln($h);
	}

	function CheckPageBreak($h)
	{
		//If the height h would cause an overflow, add a new page immediately
		if($this->GetY()+$h>$this->PageBreakTrigger)
				$this->AddPage($this->CurOrientation);
	}

	function NbLines($w,$txt)
	{
		//Computes the number of lines a MultiCell of width w will take
		$cw=&$this->CurrentFont['cw'];
		if($w==0)
				$w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		$s=str_replace("\r",'',$txt);
		$nb=strlen($s);
		if($nb>0 and $s[$nb-1]=="\n")
				$nb--;
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$nl=1;
		while($i<$nb)
		{
				$c=$s[$i];
				if($c=="\n")
				{
						$i++;
						$sep=-1;
						$j=$i;
						$l=0;
						$nl++;
						continue;
				}
				if($c==' ')
						$sep=$i;
				$l+=$cw[$c];
				if($l>$wmax)
				{
						if($sep==-1)
						{
								if($i==$j)
										$i++;
						}
						else
								$i=$sep+1;
						$sep=-1;
						$j=$i;
						$l=0;
						$nl++;
				}
				else
						$i++;
		}
		return $nl;
	}

	function Footer()
	{
		// Posición a 1,5 cm del final
		$this->SetY(-15);
		// Arial itálica 8
		$this->SetFont('Arial','I',8);
		// Color del texto en gris
		$this->SetTextColor(128);
		// Número de página
		$this->Cell(0,10,utf8_decode('Página ').$this->PageNo(),0,0,'C');
	}
}