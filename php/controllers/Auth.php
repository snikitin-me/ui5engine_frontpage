<?php 

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$c = $app['controllers_factory'];

require_once($config['modulespath'] . '/users/php/models/User.php'); 

$userModel = new Users\Models\User($app);

$c->get('/login', function (Request $req) use ($app, $userModel) {
  return $app['twig']->render('layout.twig', array(
  	'error' => false,
  	'mainblock' => 'elements/login.html'
  ));
});

$c->get('/signup', function (Request $req) use ($app, $userModel) {
  return $app['twig']->render('layout.twig', array(
    'error' => false,
    'mainblock' => 'elements/signup.html'
  ));
});

$c->post('/login', function (Request $req) use ($app, $userModel, $config) {
    $username = $req->request->get('username');
    $password =  $req->request->get('password');
    $errorText = "Неправильный логин или пароль";

    try{

      $user = $userModel->authUser($username, $password);
      
      if ($user) {

          if($user['status'] == '0'){
            throw new \Exception('Учетная запись не активирова');
          }

          $app['monolog']->addInfo(sprintf("User [%s] login", $user['username']));

          $app['session']->set('user', $user);

          return $app->redirect('/modules/launchpad/webapp/index.html');
      }

    } catch(\Exception $e) {
      $app['monolog']->addInfo('Error: ' . $e->getMessage() . ' Error Code : ' . $e->getCode());
      $errorText = $e->getMessage();
    }

  $app['monolog']->addInfo(sprintf("User [%s] input wrong password.", $username));
  
  return $app['twig']->render('layout.twig', array(
  	'error' => $errorText,
  	'mainblock' => 'elements/login.html'
  ));
});

$c->get('/logout', function() use ($app, $config) {
	$app['session']->set('user', null);
	return $app->redirect('/');
});

return $c;