<?php
namespace RolBot\Middleware;

use \RedBeanPHP\R as R;
use RolBot\Repository\LoginRepository;

class SecurityMiddleware
{
	public function configure(&$app)
	{
		$loggedInMiddleware = function ($request, $response, $next) {
			$route = $request->getAttribute('route');
			// route does not exist
			// ignore authentication
			if ($route === null) {
				$response = $next($request, $response);
				return $response;
			}
			$routeName = $route->getName();
			$groups = $route->getGroups();
			$methods = $route->getMethods();
			$arguments = $route->getArguments();

			$publicRoutesNameArray = array(
				'loginpage',
				'loginapi',
				'logoutapi',
				'root',
				'telegramHook',
				'sethook',
				'unsethook'
			);

			$is_public = in_array($routeName, $publicRoutesNameArray);
			$session_active = LoginRepository::isLoggedIn();

			if (!$session_active && !$is_public) {
				$home_url = $this->get('router')->pathFor('loginpage');
				$response = $response->withRedirect($home_url);
			} else {
				$response = $next($request, $response);
			}
			return $response;
		};
		$app->add($loggedInMiddleware);
	}

}
