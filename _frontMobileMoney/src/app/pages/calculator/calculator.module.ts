import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import {FormsModule, ReactiveFormsModule} from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { CalculatorPageRoutingModule } from './calculator-routing.module';

import { CalculatorPage } from './calculator.page';

@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        IonicModule,
        CalculatorPageRoutingModule,
        ReactiveFormsModule
    ],
  declarations: [CalculatorPage]
})
export class CalculatorPageModule {}
