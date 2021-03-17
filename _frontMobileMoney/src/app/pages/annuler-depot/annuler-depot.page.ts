import { Component, OnInit } from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import { AlertController, LoadingController } from '@ionic/angular';
import {AuthenticationService} from '../../services/authentication.service';

@Component({
  selector: 'app-annuler-depot',
  templateUrl: './annuler-depot.page.html',
  styleUrls: ['./annuler-depot.page.scss'],
})
export class AnnulerDepotPage implements OnInit {
  credentials: FormGroup;

  constructor( private fb: FormBuilder,
               private alertCtrl: AlertController,
               private loadingCtrl: LoadingController,
               private authService: AuthenticationService) { }

  ngOnInit() {
    this.credentials = this.fb.group({
      numero: ['',Validators.required]
    })
  }

  async annuler() {
    const alert = await this.alertCtrl.create({
      cssClass: 'my-custom-class',
      header: 'Confirmation',
      message: `Voulez vous annuler la transaction N° ${this.credentials.value.numero}`,
      buttons: [
        {
          text: 'Cancel',
          role: 'cancel',
          cssClass: 'secondary',
          handler: () => {
          }
        }, {
          text: 'Confirm',
          handler: async () => {
            const loading = await this.loadingCtrl.create();
            await loading.present();
            this.authService.annulerTransaction(this.credentials.value).subscribe(
              async (data) => {
                console.log(data);
                await loading.dismiss();
                this.credentials.reset();
                const alert = await this.alertCtrl.create({
                  header: 'Annulation ',
                  message: `Transaction annuler avec success`,
                  buttons: ['OK']
                });
                await alert.present();
              }, async (error) => {
                await loading.dismiss();
                const alert = await this.alertCtrl.create({
                  header: 'Failed',
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
}
