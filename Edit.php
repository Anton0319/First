<?php if(!empty($selected_new)): ?>
<!--Извлекаем элементы из массива в цикле-->
<?php foreach($selected_new as $snew): ?>
	
<!--Блок для вывода ответа сервера-->			
<span id="answer"></span>

<form class="row" id="add_form" action="/admin/edit/edit"  method="post" enctype="multipart/form-data">
    <div class="col-12">
	    Все поля обязательны для заполнения
	</div>
    <div class="col-12 py-3">
	    <!--Скрытые поля для передачи значений в контроллер-->	
		<input name="idNews" type="hidden" id="idNews"  value="<?=$snew->id;?>" /><br/>
		<input name="imageName" type="hidden" id="idNews"  value="<?=$snew->image;?>" /><br/>
		<!--Поле для вывода сообщения об ошибке в поле Заголовок -->	
		<span class="error1"></span><br/>
		
        <input name="title" id="title" type="text" class="form_title_box" value="<?=$snew->title;?>" /><br/>
    </div>
	
	<!--Поле для вывода превью изображения-->				
    <div class="uploading_image">
        <div id="container-image"><img id="loaded-image" src="../images/<?=$snew->image;?>">
		</div>
	</div>
	<!--Поле для загрузки изображения-->	
	<div class="form-group">
        <label for="image">Загрузить картинку:</label>
		<!--Поле для вывода сообщения об ошибке -->	
		<span class="error2"></span><br/>
		
        <input type="file" name="upload" id="image" accept="image/*">
    </div>
    <!--Поле для вывода превью изображения-->
	<div id="selectCategory">
	    <p>Выберите категорию</p>
		<fieldset>
		
            <!--Поле для вывода сообщения об ошибке-->			
		    <span class="error3"></span><br/>
					
            <input name="selecting_category" type="radio" value="business"><label>Бизнесс</label><br/>
	        <input name="selecting_category" type="radio" value="technology"><label>Технологии</label><br/>
		    <input name="selecting_category" type="radio" value="sport"><label>Спорт</label><br/>
		    <input name="selecting_category" type="radio" value="art"><label>Искусство</label><br/>
		    <input name="selecting_category" type="radio" value="lifestyle"><label>Lifestyle</label><br/>
		    <input name="selecting_category" type="radio" value="agro"><label>Агро</label><br/>
		    <input name="selecting_category" type="radio" value="different"><label>Разное</label><br/>
					
		</fieldset>
	</div>
	<!--Поле для ввода текста-->	
    <div class="col-12 py-3">
	    <!--Поле для вывода сообщения об ошибке-->
		<span class="error4"></span>
		
        <textarea name="text" id="text" class="txta" placeholder="Сообщение"><?=$snew->text;?></textarea><br/>
										
    </div>
	
    <div class="col-12 py-3 text-center"> 
	    <input type="submit" class="btn contact_btn" id="send" value="Отправить"/>
	</div>
</form>
	<?php endforeach; ?>
        <?php else: ?>
            <h3>Posts not found...</h3>
    <?php endif; ?>	
	
<script>
//Функция для отображения выбранной категории, хранимой в базе данных
$('input[type="radio"]').val(['<?=$snew->category;?>']);

$(document).ready(function () {
 
 
    //Функция для отображения превью изображения
    document.getElementById('image').onchange = function (e) {
		$('#container-image').empty();
	    var image = e.target.files[0];
        window.loadImage(image, function (img) {
            if (img.type === "error") {
                console.log("couldn't load image:", img);
            } else {
                window.EXIF.getData(image, function () {
                    console.log("load image done!");
                    var orientation = window.EXIF.getTag(this, "Orientation");
                    var canvas = window.loadImage.scale(img,
                        {orientation: orientation || 0, canvas: true, maxWidth: 200});
                    $("#container-image").append(canvas);
                });
            }
        });
    };
 
  
 
  
    //Функция обработки форм до ее отправки на основе плагина jQuery Validate
    $ ("#add_form").validate({
	    //Прописываем правила для полей   
        rules:{
		    title:{
                required: true,//поле должно быть заполнени
                minlength: 3,//минимальное количество символов - 3
                maxlength: 150,//максимальное количество символов - 150
            },
			text:{
                required: true,//поле должно быть заполнени
            },
			upload:{
				accept: "image/*",//поле принимает только форматы изображений
			},
			selecting_category:{
                required: true,//поле должно быть заполнени
            },
                
        },
		//Прописываем соответствующие сообщения об ошибках
        messages:{
            title:{
                required: "Это поле обязательно для заполнения",
                minlength: "Название темы должно быть минимум 3 символа",
                maxlength: "Максимальное число символов - 150",
            },
			upload:{
				accept: "Нужно загружать только изображения",
			},
			text:{
                required: "Это поле обязательно для заполнения",
            }, 
			selecting_category:{
                required: "Необходимо выбрать категорию",
            }, 
        },
		//Прописываем в каких элементах необходимо выводить ошибки
		errorPlacement: function(error, element) {
			if (element.attr("name") == "title") error.appendTo($('span.error1'));
			if (element.attr("name") == "upload") error.appendTo($('span.error2'));
			if (element.attr("name") == "selecting_category") error.appendTo($('span.error3'));
			if (element.attr("name") == "text") error.appendTo($('span.error4'));
		},
		//Функция обработчик, срабатывает если все поля заполнены без ошибок
		submitHandler: function (form) {
			event.preventDefault();	
			var form = $('#add_form').get(0); 
		
            $.ajax({
				url: '/admin/edit/edit',
                type: 'POST',
                data: new FormData(form),
			    cache:false,
			    contentType: false,
			    processData: false,
			    dataType: 'json',
                success: function (response) {
					$('#answer').css('background-color', response.color);
				    $('#answer').text(response.answer);
				    $('#add_form').hide();
                    console.log(response);
                },
				error: function() {
				    alert('Ошибка');
			    }
					 
                 
            });
             return false; // required to block normal submit since you used ajax
        }
,	
	
    });
		  
 
});
 </script>
