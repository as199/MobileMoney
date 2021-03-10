import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import {AuthenticationService} from '../../services/authentication.service';
import {AlertController, LoadingController} from '@ionic/angular';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';

@Component({
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss'],
})
export class LoginPage implements OnInit {
  credentials: FormGroup;
  constructor(
    private router: Router,
    private fb: FormBuilder,
    private authService: AuthenticationService,
    private alertCtrl: AlertController,
    private loadingCtrl: LoadingController
  ) {}

  ngOnInit() {
    this.credentials = this.fb.group({
      username: ['7741252857', [Validators.required, Validators.minLength(9)]],
      password: ['pass1234', [Validators.required, Validators.minLength(6)]],
    });
  }

  async login() {
    const loading = await this.loadingCtrl.create();
    await loading.present();

    this.authService.login(this.credentials.value).subscribe(
      async(res) =>{
        await loading.dismiss();
        await this.router.navigateByUrl('/tabs-admin/admin-system', { replaceUrl: true});
        await this.authService.SaveInfos();

      }, async(res) =>{
        await loading.dismiss();
        const alert = await this.alertCtrl.create({
          header: 'Login failed',
          message: res.error.error,
          buttons: ['OK']
        });
        await alert.present();
      }
    )
  }
  

  get username() {
    return this.credentials.get('username');
  }
  get password() {
    return this.credentials.get('password');
  }

}