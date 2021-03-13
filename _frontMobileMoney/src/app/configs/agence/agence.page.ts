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
  agences: any;

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

    this.credentials = this.fb.group({
      nomAgence: ['', [Validators.required, Validators.min(1)]],
      adresse: ['', [Validators.required]],
      userAgence: [, []]
    });
  }

  chargerUser(){
    this.authService.GetUserNotAgence().subscribe((data) => {
      console.log(data);
      
      this.users = data.data;
    },err => {
      console.log(err);
      
    });
  }

  chargerAgence(){
    this.authService.GetAgence().subscribe((data) => {
      console.log(data);
      
      this.agences = data.data;
    },err => {
      console.log(err);
      
    });
  }

  previous(){
    this.visible =true;
  }
  next(){
    this.visible =false;
  }

  async Ajouter(){
    const loading = await this.loadingCtrl.create({
      message: 'Please wait...'
    });
    await loading.present();
    this.authService.AddAgence(this.credentials.value).subscribe(async (data) => {
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

}
