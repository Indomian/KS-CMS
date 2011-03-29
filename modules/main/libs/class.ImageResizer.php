<?php
/**
 * Класс работы с изображениями v1.8.3
 * Изменение изображений по след. параметрам: Ширина, Высота, Пропорциональность, Белые поля.
 *
 * Автор: Илья Дорошко, Егор Болгов
 *
 *
 * -= Последние изменения =-
 * 27.02.2010
 * [+] Добавлен контроль доступного объема ОП при попытке открыть файл
 * 02.10.2009
 * [+] Добавлен статический массив доступных расширений файлов.
 * 12.01.2009
 * [исп] Исправлена функция imagerotate(), не работающая в Ubuntu
 * [+-] В функцию изменения размера оригинала добавлена функция Автоповорота
 *
 * 05.01.2009
 * [+] Функция автоматического поворота изображений по данным EXIF
 * [+] Добавлена возможность изменения цвета "белых" полей пример: $this->border_color = '255 255 255';
 * [?] Автоповорот принудителен временно
 *
 * 30.12.2008
 * [+] Функция уменьшения оригинала изображения для последующей легкой обработки
 * [+] Отдельная функция для создания "стандартного" изображения
 * [+] Добавлена возможность указания определенной папки для превью
 * [+] Глобальные переменные с размерами оригиналов и стандартов
 *
 * 29.12.2008
 * [+] Возможность вывода измененного изображения без сохранения через $this->sSave == false
 *
 * 26.12.2008
 * [исп] Обработка вертикальных изображений до квадратных. (ошибка со стартовой точкой)
 *
 * 25.12.2008
 * [исп] Правильная обработка загруженных изображений квадратных размеров
 *
 * 24.12.2008
 * [исп] Создание папок для изображений с одинаковыми width и height, уменьшаемые с пропорцией.
 * 		- Теперь не 500x500, а 500x или x500 в зависимости от пропорционально изменяемой стороны.
 *
 * 22.12.2008
 * [+] Функции для работы с именами и папками будущих изображений
 * [+] Получение EXIF снимка
 * [+-] Автоповорот фотографий на основе EXIF
 * [исп] Бело-черные поля.
 *
 * 19.12.2008
 * [+] Изм. размеров по жестким рамерам с бел. полями
 * [-] Убрана поддержка GIF-изображений
 * [исп] Конкретная работа с с горизонтальными и вертикальными изобр.
 *
 * 18.12.2008
 * [+] Пропорциональное изменение размеров
 * [+] Изм. размеров по жестким размерам (квадрат)
 *
 * -= Краткое описание =-
 * 1. Для работы подключить класс и создать его объект (пр. $IMG = new ImageResizer('путь/к_картинке/имя.jpg'))
 * 2. Для вызова функции изменения изображения, нужно: $IMG->Resize(200, 250);
 *
 * 	 ГДЕ
 *
 * 	 1. Ширина (px)
 *   2. Высота (px)
 *   3. (необяз. параметр) Пропорциональное масштабирование default: true
 *   4. (необяз. параметр) Белые поля (для жестких размеров) default: false
 */

class ImageResizer extends CBaseObject
{
	var $inputfile;
	var $myfile;
	var $newfilename;             // Имя нового файла (полный путь с именем)
	var $newdirname;              // Имя папки для нового файла
	var $file_dirname;            // Абсолютный путь к исх. файлу
	var $file_name;               // Имя файла с расширением
	var $file_name_body;          // Имя файла без расширения
	var $file_ext;                // Расширение файла
	var $width_orig;              // Ширина
	var $height_orig;             // Высота
	var $allow_ext;               // Разрешенные расширения файлов
	var $image_w;                 // Размер width
	var $image_h;                 // Размер height
	var $StandartWidth = 500;     //
	var $StandartHeight = 500;    //
	var $OrigWidth = 3000;         //
	var $OrigHeigth = 3000;        //
	var $image_type;              // Тип изображения
  	var $image_ratio;             // Пропорциональный ресайз
  	var $image_ratio_wb;          // Добавление белых полей при $image_ratio = false
  	var $border_color;             // Цвет "белых" полей.
  	var $quality;                 // Качество 0 - 100
  	var $isSave = true;           // Сохранять конечное изображение на диск
  	var $isCreateDir = true;
  	var $SaveOriginal = false;    // Сохранение оригинала изображения
  	var $square_align = 'center';
  	var $oreintation = 0;
  	/**
  	 * Переменная для хранения нового изображения после ресайза
  	 */
  	protected $newImage;
  	/**
  	 * Статический массив допустимых расширений
  	 */
  	static $arAllowExt=array('jpg','jpeg','png');

	function __construct($inputfile,$bRoot=false)
	{
		$this->inputfile = $inputfile;
		if(!$bRoot) $inputfile = ROOT_DIR.$inputfile;
		if(!is_file($inputfile)) { throw new CError('No input file! ['.$inputfile.']');}

		$info = pathinfo($inputfile); // Информация о файле

		/* Проверка расширения */

		if(!$bRoot && !in_array(strtolower($info['extension']),self::$arAllowExt))
			throw new CError('PHOTOGALLERY_WRONG_FILE',0,$inputfile);
		/* //Проверка расширения */

		list($width, $height, $type, $attr) = getimagesize($inputfile);

		$this->myfile = $inputfile;
		$this->file_dirname = $info['dirname'];
		$this->file_name = $info['basename'];
		$this->file_name_body = $info['filename'];
		$this->file_ext = $info['extension'];
		$this->width_orig = $width;
		$this->height_orig = $height;
		$this->image_type = $type;

		if($this->width_orig*$this->height_orig*4>(GetMaxMemory()-1024*1024))
		{
			throw new CError(SYSTEM_NO_MEMORY,1,($this->width_orig*$this->height_orig*4).'/'.(GetMaxMemory()-1024*1024));
		}
		$this->quality = 93;
	}

	/**
	 * Деструктор выполняет автоматическое удаление изображения
	 */
	function __destruct()
	{
		if($this->newImage)
		{
			imagedestroy($this->newImage);
			$this->newImage=0;
		}
	}

	function NewDirName($w,$h)
	{
		if($w == 0) { $w = ""; }
		if($h == 0) { $h = ""; }
		$delim = "x";
		return $w.$delim.$h;
	}

	function NewFileName()
	{
		$name = $this->file_name;
		$path = $this->NewDirCreate();
		$full_path = $path."/".$name;
		if(file_exists($full_path))
		{
			$open = opendir($path);
			while($imgs = readdir($open))
			{
				//if($imgs == $name) { $name = rand()."_".$name; }
			}
		}
		return $path."/".$name;
	}

	function NewDirCreate()
	{
		$newdir = $this->file_dirname."/".$this->newdirname;
		if(!is_dir($newdir) && $this->isSave == true)
		{
			@mkdir($newdir);
		}
		return $newdir;
	}

	function CreateOriginal()
	{
		$exif = $this->ExifData();
		if(!empty($exif['Orientation'])) { $this->oreintation = $exif['Orientation']; }

		$this->isCreateDir = false;
		$this->Resize($this->OrigWidth,$this->OrigHeigth);
		$this->isCreateDir = true;
		$this->__construct($this->inputfile);

		if($this->oreintation > 0)
		{
			$this->AutoRotate();
			$this->__construct($this->inputfile);
		}
		return array($this->width_orig,$this->height_orig);
	}

	function CreateStandart()
	{
		$this->Resize($this->StandartWidth,$this->StandartHeigth,true,false,'standart');
	}

	/**
	 *  Метод изменения размера изображения
	 * @param $image_w - требуемая ширина изображения
	 * @param $image_h - требуемая высота изображения
	 * @param $image_ratio - сохранять пропорции изображения, обрезав лишнее
	 * @param $image_ratio_wb - сохранять пропорции изображения, добавив белые границы
	 * @param $new_dir - имя новой папки, если не указано генерируется автоматически
	 * */
	function Resize($image_w,$image_h,$image_ratio=true, $image_ratio_wb=false,$newdir=false)
	{
		if($image_w == 0 && $image_h == 0) { throw new CError('WH by zero');}

		/* Автоповорот */
		/*
		$this->AutoRotate();
		$this->__construct($this->inputfile);
		*/
		/* //Автоповорот */


		// Имя папки для файла
		if($this->isCreateDir == true)
		{
			if(strlen($newdir) > 0)
			{
				$this->newdirname = $newdir;
			}
			else
			{
				if($image_w == $image_h && $image_ratio == true)
				{
					if($this->width_orig > $this->height_orig) { $this->newdirname = $this->NewDirName($image_w,0);	 }
					else { $this->newdirname = $this->NewDirName(0,$image_h); }
				}
				else
				{
					$this->newdirname = $this->NewDirName($image_w,$image_h);
				}
			}
		}
		// -------------------

		if($image_ratio == true)
		{
			if($image_ratio_wb==true)
			{
				//Режим усечения изображения по центру
				if($image_w == 0) { $image_w = $image_h; }
				if($image_h == 0) { $image_h = $image_w; }
			}
			else
			{
				if($image_w > $this->width_orig && $image_h > $this->height_orig)
				{
					$image_w = $this->width_orig;
					$image_h = $this->height_orig;
				}
				elseif($image_w == 0)
				{
					$aspect_ratio = (float) $this->width_orig / $this->height_orig;
					$image_w = round($image_h * $aspect_ratio);
				}
				elseif($image_h == 0)
				{
					$aspect_ratio = (float) $this->height_orig / $this->width_orig;
					$image_h = round($image_w * $aspect_ratio);
				}
				elseif($image_w == $image_h)
				{
					if($this->width_orig > $this->height_orig || $this->width_orig == $this->height_orig)
					{
						$aspect_ratio = (float) $this->height_orig / $this->width_orig;
						$image_h = round($image_w * $aspect_ratio);
					}

					if($this->width_orig < $this->height_orig)
					{
						$aspect_ratio = (float) $this->width_orig / $this->height_orig;
						$image_w = round($image_h * $aspect_ratio);
					}
				}
				else
				{
					if($this->width_orig > $this->height_orig || $this->width_orig == $this->height_orig)
					{
						$aspect_ratio = (float) $this->height_orig / $this->width_orig;
						$image_h = round($image_w * $aspect_ratio);
					}

					if($this->width_orig < $this->height_orig)
					{
						$aspect_ratio = (float) $this->width_orig / $this->height_orig;
						$image_w = round($image_h * $aspect_ratio);
					}
				}
			}
		}
		else
		{
			if($image_w == 0) { $image_w = $image_h; }
			if($image_h == 0) { $image_h = $image_w; }
		}

		$this->Process($image_w,$image_h,$image_ratio,$image_ratio_wb);

	}

	/**
	 * Метод выполняет обработку изображения и выполнение операций
	 * добавлен контроль расхода оперативной памяти.
	 */
	function Process($image_w,$image_h,$image_ratio,$image_ratio_wb)
	{
		if((($this->width_orig*$this->height_orig*4)+$image_h*$image_w*4)>(GetMaxMemory()-1024*1024))
		{
			throw new CError(SYSTEM_NO_MEMORY,1,(($this->width_orig*$this->height_orig*4)+$image_h*$image_w*4).'/'.(GetMaxMemory()-1024*1024));
		}
		switch ($this->image_type)
	    {
	        case 2: $im = imagecreatefromjpeg($this->myfile);  break;
	        case 3: $im = imagecreatefrompng($this->myfile); break;
	        default:  throw new CError('PHOTOGALLERY_WRONG_FILE', E_USER_WARNING);  break;
	    }

	    if($image_ratio == true)
	    {
			if($image_ratio_wb==false)
			{
				$newImg = imagecreatetruecolor($image_w, $image_h);
				imagecopyresampled($newImg, $im, 0, 0, 0, 0, $image_w, $image_h, $this->width_orig, $this->height_orig);
			}
			else
			{
				//Усечение изображения по центру
				$newImg = imagecreatetruecolor($image_w, $image_h);
				$x=0;
				$y=0;
				$w=$this->width_orig;
				$h=$this->height_orig;
				if($this->width_orig >= $this->height_orig)
				{
					$scale=$image_h/$this->height_orig;
					$image_w_r = round($this->width_orig*$scale);
					if($image_w_r>$image_w)
					{
						$x=round(($image_w_r-$image_w)/2/$scale);
						$w=round($image_w_r/$scale-$x);
					}
				}
				else
				{
					$scale=$image_w/$this->width_orig;
					$image_h_r = round($this->height_orig*$scale);
					if($image_h_r>$image_h)
					{
						$y=round(($image_h_r-$image_h)/2/$scale);
						$h=round($image_h_r/$scale-$y);
					}
				}
				imagecopyresampled($newImg, $im, 0, 0, $x, $y, $image_w, $image_h, $w, $h);
			}
	    }
	    else
	    {
	    	if($image_ratio_wb == true)
	    	{
	    		$wb_type = 0;

	    		if($this->width_orig > $this->height_orig || $this->width_orig == $this->height_orig)
				{
					$aspect_ratio = (float) $this->height_orig / $this->width_orig;
					$image_h_r = round($image_w * $aspect_ratio);
					$image_w_r = $image_w;
					$wb_type = 1;
					if($image_h < $image_h_r)
					{
						$aspect_ratio = (float) $this->width_orig / $this->height_orig;
						$image_w_r = round($image_h * $aspect_ratio);
						$image_h_r = $image_h;
						$wb_type = 2;
					}
				}
				elseif($this->width_orig < $this->height_orig)
				{
					$aspect_ratio = (float) $this->width_orig / $this->height_orig;
					$image_w_r = round($image_h * $aspect_ratio);
					$image_h_r = $image_h;
					$wb_type = 2;
					if($image_w < $image_w_r)
					{
						$aspect_ratio = (float) $this->height_orig / $this->width_orig;
						$image_h_r = round($image_w * $aspect_ratio);
						$image_w_r = $image_w;
						$wb_type = 1;
					}
				}
			   	$newImg_first = imagecreatetruecolor($image_w_r, $image_h_r);
				imagecopyresampled($newImg_first, $im, 0, 0, 0, 0, $image_w_r, $image_h_r, $this->width_orig, $this->height_orig);


	    		$newImg = imagecreatetruecolor($image_w, $image_h);
	    		if($this->border_color != "")
	    		{
	    			$colors = explode(" ",$this->border_color);
	    			$r = $colors[0];
	    			$g = $colors[1];
	    			$b = $colors[2];
	    		} else { $r = 255; $g = 255; $b = 255; }

    			$background_color = imagecolorallocate($newImg, $r, $g, $b);
			 	imagefill($newImg, 0, 0, $background_color);

			 	if($wb_type == 1)
			 	{
			 		$one = round(($image_h - $image_h_r)/2);

			 		imagecopyresampled($newImg, $newImg_first, 0, round(($image_h - $image_h_r)/2), 0, 0, $image_w_r, $image_h_r, $image_w_r, $image_h_r);
			 	}

			 	if($wb_type == 2)
			 	{
			 		imagecopyresampled($newImg, $newImg_first,  round(($image_w - $image_w_r)/2), 0, 0, 0, $image_w_r, $image_h_r, $image_w_r, $image_h_r);
			 	}

	    	}
			else
	    	{
	    		//Только в квадратный вид
	    		if($this->width_orig > $image_w && $this->height_orig > $image_h)
	    		{
	    			$WH = max($image_h,$image_w);
	    			$image_h = $WH;
	    			$image_w = $WH;

	    			$newImg = imagecreatetruecolor($image_w, $image_h);


					if($this->width_orig > $this->height_orig || $this->width_orig == $this->height_orig)
					 {
					 	imagecopyresampled($newImg, $im, 0, 0,
					 	round((max($this->width_orig,$this->height_orig)-min($this->width_orig,$this->height_orig))/2),
					 	0, $image_w, $image_h,
					 	min($this->width_orig,$this->height_orig), min($this->width_orig,$this->height_orig));
					 }

					 if($this->width_orig < $this->height_orig)
					 {
					 	switch($this->square_align)
					 	{
					 		case 'top': $start = 0;
					 		case 'center': $start = round((max($this->width_orig,$this->height_orig)-min($this->width_orig,$this->height_orig))/2);
					 	}
					 	imagecopyresampled($newImg, $im, 0, 0, 0, $start, $image_w, $image_h,
					              min($this->width_orig,$this->height_orig), min($this->width_orig,$this->height_orig));
					 }

	    		}
	    		else
	    		{
	    			// Если новые размеры больше старых, то изображение остается неизменным
	    			$image_w = $this->width_orig;
	    			$image_h = $this->height_orig;
	    			$newImg = imagecreatetruecolor($image_w, $image_h);
	    			imagecopyresampled($newImg, $im, 0, 0, 0, 0, $image_w, $image_h, $this->width_orig, $this->height_orig);
	    		}
	    	}

	    }


	    if($this->isSave == true)
	    {
		    // Генерация нового имени файла пример: путь/ширинаХвысота_оригимяфайла
		    $newfilename = $this->NewFileName();
		    switch ($this->image_type)
		    {
		        //case 1: imagegif($newImg,$newfilename); break;
		        case 2: imagejpeg($newImg,$newfilename,$this->quality);  break;
		        case 3: imagepng($newImg,$newfilename); break;
		        default:  throw new CError('Failed resize image!', E_USER_WARNING);  break;
		    }
	    }
	    $this->newImage=$newImg;
	 	imagedestroy($im);
	}

	/**
	 * Метод выполняет сохранение нового изображения по новому пути
	 */
	function Save($path)
	{
		if($this->newImage)
		{
			$res=@imagejpeg($this->newImage,$path,$this->quality);
			if(!imagedestroy($this->newImage)) throw new CError('SYSTEM_STRANGE_ERROR');
			$this->newImage=0;
			return $res;
		}
	}

	/**
	 * Метод выполняет обрезку изображение по заданным координатам
	 */
	function Crop($x1,$y1,$x2,$y2)
	{
		if($x2<$x1){$tmp=$x2;$x2=$x1;$x1=$x2;}
		if($y2<$y1){$tmp=$y2;$y2=$y1;$y1=$y2;}
		if($x1<0) $x1=0;
		if($y1<0) $y1=0;
		if($x2>$this->width_orig) $x2=$this->width_orig;
		if($y2>$this->height_orig)$y2=$this->height_orig;
		$newWidth=$x2-$x1;
		$newHeight=$y2-$y1;
		if($this->width_orig==$newWidth && $this->height_orig==$newHeight) return true;
		if($newWidth==0||$newHeight==0) return false;
		switch ($this->image_type)
	    {
	        case 2: $im = imagecreatefromjpeg(ROOT_DIR.$this->inputfile);  break;
	        case 3: $im = imagecreatefrompng(ROOT_DIR.$this->inputfile); break;
	        default:  throw new CError('Unsupported filetype!', E_USER_WARNING);  break;
	    }
		$newImg = imagecreatetruecolor($newWidth, $newHeight);
	    imagecopyresampled($newImg, $im, 0, 0, $x1, $y1, $newWidth,$newHeight,$newWidth,$newHeight);
	    switch ($this->image_type)
	    {
	        //case 1: imagegif($newImg,$newfilename); break;
	        case 2: return imagejpeg($newImg,ROOT_DIR.$this->inputfile,$this->quality);  break;
	        case 3: return imagepng($newImg,ROOT_DIR.$this->inputfile); break;
	        default:  throw new CError('Failed crop image!', E_USER_WARNING);  break;
	    }
	}

	/* ---================--- */
	function ExifData()
	{
		$exif_data = @exif_read_data($this->myfile);
		return $exif_data;
	}

	function ImageRotateSec($src_img, $angle, $bicubic=false)
	{

	   // convert degrees to radians
	   $angle = $angle;
	   $angle = deg2rad($angle);

	   $src_x = imagesx($src_img);
	   $src_y = imagesy($src_img);

	   $center_x = floor($src_x/2);
	   $center_y = floor($src_y/2);

	   $cosangle = cos($angle);
	   $sinangle = sin($angle);

	   $corners=array(array(0,0), array($src_x,0), array($src_x,$src_y), array(0,$src_y));

	   foreach($corners as $key=>$value) {
	     $value[0]-=$center_x;        //Translate coords to center for rotation
	     $value[1]-=$center_y;
	     $temp=array();
	     $temp[0]=$value[0]*$cosangle+$value[1]*$sinangle;
	     $temp[1]=$value[1]*$cosangle-$value[0]*$sinangle;
	     $corners[$key]=$temp;
	   }

	   $min_x=1000000000000000;
	   $max_x=-1000000000000000;
	   $min_y=1000000000000000;
	   $max_y=-1000000000000000;

	   foreach($corners as $key => $value) {
	     if($value[0]<$min_x)
	       $min_x=$value[0];
	     if($value[0]>$max_x)
	       $max_x=$value[0];

	     if($value[1]<$min_y)
	       $min_y=$value[1];
	     if($value[1]>$max_y)
	       $max_y=$value[1];
	   }

	   $rotate_width=round($max_x-$min_x);
	   $rotate_height=round($max_y-$min_y);

	   $rotate=imagecreatetruecolor($rotate_width,$rotate_height);
	   imagealphablending($rotate, false);
	   imagesavealpha($rotate, true);

	   //Reset center to center of our image
	   $newcenter_x = ($rotate_width)/2;
	   $newcenter_y = ($rotate_height)/2;

	   for ($y = 0; $y < ($rotate_height); $y++) {
	     for ($x = 0; $x < ($rotate_width); $x++) {
	       // rotate...
	       $old_x = round((($newcenter_x-$x) * $cosangle + ($newcenter_y-$y) * $sinangle))
	         + $center_x;
	       $old_y = round((($newcenter_y-$y) * $cosangle - ($newcenter_x-$x) * $sinangle))
	         + $center_y;

	       if ( $old_x >= 0 && $old_x < $src_x
	             && $old_y >= 0 && $old_y < $src_y ) {

	           $color = imagecolorat($src_img, $old_x, $old_y);
	       } else {
	         // this line sets the background colour
	         $color = imagecolorallocatealpha($src_img, 255, 255, 255, 127);
	       }
	       imagesetpixel($rotate, $x, $y, $color);
	     }
	   }

	  return($rotate);
	}


	function Rotate($src, $degrees)
	{
		$dest = imagecreatefromjpeg($src);
		//$dest = imagerotate($dest, $degrees, 0); - не работает в Ubuntu
		$dest = $this->ImageRotateSec($dest, $degrees);
		return $dest;
	}

	function Mirror($src, $type)
	{
		$imgsrc = imagecreatefromjpeg($src);
		$width = imagesx($imgsrc);
		$height = imagesy($imgsrc);
		$imgdest = imagecreatetruecolor($width, $height);
		for ($x=0 ; $x<$width ; $x++)
		{
			for ($y=0 ; $y<$height ; $y++)
			{
				if ($type == 1) imagecopy($imgdest, $imgsrc, $width-$x-1, $y, $x, $y, 1, 1);
				if ($type == 2) imagecopy($imgdest, $imgsrc, $x, $height-$y-1, $x, $y, 1, 1);
				if ($type == 3) imagecopy($imgdest, $imgsrc, $width-$x-1, $height-$y-1, $x, $y, 1, 1);
			}
		}

		return $imgdest;
	}

	function AutoRotate($orient=true)
	{
		$source = $this->myfile;
		$exif = $this->ExifData();
		if(!empty($exif['Orientation'])) { $orientation = $exif['Orientation']; }
		else { $orientation = $this->oreintation; }

		// Автоматический поворот изображений (для камер с датчиком ориентации)

		if($orientation > 0 && $orient)
		{
			switch($orientation)
		    {
		        case 1: // nothing
		        break;

		        case 2: // horizontal flip
		            $dest = $this->Mirror($source, 1);
		        break;

		        case 3: // 180 rotate left
		            $dest = $this->Rotate($source,180);
		        break;

		        case 4: // vertical flip
		            $dest = $this->Mirror($source, 2);
		        break;

		        case 5: // vertical flip + 90 rotate right
		            $dest_first = $this->Mirror($source, 2);
		            $dest = $this->Rotate($dest_first,-90);
		        break;

		        case 6: // 90 rotate right
		            $dest = $this->Rotate($source,-90);
		        break;

		        case 7: // horizontal flip + 90 rotate right
		            $dest_first = $this->Mirror($source, 1);
		            $dest = $this->Rotate($source,-90);
		        break;

		        case 8:    // 90 rotate left
		            $dest = $this->Rotate($source,90);
		        break;
		    }
		    if($dest)
		    {
			    imagejpeg($dest,$this->myfile,$this->quality);
			    imagedestroy($dest);
		    }
		}
	}

}
?>
