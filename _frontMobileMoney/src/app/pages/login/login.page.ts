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
  passwordToggleIcon= "eye";
  showPassword = false;
  constructor(
    private router: Router,
    private fb: FormBuilder,
    private authService: AuthenticationService,
    private alertCtrl: AlertController,
    private loadingCtrl: LoadingController
  ) {}

  ngOnInit() {
    // diana 774306566   ps: pass1234
    // useragence : 7741242857
    //adminAgence: 766540364
    // caissier: 7741222854
    // admin system : 7741252857
  this.credentials = this.fb.group({
      username: ['7741252857', [Validators.required, Validators.minLength(9)]],
      password: ['pass1234', [Validators.required, Validators.minLength(6)]],
  });
  }

  async login() {
    const loading = await this.loadingCtrl.create({
      message: 'Please wait...'
    });
    await loading.present();

    this.authService.login(this.credentials.value).subscribe(
      async(res) =>{
        await loading.dismiss();
        let role = this.authService.getRole();
         this.authService.RedirectMe(role);

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



  togglePassword(): void {
    this.showPassword = !this.showPassword;
    if (this.passwordToggleIcon =="eye"){
      this.passwordToggleIcon = "eye-off";
    }else{
      this.passwordToggleIcon = "eye";
    }
  }

  onItemClick(url: string) {
    this.router.navigate([url]);
  }
}
