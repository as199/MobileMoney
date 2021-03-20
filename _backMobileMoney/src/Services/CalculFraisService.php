<?php


namespace App\Services;


use App\Repository\CommissionRepository;
use App\Repository\TarifRepository;

class CalculFraisService
{
    /**
     * @var TarifRepository
     */
    private TarifRepository $tarifRepository;
    /**
     * @var CommissionRepository
     */
    private CommissionRepository $commissionRepository;

    /**
     * CalculFraisService constructor.
     * @param TarifRepository $tarifRepository
     * @param CommissionRepository $commissionRepository
     */
    public function __construct(TarifRepository  $tarifRepository,
                                CommissionRepository $commissionRepository)
    {
        $this->tarifRepository = $tarifRepository;
        $this->commissionRepository = $commissionRepository;

    }

    public function CalcFrais($montant){
        $data = $this->tarifRepository->findAll();
        $frais = 0;
        foreach ($data as $value){
            if($montant>=2000000){
                $frais = ($value->getFraisEnvoi()*$montant)/100;
            }else{
                switch($montant){
                    case $montant> $value->getMontantMIn() && $montant<=$value->getMontantMax():
                        $frais = $value->getFraisEnvoi();
                        break;
                }
            }

        }
        return $frais;
        // 500 -> 425 = 925
    }


    public function DeCalcFrais($montant){
       // $data =[];
       //$frais=  $this->CalcFrais($montant);
        $datas = $this->tarifRepository->findAll();
        $frais = 0;
        $data =[];
        foreach ($datas as $value){
            if($montant>=2000000){
                $frais = ($value->getFraisEnvoi()*$montant)/100;
            }else{
                switch($montant){
                    case $montant> $value->getMontantMIn() && $montant <=$value->getMontantMax():
                        $frais = $value->getFraisEnvoi();
                        break;
                }
            }


        }
        $data['frais'] = $frais;
        $data['montantSend'] = $montant - $frais;
        return $data;
        // 500 -> 425 = 925
    }

    public function CalcPart($montant): array
    {
        $data = $this->commissionRepository->findAll();
        $Part = array();
        foreach($data as $value){
            $Part['etat']= ($montant* $value->getEtat())/100;
            $Part['transfert']= ($montant*$value->getTransfertArgent())/100;
            $Part['Depot']= ($montant*$value->getOperateurDepot())/100;
            $Part['Retrait']= ($montant*$value->getOperateurRetrait())/100;
        }
        return $Part;
    }

}