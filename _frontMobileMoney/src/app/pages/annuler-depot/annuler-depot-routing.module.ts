import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { AnnulerDepotPage } from './annuler-depot.page';

const routes: Routes = [
  {
    path: '',
    component: AnnulerDepotPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class AnnulerDepotPageRoutingModule {}
