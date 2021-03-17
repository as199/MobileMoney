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
  resultsCount = 10;
  totalPages = 10;
  data = [];
  bulkEdit = false;
  sortDirectionuser = 0;
  sortDirectionmontant = 0;
  sortDirectionfrais = 0;
  sortDirectiontype = 0;
  sortKey = null;
  constructor(private authService: AuthenticationService, private http: HttpClient, private alertCtrl: AlertController) {
    this.loadData();
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
      res => {
        console.log(res);
        this.data = res.data;
        this.sort();
      });
  }

  sortBy(key) {
    this.sortKey = key;
    if(key === "user"){
      this.sortDirectionuser ++;
      this.sort();
    }
  }

  sort() {
    if(this.sortDirectionuser == 1){
      this.data = this.data.sort((a, b)=>{
        console.log('a: ', a);
        const valA = a[this.sortKey];
        const valB = b[this.sortKey];
        return valA.localeCompare(valB);
      });
    }else if(this.sortDirectionuser == 2){

    }else{
      this.sortDirectionuser = 0;
      this.sortKey = null;
    }
  }
}
