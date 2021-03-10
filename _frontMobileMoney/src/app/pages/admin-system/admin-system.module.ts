import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { AdminSystemPageRoutingModule } from './admin-system-routing.module';

import { AdminSystemPage } from './admin-system.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    AdminSystemPageRoutingModule
  ],
  declarations: [AdminSystemPage]
})
export class AdminSystemPageModule {}
