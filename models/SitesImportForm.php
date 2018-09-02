<?php
class SitesImportForm extends Sites{
	public function attributeLabels(){

		return array_merge(parent::attributeLabels(), [
			'variant' => App::t('Откуда импортировать'),
			'field' => App::t('Список ссылок (каждая с новой строки)'),
			'file' => App::t('Выберите файл с компьютера'),
			'server' => App::t('Выберите файл с папки files на сервере'),
			'category' => App::t('В какую базу импортировать'),
			'note' => App::t('Добавить примечание'),
			'unique' => App::t('Удалять дубли доменов?'),
		]);
	}

	public function rules(){
		return [
			[['variant', 'category', 'unique'], 'required'],
			[['field'], 'fieldEmpty'],
			[['file'], 'fileEmpty'],
			[['server'], 'serverFileEmpty'],
		];
	}

	public function fieldEmpty($attribute){
		$value = $this->{$attribute};
		if( $this->variant != 'field' || !empty($value) ) return true;
		$this->addError($attribute, App::t('Значение не может быть пустым'));
		return false;
	}

	public function fileEmpty($attribute){
		$value = $this->{$attribute};
		if( $this->variant != 'file' || !empty($value) ) return true;
		$this->addError($attribute, App::t('Значение не может быть пустым'));
		return false;
	}

	public function serverFileEmpty($attribute){
		$value = $this->{$attribute};
		if( $this->variant != 'server' || !empty($value) ) return true;
		$this->addError($attribute, App::t('Значение не может быть пустым'));
		return false;
	}
}
