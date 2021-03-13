import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { AgencePageRoutingModule } from './agence-routing.module';

import { AgencePage } from './agence.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    ReactiveFormsModule,
    AgencePageRoutingModule
    
  ],
  declarations: [AgencePage]
})
export class AgencePageModule {}
