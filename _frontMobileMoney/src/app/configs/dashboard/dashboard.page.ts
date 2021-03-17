import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AlertController } from '@ionic/angular';
import { AuthenticationService } from 'src/app/services/authentication.service';
import { ConfigPage } from 'src/app/utils/PageAdminSysUrl';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.page.html',
  styleUrls: ['./dashboard.page.scss'],
})

export class DashboardPage implements OnInit {
  visible = false;
  caissier = false;
  pages: any = [];
  constructor(private router: Router, private authService: AuthenticationService,private alertController: AlertController) {
    this.pages = ConfigPage;
    this.authService.getMyRole().then((role) => {
      if(role === 'ROLE_AdminSysteme'){
          this.visible = true;
         }else if(role === "ROLE_Caissier" ){
        this.caissier = true;
      }
    });
   }

  ngOnInit() {
  }

  onItemClick(url: string) {
    this.router.navigate([url]);
  }

}
