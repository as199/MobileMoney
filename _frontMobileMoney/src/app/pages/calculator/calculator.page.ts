import { Component, OnInit } from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {AuthenticationService} from '../../services/authentication.service';
import {AlertController, LoadingController} from '@ionic/angular';

@Component({
  selector: 'app-calculator',
  templateUrl: './calculator.page.html',
  styleUrls: ['./calculator.page.scss'],
})
export class CalculatorPage implements OnInit {
  credentials: FormGroup;
  avatar: string;
  constructor(private fb: FormBuilder,
              private alertCtrl: AlertController,
              private loadingCtrl: LoadingController,
              private authService: AuthenticationService) { }

  ngOnInit() {
    this.credentials = this.fb.group({
      montant: ['', [Validators.required, Validators.min(1)]]});


  }

  async calculer() {
    const loading = await this.loadingCtrl.create();
    await loading.present();

    this.authService.calculator(this.credentials.value).subscribe(
      async(res) =>{
        await loading.dismiss();
        const alert = await this.alertCtrl.create({
          header: `Pour la transaction de ${this.credentials.value.montant}, le frais est égal à:`,
          cssClass: 'my-custom-class',
          message: `<span class="montant">${res.data} CFA</span>`,
          buttons: ['OK']
        });
        await alert.present();
        console.log(res);
      }
    )
  }
  get montant() {
    return this.credentials.get('montant');
  }
}
