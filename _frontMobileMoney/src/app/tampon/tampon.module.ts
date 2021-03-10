import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { TamponPageRoutingModule } from './tampon-routing.module';

import { TamponPage } from './tampon.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    TamponPageRoutingModule
  ],
  declarations: [TamponPage]
})
export class TamponPageModule {}
