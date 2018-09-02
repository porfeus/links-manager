<?php
class UsersGenerateForm extends Users{
	public function attributeLabels(){

		return array_merge(parent::attributeLabels(), [
			'accounts_num' => App::t('Количество аккаунтов'),
			'activated_days' => App::t('Дней активации'),
			'language' => App::t('Язык сообщения'),
		]);
	}

	public function rules(){
		return [
			[['accounts_num', 'activated_days', 'users_limit', 'language'], 'required'],
			[['accounts_num', 'activated_days', 'users_limit'], 'number'],
		];
	}
}
