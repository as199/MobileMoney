import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { VersementPageRoutingModule } from './versement-routing.module';

import { VersementPage } from './versement.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    ReactiveFormsModule,
    VersementPageRoutingModule
  ],
  declarations: [VersementPage]
})
export class VersementPageModule {}
