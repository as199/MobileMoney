import { Component, OnInit } from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {AlertController, LoadingController} from '@ionic/angular';
import {SafeResourceUrl} from '@angular/platform-browser';
import {CameraResultType, CameraSource, Plugins} from '@capacitor/core';
import {AuthenticationService} from '../../services/authentication.service';
import {ConfirmedValidator} from '../confirmed.validator';

@Component({
  selector: 'app-profil',
  templateUrl: './profil.page.html',
  styleUrls: ['./profil.page.scss'],
})
export class ProfilPage implements OnInit {
  visible = true;
  credentials: FormGroup;
  image: SafeResourceUrl;
  myimg: any;
  users: any;
  avatar: string;
  id: string;
  infos: any;
  myId: any;
  modifPass = false;
  constructor(
    private authService: AuthenticationService,
    private fb: FormBuilder,
    private loadingCtrl: LoadingController,
    private alertCtrl: AlertController,
  ) {

  }


async chargerMesInfos() {
  const loading = await this.alertCtrl.create({
    message: 'Please wait...'
  });
  await loading.present();
  this.authService.getId().then((data) => {
    this.id = data;
    this.authService.GetOneUserById(+this.id).subscribe(
      async (data) => {
        await loading.dismiss();
        this.infos = data;
        this.myId = this.infos['id'];
        this.image = "data:image/jpeg;base64," + this.infos['avatar'];
      }, error => {
      });
  });
}


  ngOnInit() {
    this.chargerMesInfos();
    // this.authService.refresh$.subscribe(
    //   ()=> {
    //     this.chargerMesInfos();
    //   });
    this.credentials = this.fb.group({
      prenom: ['', [Validators.required, Validators.minLength(2)]],
      nom: ['', [Validators.required, Validators.minLength(2)]],
      telephone: ['', [Validators.required, Validators.minLength(8)]],
      email: ['', [Validators.required, Validators.email]],
      adresse: ['', [Validators.required]],
      password: ['', [Validators.required, Validators.minLength(8)]],
      cpassword: ['', [Validators.required]],
    }, {
      validator: ConfirmedValidator('password', 'cpassword')
    });

  }

  async selectImage(): Promise<any> {
    const  { Camera } = Plugins;
    const result = await Camera.getPhoto({
      quality: 75,
      allowEditing: true,
      source: CameraSource.Photos,
      resultType: CameraResultType.Base64
    });
    this.image ="data:image/jpeg;base64,"+result.base64String;
    this.myimg = result.base64String;
    //this.domsanitizer.bypassSecurityTrustResourceUrl(result && result.base64String);
  }

  previous(){
    this.visible =true;
  }
  next(){
    this.visible =false;
  }
  Afficher() {
    this.modifPass = !this.modifPass;
  }


  async Update(){
    const loading = await this.loadingCtrl.create({
      message: 'Please wait...'
    });
    await loading.present();

    let formData = new FormData();
    formData.append('prenom', this.credentials.value.prenom);
    formData.append('nom', this.credentials.value.nom);
    formData.append('adresse', this.credentials.value.adresse);
    formData.append('telephone', this.credentials.value.telephone);
    formData.append('email', this.credentials.value.email);
    if(this.credentials.value.password !== ""){
      formData.append('password', this.credentials.value.password);
    }


    this.authService.UpdateUser(formData, this.myId).subscribe(async (data) => {
      this.credentials.reset();
      this.infos = data[0];
      await loading.dismiss();
      const alert = await this.alertCtrl.create({
        header: 'Succée',
        message: 'Utilisateur ajouter avec succée',
        buttons: ['OK']
      });
      await alert.present();

    },async (err) => {
      await loading.dismiss();
      const alert = await this.alertCtrl.create({
        header: 'Failed',
        cssClass: "my-custom-class",
        message: 'Erreur d\'ajout  de l\'utilisateur ',
        buttons: ['OK']
      });
      await alert.present();

    });
  }
  async delete(id: any){
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
            console.log('Confirm Cancel');
          }
        }, {
          text: 'Ok',
          handler: async () => {
            const loading = await this.loadingCtrl.create();
            await loading.present();
            this.authService.deleteUser(id).subscribe(
              async (data) => {
                await loading.dismiss();
                this.credentials.reset();
                const alert = await this.alertCtrl.create({
                  header: 'Succée',
                  message: 'Utilisateur  supprimer avec succée',
                  buttons: ['OK']
                });
                await alert.present();
              }, async(error) => {

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

//#region parti des getters
  get password() {
    return this.credentials.get('password');
  }
  get cpassword() {
    return this.credentials.get('cpassword');
  }
  get email() {
    return this.credentials.get('email');
  }
  get prenom() {
    return this.credentials.get('prenom');
  }
  get nom() {
    return this.credentials.get('nom');
  }
  get telephone() {
    return this.credentials.get('telephone');
  }

  get genre() {
    return this.credentials.get('genre');
  }
  get type() {
    return this.credentials.get('type');
  }

  get adresse() {
    return this.credentials.get('adresse');
  }
  //#endregion

}
