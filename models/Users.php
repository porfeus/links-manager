<?php
class Users extends BaseModel{
	public $table = 'users';
	public $primaryKey = 'id';

	public function attributeLabels(){
		return [
			'id' => App::t('ID'),
			'login' => App::t('Логин'),
			'password' => App::t('Пароль'),
			'email' => App::t('E-mail'),
			'language' => App::t('Язык'),
			'activated_time' => App::t('Активирован с'),
			'activated_add_time' => App::t('Активирован до'),
			'users_limit' => App::t('Лимит человек'),
			'users_online' => App::t('IP-адреса онлайн'),
			'email_send_time' => App::t('Последняя отправка письма'),
			'ip_old' => App::t('Старый IP'),
			'ip_new' => App::t('Новый IP'),
			'last_enter_time' => App::t('Последний вход'),
			'last_update_time' => App::t('Последняя активность'),
		];
	}

	public function rules(){
		return [
			[['login', 'password', 'users_limit'], 'required'],
			[['login'], 'unique'],
		];
	}

	public function activatedDate(){
		if( $this->activated_time == 0 ) return App::t('Нет');
		return date('d.m.Y', $this->activated_time);
	}

	public function activatedEndDate(){
		if( $this->activated_time == 0 ) return ceil($this->activated_add_time / 86400).' '. App::t('дн.');
		return date('d.m.Y', intval($this->activated_time + $this->activated_add_time));
	}

	public function enterDate(){
		if( $this->last_enter_time == 0 ) return App::t('Нет');
		return date('d.m.Y', intval($this->last_enter_time));
	}

	public function onLine(){
		if( $this->last_update_time + 600 > time() ) return 1;
		return 0;
	}

	public function usersOnlineLimited(){
		if( $this->blank('users_online') ) return false;

		$usersOnline = unserialize($this->users_online);
		$ip = $_SERVER['REMOTE_ADDR'];

		unset($usersOnline[$ip]);

		foreach($usersOnline as $userIp=>$userTime){
			if( $userTime + 600 > time() ) continue;
			unset($usersOnline[$userIp]);
		}

		if( count($usersOnline) >= $this->users_limit ) return true;
		return false;
	}

	public function usersOnlineSet(){
		$usersOnline = [];
		if( !$this->blank('users_online') ){
			$usersOnline = unserialize($this->users_online);
		}
		$ip = $_SERVER['REMOTE_ADDR'];

		$usersOnline[$ip] = time();
		$this->users_online = serialize($usersOnline);
	}

	public function usersOnlineDel(){
		$usersOnline = [];
		if( !$this->blank('users_online') ){
			$usersOnline = unserialize($this->users_online);
		}
		$ip = $_SERVER['REMOTE_ADDR'];

		unset($usersOnline[$ip]);
		$this->users_online = serialize($usersOnline);
	}

	public function getLanguage(){
		return $this->ifnull('language', App::get()->config['default_language']);
	}
}
