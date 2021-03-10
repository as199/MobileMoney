import { Injectable } from '@angular/core';

import { Plugins} from '@capacitor/core';
import {BehaviorSubject, from, Observable} from 'rxjs';
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
  constructor(private http: HttpClient, private router: Router) {
    this.loadToken();
  }
  async loadToken(){
    const token = await Storage.get({key: TOKEN_KEY});
    if (token && token.value){
      this.myToken = token.value;
      this.decoded = jwt_decode(this.myToken);
       await this.SaveInfos();
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
      return from(Storage.set({key: TOKEN_KEY, value: token}));
    }),
    tap(_=> {
      this.isAuthenticated.next(true);
    })
  )
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
    return this.http.post(`${this.url}/transactions`,data);
  }
  findTransactionByCode(code: string): Observable<any>{
    return this.http.post(`${this.url}/transactions/find`,code);
  }

  getToken(){
    return this.myToken;
  }

   async SaveInfos(){
     await Storage.set({key: 'role', value: this.decoded['roles']});
     await Storage.set({key: 'telephone', value: this.decoded['telephone']});

  }

  getRole(){
    return this.myRole;
  }

  RedirectMe(role: string){
    if(role === "ROLE_AdminSysteme"){
      this.router.navigateByUrl('/tabs-admin/admin-system', { replaceUrl: true});
    }else if(role === "ROLE_AdminAgence" ){
      this.router.navigateByUrl('/transaction', { replaceUrl: true});
    }else if(role === "Caissier" ){

    }
  }
}
