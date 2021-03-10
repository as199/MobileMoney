import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { TamponPage } from './tampon.page';

const routes: Routes = [
  {
    path: '',
    component: TamponPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class TamponPageRoutingModule {}
