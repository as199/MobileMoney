import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { DepotPage } from './depot.page';

const routes: Routes = [
  {
    path: '',
    component: DepotPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class DepotPageRoutingModule {}
