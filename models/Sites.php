<?php

class Sites extends BaseModel{
	public $table = 'sites';
	public $primaryKey = 'id';

	public function attributeLabels(){
		return [
			'id' => App::t('ID'),
			'category' => App::t('База'),
			'num' => App::t('Номер'),
			'link' => App::t('Ссылка'),
			'note' => App::t('Примечание'),
		];
	}

	public function rules(){
		return [
			[['link', 'category'], 'required'],
			[['category'], 'number'],
		];
	}

	public function getDomain($link = ''){
		if( empty($link) ) $link = $this->link;
		if( empty($link) ) return '';

		$url = parse_url($link);
		return $url['host'];
	}

	public function shortLink(){
		$link = explode('/', $this->link);
		if( count($link) < 4 ) return implode('/', $link);
		$short[] = $link[0];
		$short[] = $link[1];
		$short[] = $link[2];
		if( !empty($link[3]) ){
			$short[] = mb_substr($link[3], 0, 3).'...';
		}else{
			$short[] = $link[3];
		}
		return implode('/', $short);
	}

	public function updateNum($category){
		App::get()->pdo->exec('
			SET @row_number = 0;
			UPDATE '.$this->table.'
			SET num = (@row_number:=@row_number + 1)
			WHERE category = '.intval($category).'
			ORDER BY id
		');
	}
}
