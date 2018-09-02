<?php
class UsersCreateForm extends Users{
	public function attributeLabels(){

		return array_merge(parent::attributeLabels(), [
			'activated_days' => App::t('Дней активации'),
			'language' => App::t('Язык сообщения'),
		]);
	}
	public function rules(){

		return array_merge(parent::rules(), [
			[['language'], 'required'],
			[['accounts_num', 'activated_days'], 'number'],
		]);
	}
}
