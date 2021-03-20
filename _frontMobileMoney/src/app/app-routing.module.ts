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
  },
  {
    path: 'dashboard',
    loadChildren: () => import('./configs/dashboard/dashboard.module').then( m => m.DashboardPageModule)
  },
  {
    path: 'utilisateur',
    loadChildren: () => import('./configs/utilisateur/utilisateur.module').then( m => m.UtilisateurPageModule)
  },
  {
    path: 'agence',
    loadChildren: () => import('./configs/agence/agence.module').then( m => m.AgencePageModule)
  },
  {
    path: 'versement',
    loadChildren: () => import('./configs/versement/versement.module').then( m => m.VersementPageModule)
  },
  {
    path: 'annuler-depot',
    loadChildren: () => import('./pages/annuler-depot/annuler-depot.module').then( m => m.AnnulerDepotPageModule)
  },
  {
    path: 'profil',
    loadChildren: () => import('./pages/profil/profil.module').then( m => m.ProfilPageModule)
  },
  {
    path: 'reset-password',
    loadChildren: () => import('./pages/reset-password/reset-password.module').then( m => m.ResetPasswordPageModule)
  }
];

@NgModule({
  imports: [
    RouterModule.forRoot(routes, { preloadingStrategy: PreloadAllModules }),
  ],
  exports: [RouterModule],
})
export class AppRoutingModule {}
