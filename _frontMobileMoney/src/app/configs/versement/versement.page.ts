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
visible: boolean = true;
  comptes: any;
  credentials: FormGroup;
  depots: any;
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
  previous(){
    this.visible =true;
  }
  next(){
    this.visible =false;
  }
  
  ngOnInit(){
   this.getComptes();
   this.chargerDepot();
    
    this.credentials = this.fb.group({
      montant: ['', [Validators.required, Validators.min(1)]],
      comptes: ['', [Validators.required]],
    });
  }

  chargerDepot(){
    this.authService.GetDepot().subscribe(
      (data) =>{
        this.depots = data.data;
        console.log(data);
        
      }
    )
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

  async delete(id){
    console.log(id);
    
    const alert = await this.alertCtrl.create({
      cssClass: 'my-custom-class',
      header: 'Confirmation',
      message: `Etes vous sur de vouloir supprimer cette transactions ?`,
      buttons: [
        {
          text: 'Cancel',
          role: 'cancel',
          cssClass: 'secondary',
          handler: () => {
          }
        }, {
          text: 'Confirmer',
          handler: async () => {
            const loading = await this.loadingCtrl.create();
             await loading.present();
            this.authService.deleteDepot(id).subscribe(
              async (data) => {
                console.log(data);
                
                await loading.dismiss();
                this.credentials.reset();
                const alert = await this.alertCtrl.create({
                  header: 'Succée',
                  message: 'Dépot  supprimer avec succée',
                  buttons: ['OK']
                });
              await alert.present();
            }, async(error) => {
              console.log(error);
              
               await loading.dismiss();
              const alert = await this.alertCtrl.create({
                header: 'Failed',
                cssClass: "my-custom-class",
                message: 'Erreur lors de la suppression du Dépot',
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
