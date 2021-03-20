import { Component, OnInit } from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {AuthenticationService} from '../../services/authentication.service';
import {AlertController, LoadingController} from '@ionic/angular';

@Component({
  selector: 'app-reset-password',
  templateUrl: './reset-password.page.html',
  styleUrls: ['./reset-password.page.scss'],
})
export class ResetPasswordPage implements OnInit {
  credentials: FormGroup;
  constructor( private fb: FormBuilder,
               private authService: AuthenticationService,
               private alertCtrl: AlertController,
               private loadingCtrl: LoadingController) { }

  ngOnInit() {
    this.credentials = this.fb.group({
      telephone: ['774306566', [Validators.required, Validators.minLength(9)]],
      email: ['diana2@gmail.com', [Validators.required, Validators.email]],
    });
  }

  async reset() {
    console.log(this.credentials.value);
    const loading = await this.loadingCtrl.create({
      message: 'Please wait...'
    });
    await loading.present();
    this.authService.ResetUser(this.credentials.value).subscribe(async (data) => {
      console.log(data);
      await loading.dismiss();
      const alert = await this.alertCtrl.create({
        header: 'Success',
        message: `Votre nouveau password est: ${data.data}`,
        buttons: ['OK']
      });
      await alert.present();
    }, async err => {
      console.log(err);
      await loading.dismiss();
      const alert = await this.alertCtrl.create({
        header: 'Success',
        message: `le téléphone ou l'email renseigner ne correspont à aucun compte`,
        buttons: ['OK']
      });
    })
  }

  get telephone() {
    return this.credentials.get('telephone');
  }
  get email() {
    return this.credentials.get('email');
  }
}
