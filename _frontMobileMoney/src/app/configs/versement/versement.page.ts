import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AlertController, LoadingController, ToastController } from '@ionic/angular';
import { AuthenticationService } from 'src/app/services/authentication.service';

@Component({
  selector: 'app-versement',
  templateUrl: './versement.page.html',
  styleUrls: ['./versement.page.scss'],
})
export class VersementPage implements OnInit {
visible: boolean = false;
  comptes: any;
  credentials: FormGroup;
  constructor(
    private authService: AuthenticationService,
    private fb: FormBuilder,
    private loadingCtrl: LoadingController,
    private alertCtrl: AlertController,
    private toastCtrl: ToastController

    ) { 
      this.authService.getMyRole().then((role) => {
        if(role === 'ROLE_AdminSysteme'){
            this.visible = true;
           }
      });
    }
  async getComptes(){
    const loading = await this.loadingCtrl.create({
      message: 'Please wait...',
    });
    await loading.present();
  this.authService.GetCompte().subscribe(
    async data =>{
      await loading.dismiss();
      this.comptes = data["hydra:member"];
      console.log(data);
      

    }
  );
  }

  
  ngOnInit() {
    console.log(this.visible);
    
   this.getComptes();
    
    this.credentials = this.fb.group({
      montant: ['', [Validators.required, Validators.min(1)]],
      comptes: ['', [Validators.required]],
    });
  }

  async Verser(){

    const alert = await this.alertCtrl.create({
      cssClass: 'my-custom-class',
      header: 'Confirm!',
      message: `Voulez vous déposer:<br> ${this.credentials.value.montant} !`,
      buttons: [
        {
          text: 'Annuler',
          role: 'cancel',
          cssClass: 'secondary',
          handler: (blah) => {
          
          }
        }, {
          text: 'Confirmer',
          handler: async () => {
            const loading = await this.loadingCtrl.create({
                cssClass: 'my-custom-class',
                message: 'Please wait...',
              });
            
              await loading.present();
            this.authService.Verser(this.credentials.value).subscribe(
              async (data) =>{
                this.credentials.reset();
                await loading.dismiss();
                const toast = await this.toastCtrl.create({
                  message: 'Dépot effecutuer avec succé.',
                  position: 'middle',
                  duration: 2000
                });
                toast.present();
                
              },
              async (err) =>{
                await loading.dismiss();
                const toast = await this.toastCtrl.create({
                  message: 'Erreur lors du depot.',
                  position: 'middle',
                  duration: 2000
                });
                toast.present();
              }
            );
          }
        }
      ]
    });

    await alert.present();
   
      
    
  }

}
