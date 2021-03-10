import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import {AuthenticationService} from '../services/authentication.service';

@Component({
  selector: 'app-tampon',
  templateUrl: './tampon.page.html',
  styleUrls: ['./tampon.page.scss'],
})
export class TamponPage implements OnInit {

  constructor(private authService: AuthenticationService, private router: Router) {
    this.router.navigateByUrl('/tabs-admin/admin-system', { replaceUrl: true});
  }

  ngOnInit() {
    // let role = this.authService.getRole();
    // this.authService.RedirectMe(role);
    // console.log(role);
  }

}
