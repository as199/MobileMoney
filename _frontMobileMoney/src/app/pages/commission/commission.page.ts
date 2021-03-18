import { Component, OnInit } from '@angular/core';
import { AlertController } from '@ionic/angular';
import { AuthenticationService } from 'src/app/services/authentication.service';

@Component({
  selector: 'app-commission',
  templateUrl: './commission.page.html',
  styleUrls: ['./commission.page.scss'],
})
export class CommissionPage implements OnInit {

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
  avatar: string;
  constructor(private authService: AuthenticationService) {
    this.loadData();
  }

  ngOnInit() {


  }




  loadData(){
    this.authService.MesTransactions().subscribe(
      res => {
        this.data = res.data;
      });
  }

  sortBy(key) {
    this.sortKey = key;
    if(key === "user"){
      this.sortDirectionuser ++;
    }
  }


}
