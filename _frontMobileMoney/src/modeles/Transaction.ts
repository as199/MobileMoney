export interface Transaction{
  montant: number,
  status: boolean,
  type: string,
  clientenvoi: Client,
  clientRetrait: Client,
  numero: string
}

export interface Client{
  cni: string,
  nom: string,
  prenom: string,
  adresse: string,
  telephone: string
}
