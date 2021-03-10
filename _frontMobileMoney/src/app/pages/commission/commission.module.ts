import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { CommissionPageRoutingModule } from './commission-routing.module';

import { CommissionPage } from './commission.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    CommissionPageRoutingModule
  ],
  declarations: [CommissionPage]
})
export class CommissionPageModule {}
