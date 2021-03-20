import { LoadingController, AlertController } from '@ionic/angular';
import { Component, OnInit } from '@angular/core';
import { AuthenticationService } from 'src/app/services/authentication.service';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import { Transaction } from 'src/modeles/Transaction';

@Component({
  selector: 'app-retrait',
  templateUrl: './retrait.page.html',
  styleUrls: ['./retrait.page.scss'],
})
export class RetraitPage implements OnInit {
  myTransaction: Transaction;
  parti1 = 1;
  parti2 = 0;
  etatColor1 = 'danger';
  etatColor2 = 'white';
  credentials: FormGroup;
  clientEnvoi= [];
  clientRetrait= []
  montant= '';
  dateEnvoi='';
  nomEmetteur ="";
  nomRecepteur="";
  avatar: string;
  etat = 'beneficiare';
  constructor(
    private authService: AuthenticationService,
    private fb: FormBuilder,
    private alertCtrl: AlertController,
    private loadingCtrl: LoadingController
    ) {
      this.myTransaction = {} as Transaction;
    }

  ngOnInit() {
    this.credentials = this.fb.group({
      code: ['', [Validators.required, Validators.minLength(9),Validators.maxLength(9)]],
      cni: ['', [Validators.required, Validators.minLength(5)]],
      type: ['retrait', [Validators.required, Validators.minLength(5)]]
    });


  }
  previous(){
  this.parti1 = 1;
  this.parti2 = 0;
  }
  next(){
    this.parti1 = 0;
    this.parti2 = 1;
  }


 async Recherche() {
    const loading = await this.loadingCtrl.create();
    await loading.present();

    let num = this.credentials.value.code;
    this.authService.findTransactionByCode(this.credentials.value).subscribe(
      async (data) =>{
        await loading.dismiss();
        this.clientEnvoi = data["data"]["clientEnvoi"];
        this.clientRetrait = data["data"]["clientRecepteur"];
        this.montant = data["data"]["montant"];
        this.dateEnvoi = data["data"]["dateEnvoi"];
        this.nomEmetteur = data["data"]["clientEnvoi"].prenom +" "+data["data"]["clientEnvoi"].nom;
        this.nomRecepteur = data["data"]["clientRecepteur"].prenom +" "+data["data"]["clientRecepteur"].nom;
        console.log(data["data"]["clientEnvoi"]);
    },async(error) => {
      this.credentials.reset();
      await loading.dismiss();
        const alert = await this.alertCtrl.create({
          header: 'Failed',
          cssClass: 'my-custom-class-error',
          message: 'Ce code de transfert n\'existe pas',
          buttons: ['OK']
        });
        await alert.present();
      })
  }

async retirer(){
  console.log(this.credentials.value);

  const alert = await this.alertCtrl.create({
    cssClass: 'my-custom-class',
    header: 'Confirmation',
    message: `<div class="affiche">
              Bénéficiaire  <br> <p>${this.nomRecepteur}</p> <br>
              Téléphone  <br><p>${this.clientRetrait['telephone']}</p><br>
              N CNI  <br><p>${this.credentials.value.cni}</p><br>
              MONTANT RECU  <br><p>${this.montant}</p><br>
              EMETTEUR  <br><p>${this.nomEmetteur}</p> <br>
              Téléphone  <br><p>${this.clientEnvoi['telephone']}</p><br>
      </div>`,
    buttons: [
      {
        text: 'Annuler',
        role: 'cancel',
        cssClass: 'secondary',
        handler: () => {
          console.log('Confirm Cancel');
        }
      }, {
        text: 'Confirmer',
        handler: async () => {
          const loading = await this.loadingCtrl.create();
           await loading.present();
          this.authService.Transaction(this.credentials.value).subscribe(
            async (data) => {
              await loading.dismiss();
            const alert = await this.alertCtrl.create({
              header: 'Succée',
              message: 'Retrait effectuée avec succée',
              buttons: ['OK']
            });
            await alert.present();
          }, async(error) => {
            console.log(error);
            await loading.dismiss();
            const alert = await this.alertCtrl.create({
              header: 'Failed',
              cssClass: "my-custom-class",
              message: error.error.message,
              buttons: ['OK']
            });
            await alert.present();
          })
        }
      }
    ]
  });

  await alert.present();
}

  get code() {
    return this.credentials.get('code');
  }
  get cni() {
    return this.credentials.get('cni');
  }

  suivant() {
    this.parti1 = 0;
    this.parti2 = 1;
    this.etat = 'emetteur';
  }
}
