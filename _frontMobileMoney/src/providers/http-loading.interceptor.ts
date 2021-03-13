import {HttpEvent, HttpHandler, HttpInterceptor, HttpRequest } from '@angular/common/http';
import { Injectable } from "@angular/core";
import { Observable, of} from 'rxjs';
import { AuthenticationService } from 'src/app/services/authentication.service';
import {catchError} from 'rxjs/operators';

@Injectable()
export class HttpRequestInterceptor implements HttpInterceptor {
  constructor(private authService: AuthenticationService) {

  }

  intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    if (!this.authService.loggedIn()) {
      return next.handle(req);
    } else {
      req = req.clone({
        setHeaders: {
          Authorization: `Bearer ${this.authService.getToken()}`
        }
      });
      return next.handle(req).pipe(
        catchError((err, caught) => {
          if (err.status === 401) {
            // this.authService.logout();
            return of(err);
          }
          throw err;
        })
      );
    }
  }
}
