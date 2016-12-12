<?php
/**
 * Created by PhpStorm.
 * User: Bruno
 * Date: 11/12/16
 * Time: 11:50
 */

namespace Core\Component\DataBase;


class Proxy
{
    const name = '_PX_';

    private $template ='';
    private $placeholders = array();
    private $fqcn = '';

    private $baseTemplate =
    '<?php
namespace <namespace>;

class <proxyShortClassName> extends \<className> implements \<proxyInterface>
{
    <methods>
}';


    public function generateProxy($fileName = null)
    {
        preg_match_all('(<([a-zA-Z]+)>)', $this->baseTemplate, $placeholderMatches);

        $placeholderMatches = array_combine($placeholderMatches[0], $placeholderMatches[1]);
        $placeholders       = array();

        foreach ($placeholderMatches as $placeholder => $name) {
            $placeholders[$placeholder] = isset($this->placeholders[$name])
                ? $this->placeholders[$name]
                : array($this, 'get' . ucfirst($name));
        }

        foreach ($placeholders as &$placeholder) {
            if (is_callable($placeholder)) {
                $placeholder = call_user_func($placeholder, $this);

            }
        }

        $proxyCode = strtr($this->baseTemplate, $placeholders);

        if ( ! $fileName) {
            $proxyClassName = $this->getNamespace() . '\\' . $this->getProxyShortClassName();

            if ( ! class_exists($proxyClassName)) {

                var_dump(substr($proxyCode, 5));
                eval(substr($proxyCode, 5));
            }

            return;
        }

        $parentDirectory = dirname($fileName);

        if ( ! is_dir($parentDirectory) && (false === @mkdir($parentDirectory, 0775, true))) {
            throw new  \Exception('not writable directory: '.$parentDirectory);
        }

        $tmpFileName = $fileName . '.' . uniqid('', true);

        file_put_contents($tmpFileName, $proxyCode);
        @chmod($tmpFileName, 0664);
        rename($tmpFileName, $fileName);
    }

    public function setFqcn($fqcn)
    {
        $this->fqcn = $fqcn;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getProxyShortClassName()
    {
        $class = explode('\\', $this->fqcn);

        return  end($class).'Proxy';
    }

    public function getProxyInterface()
    {
        return  'Core\Component\DataBase\ProxyInterface';
    }

    public function getNameSpace()
    {
        return 'App\Proxy';
    }

    public function getMethods()
    {
        $methods =
'public function setId($id) {
    $this->id = $id;
}

 public function setOid($oid) {
     $this->oid = $oid;
}
';

        return $methods;
    }

    public function getClassName()
    {
        return $this->fqcn;
    }
}