<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Export extends CI_Controller
{
 
    public function index()
    {
        $mpdf = new \Mpdf\Mpdf();
        $html = $this->load->view('html_to_pdf', [], true);
        $mpdf->WriteHTML($html);
        $mpdf->Output(); // opens in browser
        //$mpdf->Output('arjun.pdf','D'); // it downloads the file into the user system, with give name
    }
}
