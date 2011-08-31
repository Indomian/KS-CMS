<?php

include_once MODULES_DIR.'/main/libs/class.CRectGenerator.php';

/**
 * Класс работы с изображениями v2.6
 * Изменение изображений по след. параметрам: Ширина, Высота, Пропорциональность, Белые поля.
 *
 * Автор: Егор Болгов
 */

class CImageResizer extends CBaseObject
{
	protected $sFilename;
	protected $iWidth;
	protected $iHeight;
	protected $iType;
	protected $obRectangle;
  	/**
  	 * Переменная для хранения нового изображения после ресайза
  	 */
  	protected $newImage;
  	/**
  	 * Статический массив допустимых расширений
  	 */
  	static $arAllowExt=array('jpg','jpeg','png');

	/**
	 * Конструктор, принимает только путь к файлу, абсолютный на сервере
	 */
	function __construct($inputfile)
	{
		$this->sFilename = $inputfile;
		if(!is_file($inputfile))
			throw new CError('SYSTEM_NOT_A_FILE');

		$info = pathinfo($inputfile); // Информация о файле
		list($width, $height, $type, $attr) = getimagesize($this->sFilename);

		$this->iWidth = $width;
		$this->iHeight = $height;
		$this->iType = $type;
		$this->obRectangle=false;

		if($this->iWidth*$this->iHeight*4>(GetMaxMemory()-1024*1024))
		{
			throw new CError(SYSTEM_NO_MEMORY,1,($this->width_orig*$this->height_orig*4).'/'.(GetMaxMemory()-1024*1024));
		}
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

	/**
	 *  Метод изменения размера изображения
	 * @param $image_w - требуемая ширина изображения
	 * @param $image_h - требуемая высота изображения
	 */
	function Resize($image_w,$image_h=false)
	{
		if(is_object($image_w) && $image_w instanceof CRectGenerator)
		{
			$this->obRectangle=$image_w;
		}
		else
		{
			if($image_w == 0 && $image_h == 0)
				throw new CError('WH by zero');
			$this->obRectangle=new CRectGenerator($this->iWidth,$this->iHeight,$image_w,$image_h);
		}
		$this->obRectangle->SetSourceSize($this->iWidth,$this->iHeight);
		if($arCoord=$this->obRectangle->GetCoord())
		{
			switch ($this->iType)
			{
				case 2: $im = imagecreatefromjpeg($this->sFilename);  break;
				case 3:
					//Поддержка прозрачных png
					$im = imagecreatefrompng($this->sFilename);
					imagealphablending($im, false);
					imagesavealpha($im, true);
				break;
				default:  throw new CError('PHOTOGALLERY_WRONG_FILE', E_USER_WARNING);  break;
			}
			if($arCoord['w1']==$arCoord['w'] && $arCoord['h1']==$arCoord['h'])
			{
				$this->newImage=$im;
				return true;
			}
			else
			{
				$newImg = imagecreatetruecolor($arCoord['w1'], $arCoord['h1']);
				if(imagecopyresampled($newImg, $im, $arCoord['x1'], $arCoord['y1'], $arCoord['x'], $arCoord['y'], $arCoord['w1'],$arCoord['h1'], $arCoord['w'], $arCoord['h']))
				{
					$this->newImage=$newImg;
					imagedestroy($im);
					return true;
				}
				imagedestroy($im);
			}
		}
		return false;
	}

	/**
	 * Метод выполняет сохранение нового изображения по новому пути
	 */
	function Save($path,$quality=98)
	{
		if($this->newImage)
		{
			$res=@imagejpeg($this->newImage,$path,$quality);
			if(!imagedestroy($this->newImage)) throw new CError('SYSTEM_STRANGE_ERROR');
			$this->newImage=0;
			return $res;
		}
		return false;
	}

	/**
	 * Метод сохраняет изображение в формат PNG
	 */
	function SavePNG($path,$pack=9)
	{
		if($this->newImage)
		{
			$res=@imagepng($this->newImage,$path,$pack,PNG_ALL_FILTERS);
			if(!imagedestroy($this->newImage)) throw new CError('SYSTEM_STRANGE_ERROR');
			$this->newImage=0;
			return $res;
		}
	}
}



