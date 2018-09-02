<?php
class UsersEditForm extends Users{
	public function attributeLabels(){

		return array_merge(parent::attributeLabels(), [
			'activated_days' => App::t('Дней активации'),
		]);
	}
	public function rules(){

		return array_merge(parent::rules(), [
			[['accounts_num', 'activated_days'], 'number'],
		]);
	}
}
