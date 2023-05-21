<?php

class Core {
    private $currentController = 'Items';
    private $currentMethod = 'get';
    private $params = [];

    private $controllerMethodsMap = [
          'GET' => [
              'items/:id' => [Items::class, 'get', 'secure']
          ],
          'POST' => [
              'items' => [Items::class, 'create', 'secure'],

              'users/register' => [Auth::class, 'register', 'public'],
              'users/auth' => [Auth::class, 'authorization', 'public'],
          ],
          'PUT' => [
              'items/:id' => [Items::class, 'update', 'secure']
          ],
          'PATCH' => [
              'items/:id' => [Items::class, 'updatePartial', 'secure']
          ],
          'DELETE' => [
              'items/:id' => [Items::class, 'delete', 'secure']
          ]
    ];



    public function __construct()
    {
        header("Content-Type:application/json");

        $url = $this->getUrl();
        $controller = $this->getController($url);
        if(!empty($controller)) {
            if(file_exists('../app/controllers/' . $controller[0]) . '.php') {
                require_once '../app/controllers/' . $controller[0] . '.php';
                $this->currentController = new $controller[0];
                if (method_exists($this->currentController, $controller[1])) {
                    $this->currentMethod = $controller[1];
                    $this->params = $controller[3];
                    // execute token verify
                    if($controller[2] != 'public') {
                        TokenVerify::checkAuth();
                    }
                    call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
                }
                else {
                    throw new CustomException('Route not found', 404);
                }
            } else {
                throw new CustomException('Route not found', 404);
            }
        } else {
            throw new CustomException('Route not found', 404);
        }
    }


    private function getUrl() {
        if(isset($_SERVER['REQUEST_URI'])) {
            $url = trim($_SERVER['REQUEST_URI'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/',$url);
            return $url;
        }
    }

    private function getController($url) {
        foreach ($this->controllerMethodsMap[$_SERVER['REQUEST_METHOD']] as $route => $controller) {
            $parts = explode('/',$route);
            $count_parts = count($parts);
            if(count($url) !=  $count_parts)
                continue;
            $params = [];
            for($i=0; $i <= $count_parts-1; $i++) {
                if(mb_strlen($parts[$i]) > 0 ) {
                    if($parts[$i][0] == ':') {
                        // param value
                        if(mb_strlen($url[$i]) == 0)
                            continue 2;
                        $key = ltrim($parts[$i],':');
                        $params[$key] = $url[$i];
                    } else {
                        if($url[$i] != $parts[$i])
                            continue 2;
                    }
                }
                if ($i == $count_parts - 1) {
                    $controller[] = $params;
                    return $controller;
                }
            }

        }
        return [];
    }

}