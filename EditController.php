<?php

namespace app\controllers\admin;

use app\models\Main;
use fw\core\App;
use fw\core\base\View;
use fw\libs\Pagination;
use app\controllers\BaseValidateTrait;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Класс для редактирования новости
 *
 */
class EditController extends AppController{
	
	public $name;//имя загружаемого изображения
	public $title;//заголовок новости
	public $selecting_category;//выбранная категория
	public $text;//текст новости
	public $dat;//дата публикации
	public $nameImage;//название изображения для сохранения в базе данных
	public $numberOfViews;//количество просмотров
	
	//Трейт для базовой валидации данных пользователя
	use BaseValidateTrait;
	
	
	//Метод получения id новости
	public function getParams(){
        $url = $_SERVER['REQUEST_URI'];
        $url = explode('?', $url);
        $uri = $url[0] . '?';
        if(isset($url[1]) && $url[1] != ''){
            $params = explode('&', $url[1]);
            foreach($params as $param){
                if(!preg_match("#page=#", $param)) $uri .= "{$param}&amp;";
            }
        }
        return $uri;
        }
	//Метод для отображения страницы и данных на ней
	public function indexAction(){
		
		//Подключаем объет модели для работы с базой данных
		$model = new Main;
		
		//Получаем id новости и если она есть, то данные для отображения на страницы из базы данных	
		$param_id = getParams();
		$id = preg_match('#id=[0-9]{1,10}#', $param_id, $matches);
		if($matches != false) {
            $id = explode('=', $matches[0]);
            $d = preg_match('#^[0-9]{1,10}$#', $id[1], $matches_id);
            $selected_id = $matches_id[0];
		     
		    $selected_new = \R::find('news', 'id LIKE ?', ["%$selected_id%"]);
		}else {
			echo "Новость не найдена";
        }

        //Создаем массив данных для передечи его в вид    
        $this->set(compact('selected_new'));
	}
	
	//Метод для сохранения изменений, внесенных на страницы
	public function editAction(){
		//Проверяем получены ли данные методом AJAX
        if($this->isAjax()){
			$model = new Main;
			/*Проверяем, если не загружалось новое изображение, то обрабатываем и сохраняем только текстовые данные изображение. Если загружалось новое изображения,
			то удаяем старое изображение и сохраняем все данные*/
            if(isset($_POST['imageName'])&& empty($_FILES['upload']['tmp_name']) ) {
				
				if(isset($_POST) && $_POST != '') {
				
				    $this->id = $this->baseValidate($_POST['idNews']);
		            $this->title = $this->baseValidate($_POST['title']);
				    $this->selecting_category = $this->baseValidate($_POST['selecting_category']);
				    $this->text = $this->baseValidate($_POST['text']);
				    $this->nameImage = $this->baseValidate($_POST['imageName']);
				
				$this->saveInTheTable($this->id, $this->title, $this->text, $this->nameImage, $this->selecting_category);
								
	            }
			}else{
			
			    $this->id = $this->baseValidate($_POST['idNews']);
				$this->deleteImage($this->id);
			    $this->saveImage();
			    if(isset($_POST) && $_POST != '') {
				
				    $this->id = $this->baseValidate($_POST['idNews']);
		            $this->title = $this->baseValidate($_POST['title']);
				    $this->selecting_category = $this->baseValidate($_POST['selecting_category']);
				    $this->text = $this->baseValidate($_POST['text']);
				    $this->nameImage = $this->name . $this->format;
				
				
				    $this->saveInTheTable($this->id, $this->title, $this->text, $this->nameImage, $this->selecting_category);
		
	            }
			}
	    }   
        
        
    }
	
}
