import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthenticationService } from 'src/app/services/authentication.service';
import { PagesADMINSYS } from '../../utils/PageAdminSysUrl';

@Component({
  selector: 'app-admin-system',
  templateUrl: './admin-system.page.html',
  styleUrls: ['./admin-system.page.scss'],
})
export class AdminSystemPage implements OnInit {
  pages: any = [];
  constructor(private router: Router, private authService: AuthenticationService) {
    this.pages = PagesADMINSYS;
  }

  ngOnInit() {
  }

  onItemClick(url: string) {
    console.log(url);
    this.router.navigate([url]);
  }

  async logout() {
    await this.authService.logout();
    await this.router.navigateByUrl('/', { replaceUrl: true})
  }


}
