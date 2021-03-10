import {Component, OnInit} from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import { AlertOptions } from '@capacitor/core';
import { AlertController, ToastController, LoadingController } from '@ionic/angular';
import { AuthenticationService } from 'src/app/services/authentication.service';
import {Client, Transaction} from '../../../modeles/Transaction';

@Component({
  selector: 'app-depot',
  templateUrl: './depot.page.html',
  styleUrls: ['./depot.page.scss'],
})
export class DepotPage implements OnInit {
  myTransaction: Transaction;
  etape = true;
  credentials: FormGroup;
  frais: any;
  montantTotal: any;
  constructor( private fb: FormBuilder,
               private toastrCtl: ToastController,
               public alertCtrl: AlertController,
               private loadingCtrl: LoadingController,
               private authService: AuthenticationService) {
    this.myTransaction = {} as Transaction;
    this.myTransaction.clientenvoi = {} as Client;
    this.myTransaction.clientRetrait = {} as Client;
  }

  ngOnInit() {
    this.credentials = this.fb.group({
      montant: ['', [Validators.required, Validators.min(1)]],
      clientenvoi:this.fb.group( {
        cni: ['15021585698698', [Validators.required, Validators.minLength(8)]],
        prenom: ['Assane', [Validators.required, Validators.minLength(2)]],
        nom: ['Dione', [Validators.required, Validators.minLength(2)]],
        telephone: ['778163676', [Validators.required, Validators.minLength(9)]],
      }),
      clientRetrait:this.fb.group( {
        cni: ['2154852154662', [Validators.required, Validators.minLength(8)]],
        prenom: ['Moussa', [Validators.required, Validators.minLength(2)]],
        nom: ['top', [Validators.required, Validators.minLength(2)]],
        telephone: ['77845213654', [Validators.required, Validators.minLength(9)]],
      }),
    });
  }

  async Create() {
  this.myTransaction.montant = this.credentials.value.montant;
  this.myTransaction.type = "depot";
  this.myTransaction.status = true;
  this.myTransaction.clientenvoi = this.credentials.value.clientenvoi;
  this.myTransaction.clientRetrait = this.credentials.value.clientRetrait;
  let Infos = {
    emetteur : this.myTransaction.clientenvoi.prenom +" "+this.myTransaction.clientenvoi.nom,
    telephone: this.myTransaction.clientenvoi.telephone,
    cni: this.myTransaction.clientenvoi.cni,
    montant: this.myTransaction.montant,
    recepteur: this.myTransaction.clientRetrait.prenom+" "+this.myTransaction.clientRetrait.nom,
    telephoneRep: this.myTransaction.clientRetrait.telephone
  }
    const alert = await this.alertCtrl.create({
      cssClass: 'my-custom-class',
      header: 'Confirmation',
      message: `<div class="affiche">
                Emetteur  <br> <p>${Infos?.emetteur}</p> <br>
                Téléphone  <br><p>${Infos?.telephone}</p><br>
                N CNI  <br><p>${Infos?.cni}</p><br>
                Récepteur  <br><p>${Infos?.recepteur}</p><br>
                Montant  <br><p>${Infos?.montant}</p> <br>
                Téléphone  <br><p>${Infos?.telephoneRep}</p><br>
        </div>`,
      buttons: [
        {
          text: 'Cancel',
          role: 'cancel',
          cssClass: 'secondary',
          handler: () => {
            console.log('Confirm Cancel');
          }
        }, {
          text: 'Ok',
          handler: async () => {
            const loading = await this.loadingCtrl.create();
             await loading.present();
            this.authService.Transaction(this.myTransaction).subscribe(
              async (data) => {
                await loading.dismiss();
              this.credentials.reset();
              const alert = await this.alertCtrl.create({
                header: 'Succée',
                message: 'Transaction effectuée avec succée',
                buttons: ['OK']
              });
              await alert.present();
            }, async(error) => {
              await loading.dismiss();
              const alert = await this.alertCtrl.create({
                header: 'Failed',
                cssClass: "my-custom-class",
                message: error.error,
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

  previous(){
    this.etape = true;
  }
  next(){
    this.etape = false;
  }
  get montant() {
    return this.credentials.get('montant');
  }

  async calculFrais() {
    const loading = await this.loadingCtrl.create();
    await loading.present();
    this.authService.calculator(this.credentials.value).subscribe(
      async (data) =>{
        await loading.dismiss();
        this.frais = data.data;
        this.montantTotal = data.data + this.credentials.value.montant;
      },async(error) => {
        await loading.dismiss();
          const alert = await this.alertCtrl.create({
            header: 'Failed',
            message: 'le montant doit etre superieur à 0',
            buttons: ['OK']
          });
          await alert.present();
        })
  }
}
