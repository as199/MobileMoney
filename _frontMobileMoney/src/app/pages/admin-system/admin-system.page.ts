import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AlertController } from '@ionic/angular';
import { AuthenticationService } from 'src/app/services/authentication.service';
import { PagesADMINSYS } from '../../utils/PageAdminSysUrl';

@Component({
  selector: 'app-admin-system',
  templateUrl: './admin-system.page.html',
  styleUrls: ['./admin-system.page.scss'],
})
export class AdminSystemPage implements OnInit {
  pages: any = [];
  montant= 0;
  today : Date = new Date();
  adminSystem = false;
  cacher = true;
  constructor(private router: Router ,private authService: AuthenticationService,private alertController: AlertController) {
    this.pages = PagesADMINSYS;
   
    
  }
  

  ngOnInit() {

    this.GetSolde();
    this.authService.refresh$.subscribe(
      ()=> {
        this.GetSolde();
      });

      this.authService.getMyRole().then(res =>{
        console.log("le role est :",res);
        if (res === "ROLE_AdminSysteme"){
          this.adminSystem = true;
        }
      })
    
  }

  afficher(){
    this.cacher= !this.cacher;
    
  }
  GetSolde(){this.authService.getSolde().subscribe(
      (data) =>{
        this.montant = data.data;
    });
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


