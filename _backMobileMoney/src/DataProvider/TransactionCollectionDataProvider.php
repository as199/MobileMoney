<?php


namespace App\DataProvider;


use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Transaction;
use App\Repository\ClientRepository;
use App\Repository\TransactionRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TransactionCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $tokenStorage;
    /**
     * @var TransactionRepository
     */
    private TransactionRepository $transactionRepository;
    /**
     * @var ClientRepository
     */
    private ClientRepository $clientRepository;
    /**
     * @var UtilisateurRepository
     */
    private UtilisateurRepository $utilisateurRepository;

    /**
     * TransactionCollectionDataProvider constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param TransactionRepository $transactionRepository
     * @param ClientRepository $clientRepository
     * @param UtilisateurRepository $utilisateurRepository
     */
    public function __construct(TokenStorageInterface $tokenStorage,
                                TransactionRepository $transactionRepository,
                                ClientRepository $clientRepository,
                                UtilisateurRepository $utilisateurRepository
    )
    {
        $this->tokenStorage = $tokenStorage;
        $this->utilisateurRepository = $utilisateurRepository;
        $this->transactionRepository = $transactionRepository;
        $this->clientRepository = $clientRepository;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Transaction::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): JSONResponse
    {
        $data = [];
        $t = 0;
        if($this->tokenStorage->getToken()->getUser()->getRoles()[0] === "ROLE_AdminSysteme"){
            $transactions =  $this->transactionRepository->findAll();
           $i =0 ;
            foreach($transactions as $key => $transaction){

               if($transaction->getDateEnvoi() !=null){
                   $data[$i]['ttc'] = $transaction->getTotalCommission();
                   $data[$i]['montant'] = $transaction->getMontant();
                   $data[$i]['id'] = $transaction->getId();
                  $data[$i]['date'] = $transaction->getDateEnvoi()->format('Y-m-d ');
                  $data[$i]['commission'] = $transaction->getCommissionDepot();
                  $data[$i]['type'] = "depot";
                  $client = $this->utilisateurRepository->findOneBy(['id'=>$transaction->getUserEnvoi()->getId()]);
                  $data[$i]['nom'] = $client->getNomComplet();
               }

               $i++;
               $t++;
            }
            foreach($transactions as $key => $transaction){

                if($transaction->getDateRetrait() !=null){
                    $data[$i]['ttc'] = $transaction->getTotalCommission();
                    $data[$i]['montant'] = $transaction->getMontant();
                    $data[$i]['id'] = $transaction->getId();
                    // dd($transaction);
                    $data[$i]['date'] = $transaction->getDateRetrait()->format('Y-m-d');
                    $data[$i]['commission'] = $transaction->getCommissionRetrait();
                    $data[$i]['type'] = "retrait";
                    $client = $this->utilisateurRepository->findOneBy(['id'=>$transaction->getUserRetrait()->getId()]);
                    $data[$i]['nom'] = $client->getNomComplet();

                }
                $i++;
                $t++;
            }
           
        }else{
            $user = $this->tokenStorage->getToken()->getUser()->getId();
            $transactions = $this->transactionRepository->findBy(['userEnvoi'=>$user]);

            $transactionsR = $this->transactionRepository->findBy(['userRetrait'=>$user]);
            foreach($transactions as $key => $transaction){
                $data[$t]['ttc'] = $transaction->getTotalCommission();
                $data[$t]['montant'] = $transaction->getMontant();
               if($transaction->getDateEnvoi() !=null){
                  $data[$t]['date'] = $transaction->getDateEnvoi()->format('Y-m-d');
                  $data[$t]['commission'] = $transaction->getCommissionDepot();
                  $data[$t]['type'] = "depot";
                  $client = $this->clientRepository->findOneBy(['id'=>$transaction->getClientEnvoi()->getId()]);
                  $data[$t]['nom'] = $client->getPrenom() ." ".$client->getNom();
               }
               $t++;
               
            }
            foreach($transactionsR as $key => $trans){
                $data[$t]['montant'] = $trans->getMontant();
               if($trans->getDateRetrait() !=null){
                   $data[$t]['commission'] = $transaction->getCommissionRetrait();
                  $data[$t]['date'] = $trans->getDateRetrait()->format('Y-m-d');
                  $data[$t]['type'] = "retrait";
               }
            }
        }
        

        return new JSONResponse(['data'=>$data],200);

       // gné kanolé
    }


}