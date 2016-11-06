<?php

namespace Core\Entity;

class Entity{

    public function getUploadRefs()
    {
        return require ROOT.'\App\Config\UploadRef.php';
    }

	public function __get($key)
	{	
		$method = 'get'.ucfirst($key);
		if(method_exists($this, $method)){
			$this->$key = $this->$method();
        }else if(method_exists($this, $m = $method.'_id')){
            $this->$key = $this->$m();
		}else{
            foreach($this->getUploadRefs() as $type){

                $method = 'get'.ucfirst($type);
                if(method_exists($this, $method)){
                    $this->$key = $this->$method();
                    break;
                }
            }
        }
        return $this->$key;
	}

    public function getClassName()
    {
        $class = explode("\\", get_called_class());
        $class = strtolower(end($class));

        return $class;
    }

    public function getClass()
    {
        return get_called_class();
    }

    public function getFilePath()
    {
        return  ROOT.D_S.'public'.D_S.'img'.D_S.$this->getClassName().'s';
    }

    public function preUpload($fichier)
    {
        $pos_extension = strpos($fichier['name'], '.');
        $extension = substr($fichier['name'], $pos_extension);
        $newName = uniqid().$extension;

        return $newName;
    }

	public function upload($fichier, $name, $dir = null)
	{
        $dir = $dir? $dir: $this->getFilePath();
        if(!is_dir($dir)){
            if(!mkdir($dir, '0777', true )){
                throw new \Exception('repertoire non cree');
            }
            //chmod($repertoire, '0777');
		}
	    $name = $dir.D_S.$name;
		$moved = move_uploaded_file($fichier['tmp_name'], $name);
		if($moved){
			return $name;
		}else{
			return false;
		}
	}

    public function removeFile($filename, $dir = null)
    {
        $dir = $dir? $dir: $this->getFilePath();
        if(file_exists($dir.D_S.$filename)){
           return unlink($dir.D_S.$filename);
        }
        return false;
    }

    public function getVars()
    {
        return get_object_vars($this);
    }

    public static function loadMetadata(Metadata $metadata)
    {
        $builder = new MetadataBuilder($metadata);
    }

    public static function getMetadata()
    {
        return self::$metadata;
    }

}