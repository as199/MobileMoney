import { Component } from '@angular/core';
import { Router, RouterEvent } from '@angular/router';
import { pages } from './utils/pagesUrl';
import {AuthenticationService} from './services/authentication.service';
@Component({
  selector: 'app-root',
  templateUrl: 'app.component.html',
  styleUrls: ['app.component.scss'],
})
export class AppComponent {
  pages: any = [];

  public selectedPath = '';

  constructor(private router: Router, private authService: AuthenticationService) {
    this.pages = pages;
    this.router.events.subscribe((event: RouterEvent) => {
      if (event && event.url) {
        this.selectedPath = event.url;
      }
    });
  }

  // tslint:disable-next-line:use-lifecycle-interface
  ngOnInit() {}

  onItemClick(url: string) {
    this.router.navigate([url]);
  }

  async logout() {
    await this.authService.logout();
    await this.router.navigateByUrl('/', { replaceUrl: true})
  }
}
