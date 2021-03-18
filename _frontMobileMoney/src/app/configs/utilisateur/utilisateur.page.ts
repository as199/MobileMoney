import { LoadingController, AlertController, ToastController } from '@ionic/angular';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AuthenticationService } from 'src/app/services/authentication.service';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { Plugins, CameraResultType, CameraSource } from '@capacitor/core';

@Component({
  selector: 'app-utilisateur',
  templateUrl: './utilisateur.page.html',
  styleUrls: ['./utilisateur.page.scss'],
})
export class UtilisateurPage implements OnInit {
  visible: boolean = true;
  credentials: FormGroup;
  agences: any;
  image: SafeResourceUrl;
  myimg: any;
  users: any;
  avatar: string;
  constructor(
    private authService: AuthenticationService,
    private fb: FormBuilder,
    private loadingCtrl: LoadingController,
    private alertCtrl: AlertController,
    private toastCtrl: ToastController,
    private domsanitizer: DomSanitizer
  ) {

   }

  ngOnInit() {
    this.chargerAgence();
    this.chargerUser();
     this.authService.refresh$.subscribe(
      ()=> {
        this.chargerUser();
      });

    this.credentials = this.fb.group({
      prenom: ['Ouly', [Validators.required, Validators.minLength(2)]],
      nom: ['fall', [Validators.required, Validators.minLength(2)]],
      telephone: ['774306947', [Validators.required, Validators.minLength(8)]],
      email: ['fall4@gmail.com', [Validators.required, Validators.email]],
      genre: ['femme', [Validators.required, Validators.minLength(2)]],
      type: ['', [Validators.required]],
      adresse: ['dakar', [Validators.required]],
      agences: ['', []]
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
  chargerAgence(){
    this.authService.GetAgence().subscribe((data) => {
      this.agences = data.data;
    });
  }

  chargerUser(){
    this.authService.GetAllUsers().subscribe((data) => {
      console.log(data);
      this.users = data.data;
    });
  }

  async Ajouter(){
    const loading = await this.loadingCtrl.create({
      message: 'Please wait...'
    });
    await loading.present();

    let formData = new FormData();
    formData.append('prenom', this.credentials.value.prenom);
    formData.append('nom', this.credentials.value.nom);
    formData.append('agences', this.credentials.value.agences);
    formData.append('adresse', this.credentials.value.adresse);
    formData.append('genre', this.credentials.value.genre);
    formData.append('telephone', this.credentials.value.telephone);
    formData.append('email', this.credentials.value.email);
    formData.append('type', this.credentials.value.type);
    formData.append('password', 'pass123');

    this.authService.AddUser(formData).subscribe(async (data) => {
      this.credentials.reset();
      await loading.dismiss();
      const alert = await this.alertCtrl.create({
        header: 'Succée',
        message: 'Utilisateur ajouter avec succée',
        buttons: ['OK']
      });
    await alert.present();

    },async err => {
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
  //#region parti des getters
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
