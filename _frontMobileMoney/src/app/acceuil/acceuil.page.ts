import {Component, OnInit, ViewChild} from '@angular/core';
import {Router} from '@angular/router';
import {INTRO_KEY} from '../guards/redirect.guard';
import {IonSlides} from '@ionic/angular';
import {Plugins} from '@capacitor/core';
const { Storage } = Plugins;

@Component({
  selector: 'app-acceuil',
  templateUrl: './acceuil.page.html',
  styleUrls: ['./acceuil.page.scss'],
})
export class AcceuilPage implements OnInit {
@ViewChild(IonSlides) slides: IonSlides;
  constructor(private router: Router) { }

  ngOnInit() {
  }

  next() {
    this.slides.slideNext();
  }
  async start() {
    await Storage.set({key: INTRO_KEY, value: 'true'});
    await this.router.navigateByUrl('/login', {replaceUrl: true});
  }
}
