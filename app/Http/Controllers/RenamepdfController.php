<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\Debugbar\Facade as Debugbar;

class RenamepdfController extends Controller
{



	public function renamepdf(Request $request){

		$path_to_directory = $request->get('dossier_cible');

		$contents = scandir($path_to_directory);

		$elts_a_chercher = array_unique($this->excelPdfinfo($request));

		$pdfs_non_trouver= array();
		$pdfs_trouver= array();
		$pdfs_test= array();

             // check if $contents is a directory and actually has items
		if (is_array($contents) && count($contents)) {
			foreach($elts_a_chercher as $pdf_excel) {
				$trouver = true ;

                foreach($contents as $item) { // loop through directory contents
                	 //$pdfs_test [] = $this->getnumPdf($item);
                    if ( (strtolower($this->getnumPdf($pdf_excel)) == strtolower($this->getnumPdf($item))) && $trouver) { 

                        rename($path_to_directory . "\\" . $item, $path_to_directory . "\\" . $pdf_excel); // rename 
                        $trouver= false;
                        $pdfs_trouver [] = ["id_pdf" =>$this->getnumPdf($pdf_excel) ,"nom_pdf"=>$pdf_excel] ;
                        break; // no need to loop more, job's done
                    }
                }

                if ($trouver) {
                	$pdfs_non_trouver [] = ["id_pdf" =>$this->getnumPdf($pdf_excel),"nom_pdf"=>"non trouvé"];
                }
            }
        }
     
       return response()->json(['pdfs_trouver'=> $pdfs_trouver , 'pdfs_non_trouver' => $pdfs_non_trouver]);


    }


    public function excelPdfinfo( Request $request){

    	$noms_fichiers=array();

    	if($request->has('fichierexcel')){
    		$sheet = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    		$sheet = $sheet->load($request->fichierexcel);

    		$highestRow = $sheet->getActiveSheet()->getHighestRow(); 
    		$highestColumn = $sheet->getActiveSheet()->getHighestColumn();

    		$columnLoopLimiter = $highestColumn;
    		++$columnLoopLimiter;
        // get the column headings as a simple array indexed by column name
    		$headings = $sheet->getActiveSheet()->rangeToArray('A1:' . $highestColumn . 1, NULL, TRUE, FALSE, TRUE)[1];
    		$col_chemin =array_search('chemin', $headings);
    		$lettre_chemin_pdf = $col_chemin;
    		$rowdebut= 2;

    		$rowData = $sheet->getActiveSheet()->rangeToArray($lettre_chemin_pdf. $rowdebut . ':' . $lettre_chemin_pdf. $highestRow , NULL, TRUE, FALSE, TRUE);

        //  Loop through each data row of the worksheet in turn
    		for ($row = 2; $row <= $highestRow; $row++)
    		{ 
    			if (!empty($rowData[$row][$lettre_chemin_pdf])) {

    				$noms_fichiers[] = $rowData[$row][$lettre_chemin_pdf] ;
    			}
    		}

    	}else{

    	}

    	return $noms_fichiers;
    }


    public function getnumPdf($elt){
       
    	$pdfvar= $elt;
        $is_pdf1=strpos( $pdfvar, '.pdf' );
        $is_pdf2=strpos( $pdfvar, '.PDF' );  
        $rech="";
        if ($is_pdf1 || $is_pdf2) {
                    Debugbar::info($elt);
            $tab_split = explode("_", $pdfvar); 
            $len_tab = count($tab_split);
            Debugbar::info($len_tab);
            $rech = $tab_split[$len_tab -2].'_'.$tab_split[$len_tab -1];
            Debugbar::info($rech);

        }
    	return $rech;

    }

}
