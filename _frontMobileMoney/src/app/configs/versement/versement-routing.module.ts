import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { VersementPage } from './versement.page';

const routes: Routes = [
  {
    path: '',
    component: VersementPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class VersementPageRoutingModule {}
