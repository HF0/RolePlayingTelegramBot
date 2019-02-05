<?php
use RolBot\Config\Configuration;
?>
<nav class="navbar navbar-dark bg-dark navbar-expand-lg mb-4">
  <a class="navbar-brand" href="./#"><?= Configuration::get('bot_username'); ?></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" href="./infoadmin.html" ng-cloak>Info</a>
      </li>
      <li class="nav-item">
          <a class="nav-link" href="./character.html" ng-cloak>{{ 'PLAYERS.TITLE' | translate}}</a>
        </li>
      <li class="nav-item">
          <a class="nav-link" href="./fightgroup.html" ng-cloak>{{ 'FIGHT.TITLE' | translate}}</a>
        </li>
      <li class="nav-item">
          <a class="nav-link" href="./util.html" ng-cloak>{{ 'UTILS.TITLE' | translate}}</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="./users.html" ng-cloak>Users</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="./dev.html" ng-cloak>Dev</a>
      </li>
	</ul>
    <div class="mr-3" ng-controller="TranslateController">
      <button class="btn btn-outline-success my-2 my-sm-0" style="outline: none;" ng-class="{'active': isCurrentLanguage('en')}" ng-click="changeLanguage('en')" ng-cloak>EN{{$translate.use()}}</button>
      <button class="btn btn-outline-success my-2 my-sm-0" style="outline: none;" ng-class="{'active': isCurrentLanguage('es')}" ng-click="changeLanguage('es')" ng-cloak>ES</button>
    </div>
    <button ng-controller="LogoutController" class="btn btn-outline-success my-2 my-sm-0" ng-click="logout()">Logout</button>
  </div>
</nav>
