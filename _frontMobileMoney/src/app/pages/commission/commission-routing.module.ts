import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { CommissionPage } from './commission.page';

const routes: Routes = [
  {
    path: '',
    component: CommissionPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class CommissionPageRoutingModule {}
