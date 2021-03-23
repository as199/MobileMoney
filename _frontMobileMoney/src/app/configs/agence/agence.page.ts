import { LoadingController, AlertController, ToastController } from '@ionic/angular';
import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { AuthenticationService } from 'src/app/services/authentication.service';

@Component({
  selector: 'app-agence',
  templateUrl: './agence.page.html',
  styleUrls: ['./agence.page.scss'],
})
export class AgencePage implements OnInit {

  visible: boolean = true;
  credentials: FormGroup;
  users: any;
  agence = [];
  avatar: string;

  constructor(
    private authService: AuthenticationService,
    private fb: FormBuilder,
    private loadingCtrl: LoadingController,
    private alertCtrl: AlertController,
    private toastCtrl: ToastController
  ) {

    this.chargerUser();
  }

  ngOnInit() {
    this.chargerAgence();
    this.authService.refresh$.subscribe(
      ()=> {
        this.chargerAgence();
      });

    this.credentials = this.fb.group({
      nomAgence: ['', [Validators.required, Validators.min(1)]],
      adresse: ['', [Validators.required]],
      userAgence: [, []]
    });


  }

//#region les fonctions de chargement au demarrage
  chargerUser(){
    this.authService.GetUserNotAgence().subscribe((data) => {
     this.users = data.data;
    });
  }

  chargerAgence(){
    this.authService.GetAgence().subscribe((data) => {
     this.agence= data;
    });
  }
//#endregion

//#region partie des navigations
  previous(){
    this.visible =true;
  }
  next(){
    this.visible =false;
  }
//#endregion

//#region les fonction pour l'ajout et la suppression d'une agence
  async Ajouter(){
    const loading = await this.loadingCtrl.create({
      message: 'Please wait...'
    });
    await loading.present();
    this.authService.AddAgence(this.credentials.value).subscribe(
      async (data) => {
        console.log(data);
      this.credentials.reset();
      await loading.dismiss();
      const alert = await this.alertCtrl.create({
        header: 'Succée',
        message: 'Agence crée avec succée',
        buttons: ['OK']
      });
    await alert.present();
    },async err => {
      console.log(err);
      await loading.dismiss();
      const alert = await this.alertCtrl.create({
        header: 'Failed',
        cssClass: "my-custom-class",
        message: 'Erreur de creation de l\'agence ',
        buttons: ['OK']
      });
      await alert.present();
    });
  }

   async delete(id){
     const alert = await this.alertCtrl.create({
      cssClass: 'my-custom-class',
      header: 'Confirmation',
      message: `Etes vous sur de vouloir supprimer cette utilisateur ?`,
      buttons: [
        {
          text: 'Cancel',
          role: 'cancel',
          cssClass: 'secondary',
          handler: () => {
          }
        }, {
          text: 'Ok',
          handler: async () => {
            const loading = await this.loadingCtrl.create();
             await loading.present();
            this.authService.DeleteAgence(id).subscribe(
              async (data) => {
                console.log(data);
                await loading.dismiss();
                this.credentials.reset();
                const alert = await this.alertCtrl.create({
                  header: 'Succée',
                  message: 'Utilisateur  supprimer avec succée',
                  buttons: ['OK']
                });
              await alert.present();
            }, async(error) => {
                console.log(error);
               await loading.dismiss();
              const alert = await this.alertCtrl.create({
                header: 'Failed',
                cssClass: "my-custom-class",
                message: 'Erreur lors de la suppression de l \'utilisateur',
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
//#endregion
}
