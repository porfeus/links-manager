<?php

class ComplainForm extends Sites{

	public function attributeLabels(){
		return [
			'id' => App::t('ID'),
			'problem' => App::t('Проблема'),
			'message' => App::t('Описание проблемы'),
		];
	}

	public function rules(){
		return [
			[['id', 'problem'], 'required'],
			[['message'], 'messageEmpty'],
		];
	}

	public function problemList(){
		return [
			App::t('Сайт не работает') => 'not_work',
			App::t('Сайт не этой темы') => 'another_theme',
			App::t('Другое') => 'other',
		];
	}

	public function messageEmpty($attribute){
		$value = $this->{$attribute};
		if( $this->problem != 'other' || !empty($value) ) return true;
		$this->addError($attribute, App::t('Значение не может быть пустым'));
		return false;
	}
}
