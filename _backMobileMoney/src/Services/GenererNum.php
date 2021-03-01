<?php


namespace App\Services;
use App\Repository\CompteRepository;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;

class GenererNum
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var TransactionRepository
     */
    private TransactionRepository $transactionRepository;
    /**
     * @var CompteRepository
     */
    private CompteRepository $compteRepository;


    public function __construct(CompteRepository $compteRepository,TransactionRepository $transactionRepository)
    {
       $this->compteRepository = $compteRepository;
       $this->transactionRepository = $transactionRepository;
    }

    public function genrecode($initial,$type): string
    {
        $an = Date('y');
        $cont = $this->getLastCompte($type);
        $long = strlen($cont);
        return str_pad($initial.$an, 11-$long, "0").$cont;
    }

    private function getLastCompte($val): int
    {
        if($val === 'compte'){
            $repository = $this->compteRepository;
        }elseif ($val === 'transaction'){
            $repository = $this->transactionRepository;
         }
        $compte = $repository->findBy([], ['id'=>'DESC']);
        if(!$compte){
            $cont= 1;
        }else{
            $cont = ($compte[0]->getId()+1);
        }
        return $cont;
    }

}