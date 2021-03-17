<?php


namespace App\DataProvider;


use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Transaction;
use App\Repository\ClientRepository;
use App\Repository\TransactionRepository;
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
     * TransactionCollectionDataProvider constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param TransactionRepository $transactionRepository
     * @param ClientRepository $clientRepository
     */
    public function __construct(TokenStorageInterface $tokenStorage,
                                TransactionRepository $transactionRepository,
                                ClientRepository $clientRepository
    )
    {
        $this->tokenStorage = $tokenStorage;
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
        
        if($this->tokenStorage->getToken()->getUser()->getRoles()[0] === "ROLE_AdminSysteme"){
            $transactions =  $this->transactionRepository->findAll();
           
            foreach($transactions as $key => $transaction){
                $data[$key]['ttc'] = $transaction->getTotalCommission();
                $data[$key]['montant'] = $transaction->getMontant();
                $data[$key]['id'] = $transaction->getId();
               if($transaction->getDateEnvoi() !=null){
                  $data[$key]['date'] = $transaction->getDateEnvoi()->format('Y-m-d ');
                  $data[$key]['commission'] = $transaction->getCommissionDepot();
                  $data[$key]['type'] = "depot";
                  $client = $this->clientRepository->findOneBy(['id'=>$transaction->getClientEnvoi()->getId()]);
                  $data[$key]['nom'] = $client->getPrenom() ." ".$client->getNom();
               }
               if($transaction->getDateRetrait() !=null){
                  // dd($transaction);
                $data[$key]['date'] = $transaction->getDateRetrait()->format('Y-m-d');
                $data[$key]['commission'] = $transaction->getCommissionRetrait();
                $data[$key]['type'] = "retrait";
                $client = $this->clientRepository->findOneBy(['id'=>$transaction->getClientRecepteur()->getId()]);
                $data[$key]['nom'] = $client->getPrenom() ." ".$client->getNom();
                
             }
            }
           
        }else{
            $user = $this->tokenStorage->getToken()->getUser()->getId();
            $transactions = $this->transactionRepository->findBy(['userEnvoi'=>$user]);
            $transactionsR = $this->transactionRepository->findBy(['userRetrait'=>$user]);
            foreach($transactions as $key => $transaction){
                $data[$key]['ttc'] = $transaction->getTotalCommission();
                $data[$key]['montant'] = $transaction->getMontant();
               if($transaction->getDateEnvoi() !=null){
                  $data[$key]['date'] = $transaction->getDateEnvoi()->format('Y-m-d');
                  $data[$key]['commission'] = $transaction->getCommissionDepot();
                  $data[$key]['type'] = "depot";
                  $client = $this->clientRepository->findOneBy(['id'=>$transaction->getClientEnvoi()->getId()]);
                  $data[$key]['nom'] = $client->getPrenom() ." ".$client->getNom();
               }
            }
            foreach($transactionsR as $key => $trans){
                $data[$key]['ttc'] = $trans->getTotalCommission();
                $data[$key]['montant'] = $trans->getMontant();
               if($trans->getDateRetrait() !=null){
                  $data[$key]['date'] = $trans->getDateRetrait()->format('Y-m-d');
                  $data[$key]['commission'] = $trans->getCommissionRetrait();
                  $data[$key]['type'] = "retrait";
                  $client = $this->clientRepository->findOneBy(['id'=>$trans->getClientEnvoi()->getId()]);
                  $data[$key]['nom'] = $client->getPrenom() ." ".$client->getNom();
               }
            }
        }
        

        return new JSONResponse(['data'=>$data],200);

       // gné kanolé
    }


}