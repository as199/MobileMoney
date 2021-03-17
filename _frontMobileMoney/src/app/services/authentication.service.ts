import { Injectable } from '@angular/core';

import { Plugins} from '@capacitor/core';
import {BehaviorSubject, from, Observable, Subject} from 'rxjs';
import {HttpClient} from '@angular/common/http';
import {map, switchMap, tap} from 'rxjs/operators';
import jwt_decode from "jwt-decode";
import { Router } from '@angular/router';
import { Transaction } from 'src/modeles/Transaction';
const { Storage } = Plugins;
const TOKEN_KEY = 'my-token';


@Injectable({
  providedIn: 'root'
})
export class AuthenticationService {
isAuthenticated: BehaviorSubject<boolean> = new BehaviorSubject<boolean>(null);
token = '';
myToken='';
myRole='';
decoded: any;
 url = 'http://127.0.0.1:8000/api';
 private _refresh$ = new Subject();
  role: string;
  constructor(private http: HttpClient, private router: Router) {
    this.loadToken();
  }

  get refresh$(): any{
    return this._refresh$;
  }
  async loadToken(){
    const token = await Storage.get({key: TOKEN_KEY});
    if (token && token.value){
      this.isAuthenticated.next(true);
    }else{
      this.isAuthenticated.next(false);
    }
  }

  loggedIn(){
    return !! Storage.get({key: TOKEN_KEY});
  }

  login(credentials: {username,password}): Observable<any>{
  return this.http.post('http://127.0.0.1:8000/api/login_check', credentials).pipe(
    map((data: any) => data.token),
    switchMap(token =>{
      // return from(Storage.set({key: TOKEN_KEY, value: token}));
      return from(this.InfosSave(token));
    }),
    tap(_=> {
      this.isAuthenticated.next(true);
    })
  )
  }

  async InfosSave(token){
    this.myToken = token;
    let from = jwt_decode(token);
    this.myRole = from['roles'][0];
    await Storage.set({key: TOKEN_KEY, value: token});
    await Storage.set({key: 'role', value: from['roles']});
    await Storage.set({key: 'telephone', value: from['telephone']});
    await Storage.set({key: 'avatar', value: from['avatar']});

 }
 getToken(){
  return this.myToken;
 }

getRole(){
  return this.myRole;
}

async getAvatar() {
  const avatar = await Storage.get({key: 'avatar'});
  if (avatar && avatar.value) {
    this.role = avatar.value;

    return this.role;
  }
}
async getMyRole(){
  const token = await Storage.get({key: 'role'});
  if (token && token.value){
     this.role = token.value;

   return this.role;
  }
}

RedirectMe(role: string){
  if(role){
    this.router.navigateByUrl('/tabs-admin/admin-system', { replaceUrl: true});
  }else {
    this.router.navigateByUrl('/login', { replaceUrl: true});
  }
}

  logout(): Promise<void>{
    this.isAuthenticated.next(false);
    Storage.remove({key: 'role' });
    Storage.remove({key: 'telephone' });
    Storage.remove({key: 'intro-seen' });
    return Storage.remove({key: TOKEN_KEY});
  }

  calculator(montant: number): Observable<any>{
    return this.http.post(`${this.url}/calculer`, montant);
  }

  Transaction(data: Transaction): Observable<any>{
    return this.http.post(`${this.url}/transactions`,data).pipe(
      tap(() => {
        this._refresh$.next();
      }));
  }

  annulerTransaction(numero:any): Observable<any>{
    return this.http.post(`${this.url}/transactions/delete`, numero).pipe(
      tap(() => {
        this._refresh$.next();
      }));;
  }

  findTransactionByCode(code: string): Observable<any>{
    return this.http.post(`${this.url}/transactions/find`,code);
  }

  MesTransactions(): Observable<any>{
    return this.http.get(`${this.url}/transactions/user`);
  }

  getSolde(data: string= "sal"): Observable<any>{
    return this.http.post(`${this.url}/transactions/solde`,data);
  }

  AddAgence(agence: any): Observable<any>{
    return this.http.post(`${this.url}/agences`,agence);
  }

  Verser(data: any): Observable<any>{
    return this.http.post(`${this.url}/depots`,data);
    }

  DeleteAgence(id: number): Observable<any>{
    return this.http.delete(`${this.url}/agences/${id}`).pipe(
      tap(() => {
        this._refresh$.next();
      }));
  }

  GetAgence(): Observable<any>{
    return this.http.get<any>(`${this.url}/agences`);
  }


  GetCompte(): Observable<any>{
    return this.http.get<any>(`${this.url}/adminSys/comptes`);
  }

  AddUser(user: any): Observable<any>{
    return this.http.post(`${this.url}/adminSys/utilisateurs`,user);
  }
  deleteUser(id: number): Observable<any>{
    return this.http.delete(`${this.url}/adminSys/utilisateurs/${id}`).pipe(
      tap(() => {
        this._refresh$.next();
      }));
  }

  GetUserNotAgence(): Observable<any>{
    return this.http.get<any>(`${this.url}/adminSys/utilisateurs/users`);
  }
  GetAllUsers(): Observable<any>{
    return this.http.get<any>(`${this.url}/adminSys/utilisateurs`);
  }

  GetDepot(): Observable<any>{
    return this.http.get<any>(`${this.url}/depots`);
  }


  deleteDepot(id: number): Observable<any>{
    return this.http.delete<any>(`${this.url}/depots/${id}`);
  }

}
