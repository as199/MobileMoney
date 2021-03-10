import { NgModule } from '@angular/core';
import { PreloadAllModules, RouterModule, Routes } from '@angular/router';
import {RedirectGuard} from './guards/redirect.guard';
import {AutoLoginGuard} from './guards/auto-login.guard';
import {AuthGuard} from './guards/auth.guard';

const routes: Routes = [
  {
    path: '',
    redirectTo: 'accueil',
    pathMatch: 'full',
  },
  {
    path: 'accueil',
    loadChildren: () =>
      import('./acceuil/acceuil.module').then((m) => m.AcceuilPageModule), canLoad: [RedirectGuard]
  },
  {
    path: 'login',
    loadChildren: () =>
      import('./pages/login/login.module').then((m) => m.LoginPageModule),
    canLoad: [ AutoLoginGuard]
  },
  {
    path: 'admin-system',
    loadChildren: () =>
      import('./pages/admin-system/admin-system.module').then(
        (m) => m.AdminSystemPageModule
      ), canLoad: [AuthGuard]
  },
  {
    path: 'tabs-admin',
    loadChildren: () =>
      import('./pages/tabs-admin/tabs-admin.module').then(
        (m) => m.TabsAdminPageModule
      ), canLoad: [AuthGuard]
  },
  {
    path: 'transaction',
    loadChildren: () =>
      import('./pages/transaction/transaction.module').then(
        (m) => m.TransactionPageModule
      ), canLoad: [AuthGuard]
  },
  {
    path: 'calculator',
    loadChildren: () =>
      import('./pages/calculator/calculator.module').then(
        (m) => m.CalculatorPageModule
      ), canLoad: [AuthGuard]
  },
  {
    path: 'commission',
    loadChildren: () =>
      import('./pages/commission/commission.module').then(
        (m) => m.CommissionPageModule
      ), canLoad: [AuthGuard]
  },
  {
    path: 'acceuil',
    loadChildren: () => import('./acceuil/acceuil.module').then( m => m.AcceuilPageModule)
  },
  {
    path: 'tampon',
    loadChildren: () => import('./tampon/tampon.module').then( m => m.TamponPageModule), canLoad: [AuthGuard]
  },
  {
    path: 'depot',
    loadChildren: () => import('./pages/depot/depot.module').then( m => m.DepotPageModule)
  },
  {
    path: 'retrait',
    loadChildren: () => import('./pages/retrait/retrait.module').then( m => m.RetraitPageModule)
  }
];

@NgModule({
  imports: [
    RouterModule.forRoot(routes, { preloadingStrategy: PreloadAllModules }),
  ],
  exports: [RouterModule],
})
export class AppRoutingModule {}
