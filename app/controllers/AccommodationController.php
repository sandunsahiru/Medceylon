<?php

namespace App\Controllers;

use App\Models\Accommodation;


class AccommodationController extends BaseController
{
    private $accommodationModel;

    public function __construct()
    {
        parent::__construct();
        $this->accommodationModel = new Accommodation();
    }

    public function accommodations()
    {
        try{
            error_log("Accommodations method invoked");
            $accommodations = $this->accommodationModel->getAllAccommodations();

            if  (!$accommodations || count($accommodations) === 0) {
                error_log("No accommodations found");
                $this->session->setFlash('error', 'No accommodations available');
            }
            
            $data = [
                'accommodations' => $accommodations,
                'error' => $this->session->getFlash('error'),
                'success' => $this->session->getFlash('success'),
                'basePath' => $this->basePath
            ];
            
            echo $this->view('/accommodation/accommodation-providers', $data);
            exit();
        } catch (\Exception $e) {
            error_log("Error in accommodations: " . $e->getMessage());
            $this->session->setFlash('error', $e->getMessage());
            header('Location: ' . $this->url('error/404'));
            exit();
        }
    }
}

