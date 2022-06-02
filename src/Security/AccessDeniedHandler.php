<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;


/**
 * Si l'utilisateur obtient un ACCESS DENIED, on le remet à la page précédente
 */
class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {

        return new Response('<html><head></head><body>
                         
        <div class="face"></div>
          <div class="head"></div>
          <div class="body"></div>
          <div class="left-arm"></div>
          <div class="right-arm"></div>
          <div class="right-eye"></div>
          <div class="left-eye"></div>
        
        <style>
        
            body {
              background-color: lightblue;
            }
        
           .head {
              width: 100px;
              height: 75px;
              position: absolute;
              top: 0;
              left: 0;
              right: 0;
              bottom: 150px;
              background: #fff;
              border-top-left-radius: 60px;
              border-top-right-radius: 60px;
              border-bottom-left-radius: 10px;
              border-bottom-right-radius: 10px;
              margin: auto;
              z-index: 1;
            }
            
            .face {
              width: 65px;
              height: 40px;
              position: absolute;
              top: 0;
              left: 0;
              right: 0;
              bottom: 150px;
              background: #000;
              border-top-left-radius: 20px;
              border-top-right-radius: 20px;
              border-bottom-left-radius: 10px;
              border-bottom-right-radius: 10px;
              margin: auto;
              z-index: 2;
            }
            
            .right-eye {
              position: absolute;
              top: 0;
              left: 0;
              right: 25px;
              bottom: 150px;
              height: 10px;
              width: 10px;
              border-radius: 50%;
              background: blue;
              z-index: 3;
              margin: auto;
            }
            
            .left-eye {
              position: absolute;
              top: 0;
              left: 25px;
              right: 0;
              bottom: 150px;
              height: 10px;
              width: 10px;
              border-radius: 50%;
              background: blue;
              z-index: 3;
              margin: auto;
            }
            
            .body {
              width: 115px;
              height: 120px;
              position: absolute;
              top: 60px;
              left: 0;
              right: 0;
              bottom: 0;
              background: #fff;
              border-top-left-radius: 20px;
              border-top-right-radius: 20px;
              border-bottom-left-radius: 100px;
              border-bottom-right-radius: 100px;
              margin: auto;
              z-index: 1;
            }
            
            .left-arm {
              width: 100px;
              height: 30px;
              position: absolute;
              top: 0;
              left: 0;
              right: 230px;
              bottom: 25px;
              background: #fff;
              border-top-left-radius: 10px;
              border-top-right-radius: 10px;
              border-bottom-left-radius: 50px;
              border-bottom-right-radius: 10px;
              margin: auto;
              animation: hand 2s linear infinite;
            }
            
            .right-arm {
              width: 100px;
              height: 30px;
              position: absolute;
              top: 50px;
              left: 165px;
              right: 0;
              bottom: 0;
              background: #fff;
              border-top-left-radius: 10px;
              border-top-right-radius: 10px;
              border-bottom-left-radius: 10px;
              border-bottom-right-radius: 50px;
              margin: auto;
              transform: rotatez(90deg);
            }
            
            @keyframes hand {
              0% {
                transform: translatey(-40px) rotatez(45deg);
              }
              50% {
                transform: translatey(0px) rotatez(-0deg);
              }
              100% {
                transform: translatey(-40px) rotatez(45deg);
              }
            }

        </style>
 
 </body></html>', 402);

    }
}