import { Component, OnInit } from '@angular/core';
import { Router, RouterEvent } from '@angular/router';
import { pages } from './utils/pagesUrl';
import {AuthenticationService} from './services/authentication.service';
import { AlertController } from '@ionic/angular';
@Component({
  selector: 'app-root',
  templateUrl: 'app.component.html',
  styleUrls: ['app.component.scss'],
})
export class AppComponent implements OnInit {
  pages: any = [];

  public selectedPath = '';
  lien="/dashboard";
  visible: boolean = false;

  constructor(private router: Router, private authService: AuthenticationService,private alertController: AlertController) {
    this.pages = pages;
 
  this.router.events.subscribe((event: RouterEvent) => {
      if (event && event.url) {
        this.selectedPath = event.url;
      }
    });

  }
  ngOnInit(): void {
  
  }

 

  onItemClick(url: string) {
    this.router.navigate([url]);
  }
 

  async logout() {
    const alert = await this.alertController.create({
      cssClass: 'my-custom-class',
      header: 'Confirm!',
      message: 'Voulez-vous vous déconnecter !',
      buttons: [
        {
          text: 'Cancel',
          role: 'cancel',
          cssClass: 'secondary',
          handler: (blah) => {
          }
        }, {
          text: 'Confirmer',
          handler: async () => {
            await this.authService.logout();
            await this.router.navigateByUrl('/', { replaceUrl: true})
          }
        }
      ]
    });
    await alert.present();
  }

}
