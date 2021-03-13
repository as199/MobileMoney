import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { TabsAdminPage } from './tabs-admin.page';

const routes: Routes = [
  {
    path: '',
    component: TabsAdminPage,
    children: [
      {
        path: 'admin-system',
        loadChildren: () =>
          import('../admin-system/admin-system.module').then(
            (m) => m.AdminSystemPageModule
          ),
      },
      {
        path: 'calculator',
        loadChildren: () =>
          import('../calculator/calculator.module').then(
            (m) => m.CalculatorPageModule
          ),
      },
      {
        path: 'transaction',
        loadChildren: () =>
          import('../transaction/transaction.module').then(
            (m) => m.TransactionPageModule
          ),
      },
      {
        path: 'commission',
        loadChildren: () =>
          import('../commission/commission.module').then(
            (m) => m.CommissionPageModule
          ),
      },
      {
        path: 'depot',
        loadChildren: () => import('../depot/depot.module').then( m => m.DepotPageModule)
      },
      {
        path: 'retrait',
        loadChildren: () => import('../retrait/retrait.module').then( m => m.RetraitPageModule)
      },
      {
        path: 'dashboard',
        loadChildren: () => import('../../configs/dashboard/dashboard.module').then( m => m.DashboardPageModule)
      }
    ],
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class TabsAdminPageRoutingModule {}
