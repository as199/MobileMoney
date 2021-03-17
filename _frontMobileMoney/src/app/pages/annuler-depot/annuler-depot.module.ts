import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import {FormsModule, ReactiveFormsModule} from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { AnnulerDepotPageRoutingModule } from './annuler-depot-routing.module';

import { AnnulerDepotPage } from './annuler-depot.page';

@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        IonicModule,
        AnnulerDepotPageRoutingModule,
        ReactiveFormsModule
    ],
  declarations: [AnnulerDepotPage]
})
export class AnnulerDepotPageModule {}
