import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { AlertController } from '@ionic/angular';
import { AuthenticationService } from 'src/app/services/authentication.service';

@Component({
  selector: 'app-transaction',
  templateUrl: './transaction.page.html',
  styleUrls: ['./transaction.page.scss'],
})
export class TransactionPage implements OnInit {
  page = 0;
  data = [];
  bulkEdit = false;
  sortDirectionuser = 0;
  sortDirectionmontant = 0;
  sortKey = null;
  useragence: boolean = false;
  adminsys: boolean= false;
  avatar: string;
  constructor(private authService: AuthenticationService, private http: HttpClient, private alertCtrl: AlertController) {
    this.loadData();
    this.authService.getMyRole().then((role) => {

      if(role === 'ROLE_AdminSysteme' || role === 'ROLE_AdminAgence'){
        this.adminsys = true;
      }else if(role === "ROLE_UserAgence" ){
        this.useragence = true;
      }
    });
  }

  ngOnInit() {


  }


  async infos(row){
    const alert = await this.alertCtrl.create({
      cssClass: 'my-custom-class',
      subHeader: `Transaction  N° ${row.id}`,
      message:  ` <ion-card>
      <ion-card-content>
      <strong>${row.nom} </strong>  <br>
          <strong>Type : </strong> ${row.type} <br>
        <strong>Montant : </strog>${row.montant} <br>
        <strong>Frais : </strog>${row.ttc} <br>
        <strong>Date : </strong>${row.date} <br>
      </ion-card-content>
    </ion-card>`,
      buttons: ['OK']
    });

    await alert.present();

  }

  loadData(){
    this.authService.MesTransactions().subscribe(
      (res) => {
        console.log(res);
        this.data = res.data;
      }, error => {
        console.log(error);
      });
  }

  sortBy(key) {
      this.sortDirectionuser ++;

  }


}
