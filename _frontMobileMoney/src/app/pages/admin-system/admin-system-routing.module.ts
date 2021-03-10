import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { AdminSystemPage } from './admin-system.page';

const routes: Routes = [
  {
    path: '',
    component: AdminSystemPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class AdminSystemPageRoutingModule {}
